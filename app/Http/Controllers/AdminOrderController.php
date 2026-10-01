<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Controllers\AdminInvoiceSettingsController;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use App\Models\PickupPoint;
use App\Models\State;
use App\Models\Location;
use App\Mail\OrderConfirmation;
use App\Mail\OrderProcessing;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Mail\OrderShipped;
use App\Mail\OrderDelivered;
use App\Mail\OrderCompleted;
use App\Mail\OrderReviewRequest;
use App\Mail\OrderReadyForPickup;
use App\Mail\OrderCancelled;
use App\Mail\OrderRefunded;
use App\Models\OrderReturn;
use App\Models\OrderCancellation;
use App\Mail\OrderCancellationRequestedAdmin;
use App\Mail\OrderCancellationRequestedUser;
use App\Mail\OrderCancellationRejected;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class AdminOrderController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    public function index(Request $request)
    {
        $search        = $request->get('search', '');
        $filterStatus  = $request->get('status', '');
        $filterPayment = $request->get('payment', '');
        $sortBy        = $request->get('sort', 'created_at');
        $sortDir       = $request->get('dir', 'desc');

        // Whitelist sortable columns
        $allowedSorts = ['id', 'created_at', 'total', 'status', 'payment_method'];
        if (!in_array($sortBy, $allowedSorts)) $sortBy = 'created_at';
        if (!in_array($sortDir, ['asc', 'desc'])) $sortDir = 'desc';

        $orders = Order::with(['user', 'items'])
            ->when($search, function ($query) use ($search) {
                $query->where('id', 'like', "%{$search}%")
                    ->orWhere('order_number', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%")
                    ->orWhere('payment_method', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                          ->orWhere('email', 'like', "%{$search}%");
                    });
            })
            ->when($filterStatus,  fn($q) => $q->where('status', $filterStatus))
            ->when($filterPayment, fn($q) => $q->where('payment_method', $filterPayment))
            ->orderBy($sortBy, $sortDir)
            ->paginate(15);

        return view('admin.orders.index', compact(
            'orders', 'search', 'filterStatus', 'filterPayment', 'sortBy', 'sortDir'
        ));
    }

    public function show(Order $order)
    {
        $order->load(['user', 'items', 'return', 'cancellation']);

        return view('admin.orders.show', compact('order'));
    }

    public function edit(Order $order)
    {
        $order->load(['user', 'items']);

        $statuses = ['pending', 'paid', 'processing', 'ready_for_pickup', 'shipped', 'delivered', 'completed', 'cancelled', 'refunded'];

        // Pickup points the admin can reassign this order to.
        $pickupPoints = PickupPoint::with('location')
            ->orderBy('name')
            ->get();

        return view('admin.orders.edit', compact('order', 'statuses', 'pickupPoints'));
    }

    public function update(Request $request, Order $order)
    {
        $request->validate([
            'status'          => 'required|in:pending,paid,processing,ready_for_pickup,shipped,delivered,completed,cancelled,refunded',
            'payment_method'  => 'nullable|string|max:255',
            'pickup_point_id' => 'nullable|integer|exists:pickup_points,id',
        ]);

        $oldStatus = $order->status;
        $newStatus = $request->status;

        $payload = [
            'status'         => $newStatus,
            'payment_method' => $request->payment_method ?? $order->payment_method,
        ];

        // Pickup point reassignment — snapshot the chosen point onto the order.
        if ($request->filled('pickup_point_id') && (int) $request->pickup_point_id !== (int) $order->pickup_point_id) {
            $point = PickupPoint::with('location')->find($request->pickup_point_id);
            if ($point) {
                $payload['pickup_point_id']      = $point->id;
                $payload['pickup_point_name']    = $point->name;
                $payload['pickup_point_address'] = $point->address;
                if ($point->location) {
                    $payload['pickup_location']    = $point->location->name;
                    $payload['pickup_location_id'] = $point->location->id;
                }
            }
        }

        $order->update($payload);

        // Only send email when status actually changes
        if ($newStatus !== $oldStatus) {
            $recipient = $order->user?->email ?? $order->customer_email;

            if ($recipient) {
                try {
                    match ($newStatus) {
                        'processing'       => Mail::to($recipient)->send(new OrderProcessing($order)),
                        'shipped'          => Mail::to($recipient)->send(new OrderShipped($order)),
                        'ready_for_pickup' => Mail::to($recipient)->send(new OrderReadyForPickup($order)),
                        'delivered'        => Mail::to($recipient)->send(new OrderDelivered($order)),
                        'completed'        => Mail::to($recipient)->send(new OrderCompleted($order)),
                        'cancelled'        => Mail::to($recipient)->send(new OrderCancelled($order)),
                        'refunded'         => Mail::to($recipient)->send(new OrderRefunded($order)),
                        default            => null,
                    };
                } catch (\Exception $e) {
                    Log::error('Failed to send order status email for order #' . $order->order_number . ' (status: ' . $newStatus . '): ' . $e->getMessage());
                }

                // Review-request email — sent right after the completion email, only on completion.
                if ($newStatus === 'completed') {
                    try {
                        Mail::to($recipient)->send(new OrderReviewRequest($order));
                    } catch (\Exception $e) {
                        Log::error('Failed to send review-request email for order #' . $order->order_number . ': ' . $e->getMessage());
                    }
                }
            } else {
                Log::warning('No recipient email for order #' . $order->order_number . ' — status email not sent.');
            }
        }

        return redirect()
            ->route('admin.orders.index')
            ->with('success', 'Order ' . $order->order_number . ' updated successfully.');
    }

    public function destroy(Order $order)
    {
        $order->items()->delete();
        $order->delete();

        return redirect()
            ->route('admin.orders.index')
            ->with('success', 'Order ' . $order->order_number . ' deleted successfully.');
    }

    // ── Create order for a user ───────────────────────────────────────────────

    public function create()
    {
        $pickupPoints = PickupPoint::with('location')->orderBy('name')->get();
        $states       = State::where('is_active', true)->orderBy('name')->get();

        return view('admin.orders.create', compact('pickupPoints', 'states'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id'            => 'nullable|exists:users,id',
            'customer_email'     => 'required_without:user_id|nullable|email|max:255',
            'payment_method'     => 'required|string|in:paystack,stripe,cash,bank_transfer,pos,other',
            'payment_reference'  => 'nullable|string|max:255',
            'fulfillment_method' => 'required|in:pickup,delivery',
            'pickup_point_id'    => 'required_if:fulfillment_method,pickup|nullable|exists:pickup_points,id',
            'delivery_state_id'  => 'required_if:fulfillment_method,delivery|nullable|exists:states,id',
            'delivery_location_id' => 'nullable|exists:locations,id',
            'shipping_address'   => 'nullable|string|max:500',
            'shipping_cost'      => 'nullable|numeric|min:0',
            'coupon_discount_ngn' => 'nullable|numeric|min:0',
            'notes'              => 'nullable|string|max:1000',
            'items'              => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity'   => 'required|integer|min:1',
            'items.*.price'      => 'required|numeric|min:0',
            'items.*.installation_option'    => 'nullable|string|max:255',
            'items.*.installation_extra_ngn' => 'nullable|numeric|min:0',
        ]);

        $items       = $request->input('items');
        $subtotal    = collect($items)->sum(fn ($i) => $i['price'] * $i['quantity']);
        $shipping    = (float) ($request->shipping_cost ?? 0);
        $discount    = (float) ($request->coupon_discount_ngn ?? 0);
        $total       = $subtotal + $shipping - $discount;

        $reference = $request->payment_reference;
        if (empty($reference) && in_array($request->payment_method, ['paystack', 'stripe'])) {
            $reference = 'ADM-' . strtoupper(Str::random(10));
        }

        $pickupPoint = null;
        if ($request->fulfillment_method === 'pickup' && $request->pickup_point_id) {
            $pickupPoint = PickupPoint::with('location')->find($request->pickup_point_id);
        }

        $deliveryState    = null;
        $deliveryLocation = null;
        if ($request->fulfillment_method === 'delivery') {
            $deliveryState    = State::find($request->delivery_state_id);
            $deliveryLocation = Location::find($request->delivery_location_id);
        }

        $user = $request->user_id ? User::find($request->user_id) : null;

        $order = Order::create([
            'user_id'                => $user?->id,
            'status'                 => 'paid',
            'total'                  => $total,
            'total_usd'              => 0,
            'payment_method'         => $request->payment_method,
            'payment_id'             => $reference,
            'reference'              => $reference,
            'fulfillment_method'     => $request->fulfillment_method,
            'pickup_point_id'        => $pickupPoint?->id,
            'pickup_point_name'      => $pickupPoint?->name,
            'pickup_point_address'   => $pickupPoint?->address,
            'pickup_location'        => $pickupPoint?->location?->name,
            'pickup_location_id'     => $pickupPoint?->location?->id,
            'delivery_state_id'      => $deliveryState?->id,
            'delivery_state_name'    => $deliveryState?->name,
            'delivery_location_id'   => $deliveryLocation?->id,
            'delivery_location_name' => $deliveryLocation?->name,
            'shipping_address'       => $request->fulfillment_method === 'delivery' ? $request->shipping_address : null,
            'shipping_cost'          => $shipping,
            'customer_email'         => $user?->email ?? $request->customer_email,
            'coupon_discount_ngn'    => $discount,
        ]);

        foreach ($items as $item) {
            $product = Product::find($item['product_id']);
            $order->items()->create([
                'product_id'             => $item['product_id'],
                'name'                   => $product?->name ?? 'Unknown product',
                'price'                  => $item['price'], // effective price incl. any installation add-on
                'quantity'               => $item['quantity'],
                'sku'                    => $product?->sku ?? null,
                'installation_option'    => $item['installation_option']    ?? null,
                'installation_extra_ngn' => $item['installation_extra_ngn'] ?? 0,
            ]);
        }

        $recipient = $user?->email ?? $request->customer_email;
        if ($recipient) {
            try {
                Mail::to($recipient)->send(new OrderConfirmation($order));
            } catch (\Exception $e) {
                Log::error('Admin-created order confirmation email failed: ' . $e->getMessage());
            }
        }

        return redirect()
            ->route('admin.orders.show', $order)
            ->with('success', 'Order ' . $order->order_number . ' created successfully.');
    }

    // ── Invoice ───────────────────────────────────────────────────────────────

    public function invoice(Order $order)
    {
        $order->load('items', 'user');
        $downloadUrl = route('admin.orders.invoice.download', $order);
        $tpl = AdminInvoiceSettingsController::templateVars($order->fulfillment_method);
        return view('sims.invoice', array_merge(compact('order', 'downloadUrl'), $tpl));
    }

    public function downloadInvoice(Order $order)
    {
        $order->load('items', 'user');
        $tpl = AdminInvoiceSettingsController::templateVars($order->fulfillment_method);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('sims.invoice-pdf', array_merge(compact('order'), $tpl))
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'dpi'                    => 96,
                'defaultFont'            => 'DejaVu Sans',
                'isHtml5ParserEnabled'   => true,
                'isRemoteEnabled'        => true,
                'chroot'                 => public_path(),
                'enable_font_subsetting' => true,
            ]);

        return $pdf->download('invoice-' . $order->order_number . '.pdf');
    }

    // ── AJAX product & user search ────────────────────────────────────────────

    public function searchProducts(Request $request)
    {
        $q = $request->get('q', '');

        $products = Product::with('images')
            ->where(function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                      ->orWhere('sku', 'like', "%{$q}%");
            })
            ->where('is_active', true)
            ->limit(15)
            ->get()
            ->map(fn ($p) => [
                'id'    => $p->id,
                'name'  => $p->name,
                'sku'   => $p->sku,
                'price' => $p->price,
                'image' => $p->images->first()?->image_url
                    ? Storage::disk('public')->url($p->images->first()->image_url)
                    : null,
                // Installation add-ons, same shape the storefront uses: [{label, price}, …]
                'installation_options' => collect($p->installation_options ?? [])
                    ->map(fn ($o) => [
                        'label' => $o['label'] ?? '',
                        'price' => (float) ($o['price'] ?? 0),
                    ])
                    ->filter(fn ($o) => $o['label'] !== '')
                    ->values(),
            ]);

        return response()->json($products);
    }

    public function searchUsers(Request $request)
    {
        $q = $request->get('q', '');

        $users = User::where('name', 'like', "%{$q}%")
            ->orWhere('email', 'like', "%{$q}%")
            ->orWhere('phone_no', 'like', "%{$q}%")
            ->limit(10)
            ->get(['id', 'name', 'email', 'phone_no']);

        return response()->json($users);
    }

    // ── Returns index ─────────────────────────────────────────────────────────
    public function returnsIndex(Request $request)
    {
        $search = trim((string) $request->query('search', ''));

        $returns = OrderReturn::with(['order', 'user'])
            ->when($search !== '', function ($q) use ($search) {
                $q->where('reason', 'like', "%{$search}%")
                  ->orWhereHas('order', fn ($o) => $o->where('order_number', 'like', "%{$search}%"))
                  ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$search}%")
                                                    ->orWhere('email', 'like', "%{$search}%"));
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.orders.returns', compact('returns', 'search'));
    }

    // ── Admin logs a return on behalf of a customer (from the order page) ────────
    public function storeReturn(Request $request, Order $order)
    {
        $request->validate(['reason' => 'required|string|min:10|max:1000']);

        if (! $this->createReturnFor($order, $request->reason)) {
            return back()->with('error', 'This order already has a return request.');
        }

        return back()->with('success', 'Return logged for order ' . $order->order_number . '. Use "Update status" to approve or refund it.');
    }

    public function storeCancellation(Request $request, Order $order)
    {
        $request->validate(['reason' => 'required|string|min:10|max:1000']);

        if (! $this->createCancellationFor($order, $request->reason)) {
            return back()->with('error', 'This order already has a cancellation request.');
        }

        return back()->with('success', 'Cancellation logged for order ' . $order->order_number . '. Use "Update status" to approve or refund it.');
    }

    // ── Standalone create (from the Returns / Cancellations index) ──────────────
    public function createReturn()
    {
        return view('admin.orders.request-create', [
            'type'       => 'return',
            'title'      => 'New Return',
            'storeRoute' => route('admin.returns.store'),
            'backRoute'  => route('admin.returns.index'),
        ]);
    }

    public function storeReturnFromForm(Request $request)
    {
        $data  = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'reason'   => 'required|string|min:10|max:1000',
        ]);
        $order = Order::findOrFail($data['order_id']);

        if (! $this->createReturnFor($order, $data['reason'])) {
            return back()->withInput()->with('error', 'Order ' . $order->order_number . ' already has a return request.');
        }

        return redirect()->route('admin.returns.index')
            ->with('success', 'Return logged for order ' . $order->order_number . '.');
    }

    public function createCancellation()
    {
        return view('admin.orders.request-create', [
            'type'       => 'cancellation',
            'title'      => 'New Cancellation',
            'storeRoute' => route('admin.cancellations.store'),
            'backRoute'  => route('admin.cancellations.index'),
        ]);
    }

    public function storeCancellationFromForm(Request $request)
    {
        $data  = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'reason'   => 'required|string|min:10|max:1000',
        ]);
        $order = Order::findOrFail($data['order_id']);

        if (! $this->createCancellationFor($order, $data['reason'])) {
            return back()->withInput()->with('error', 'Order ' . $order->order_number . ' already has a cancellation request.');
        }

        return redirect()->route('admin.cancellations.index')
            ->with('success', 'Cancellation logged for order ' . $order->order_number . '.');
    }

    private function createReturnFor(Order $order, string $reason): bool
    {
        if ($order->return) return false;
        OrderReturn::create([
            'order_id' => $order->id, 'user_id' => $order->user_id,
            'reason'   => $reason,    'status'  => 'pending',
        ]);
        return true;
    }

    private function createCancellationFor(Order $order, string $reason): bool
    {
        if ($order->cancellation) return false;
        OrderCancellation::create([
            'order_id' => $order->id, 'user_id' => $order->user_id,
            'reason'   => $reason,    'status'  => 'pending',
        ]);
        return true;
    }

    // ── Order search (for the standalone return/cancellation forms) ─────────────
    public function searchOrders(Request $request)
    {
        $q = trim($request->get('q', ''));

        $orders = Order::with('user')
            ->when($q !== '', function ($query) use ($q) {
                $query->where('order_number', 'like', "%{$q}%")
                      ->orWhere('customer_email', 'like', "%{$q}%")
                      ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$q}%")
                                                        ->orWhere('email', 'like', "%{$q}%"));
            })
            ->latest()
            ->limit(15)
            ->get()
            ->map(fn ($o) => [
                'id'           => $o->id,
                'order_number' => $o->order_number,
                'customer'     => $o->user?->name ?? $o->customer_email ?? 'Guest',
                'total'        => $o->total,
                'status'       => $o->status,
            ]);

        return response()->json($orders);
    }

    // ── Review a return ─────────────────────────────────────────────────────────
    public function reviewReturn(Request $request, OrderReturn $return)
    {
        $request->validate([
            'status'      => 'required|in:approved,rejected,refunded',
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        $return->update([
            'status'      => $request->status,
            'admin_notes' => $request->admin_notes,
            'reviewed_at' => now(),
        ]);

        $order = $return->order;
        $refundMessage = '';

        // ── On approval/refund, issue the refund back to the customer ─────────
        if (in_array($request->status, ['approved', 'refunded']) && $order) {

            if (!empty($return->refund_id)) {
                // Already refunded earlier — keep statuses aligned, don't double-refund.
                $order->update(['status' => 'refunded']);
                $refundMessage = ' (refund was already issued)';
            } else {
                $result = $this->refundOrder($order);

                if ($result['handled'] && $result['success']) {
                    $return->update([
                        'status'        => 'refunded',
                        'refund_id'     => $result['refund_id'],
                        'refund_status' => $result['refund_status'],
                        'refunded_at'   => now(),
                    ]);
                    $order->update(['status' => 'refunded']);
                    $refundMessage = ' Refund issued to the customer — it will reflect in 5–10 business days.';

                    try {
                        $recipient = $order->user?->email ?? $order->customer_email;
                        if ($recipient) {
                            Mail::to($recipient)->send(new OrderRefunded($order));
                        }
                    } catch (\Exception $e) {
                        Log::error('Refund email failed for order #' . $order->order_number . ': ' . $e->getMessage());
                    }
                } elseif ($result['handled'] && !$result['success']) {
                    // Gateway call failed — mark refunded but flag for manual handling.
                    $order->update(['status' => 'refunded']);
                    $refundMessage = ' Automatic refund failed — please process the refund manually. (' . $result['message'] . ')';
                } else {
                    // No supported gateway — manual refund required.
                    $order->update(['status' => 'refunded']);
                    $refundMessage = ' This order can\'t be auto-refunded (' . $result['message'] . ') — please process the refund manually.';
                }
            }
        }

        return back()->with('success', 'Return request ' . $request->status . '.' . $refundMessage);
    }

    // ── Cancellations index ───────────────────────────────────────────────────
    public function cancellationsIndex(Request $request)
    {
        $search = trim((string) $request->query('search', ''));

        $cancellations = OrderCancellation::with(['order', 'user'])
            ->when($search !== '', function ($q) use ($search) {
                $q->where('reason', 'like', "%{$search}%")
                  ->orWhereHas('order', fn ($o) => $o->where('order_number', 'like', "%{$search}%"))
                  ->orWhereHas('user', fn ($u) => $u->where('name', 'like', "%{$search}%")
                                                    ->orWhere('email', 'like', "%{$search}%"));
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.orders.cancellations', compact('cancellations', 'search'));
    }

    // ── Review a cancellation ─────────────────────────────────────────────────
    // public function reviewCancellation(Request $request, OrderCancellation $cancellation)
    // {
    //     $request->validate([
    //         'status'      => 'required|in:approved,rejected,refunded',
    //         'admin_notes' => 'nullable|string|max:1000',
    //     ]);

    //     $cancellation->update([
    //         'status'      => $request->status,
    //         'admin_notes' => $request->admin_notes,
    //     ]);

    //     $order = $cancellation->order;
    //     $refundMessage = '';

    //     if ($order) {
    //         if ($request->status === 'refunded') {
    //             if (empty($cancellation->refund_id)) {
    //                 $result = $this->refundOrder($order);
    //                 if ($result['handled'] && $result['success']) {
    //                     $cancellation->update([
    //                         'refund_id'     => $result['refund_id'],
    //                         'refund_status' => $result['refund_status'],
    //                         'refunded_at'   => now(),
    //                     ]);
    //                     $refundMessage = ' Refund issued to the customer.';
    //                 } elseif ($result['handled']) {
    //                     $refundMessage = ' Automatic refund failed — please process manually. (' . $result['message'] . ')';
    //                 } else {
    //                     $refundMessage = ' This order can\'t be auto-refunded (' . $result['message'] . ') — please process manually.';
    //                 }
    //             }
    //             $order->update(['status' => 'refunded']);
    //         } elseif ($request->status === 'approved') {
    //             $order->update(['status' => 'cancelled']);
    //         } elseif ($request->status === 'rejected') {
    //             if ($order->status === 'cancelled') {
    //                 $order->update(['status' => 'paid']);
    //             }
    //         }
    //     }

    //     return back()->with('success', 'Cancellation request ' . $request->status . '.' . $refundMessage);
    // }


    // ── Review a cancellation ─────────────────────────────────────────────────
    public function reviewCancellation(Request $request, OrderCancellation $cancellation)
    {
        $request->validate([
            'status'      => 'required|in:approved,rejected,refunded',
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        $cancellation->update([
            'status'      => $request->status,
            'admin_notes' => $request->admin_notes,
        ]);

        $order = $cancellation->order;
        $refundMessage = '';

        if ($order) {
            if ($request->status === 'refunded') {
                if (empty($cancellation->refund_id)) {
                    $result = $this->refundOrder($order);
                    if ($result['handled'] && $result['success']) {
                        $cancellation->update([
                            'refund_id'     => $result['refund_id'],
                            'refund_status' => $result['refund_status'],
                            'refunded_at'   => now(),
                        ]);
                        $refundMessage = ' Refund issued to the customer.';
                    } elseif ($result['handled']) {
                        $refundMessage = ' Automatic refund failed — please process manually. (' . $result['message'] . ')';
                    } else {
                        $refundMessage = ' This order can\'t be auto-refunded (' . $result['message'] . ') — please process manually.';
                    }
                }
                $order->update(['status' => 'refunded']);
            } elseif ($request->status === 'approved') {
                $order->update(['status' => 'cancelled']);
            } elseif ($request->status === 'rejected') {
                if ($order->status === 'cancelled') {
                    $order->update(['status' => 'paid']);
                }
            }

            // Make sure the cancellation note is fresh on the order for the email template.
            $order->load('cancellation');

            // ── Notify the customer of the outcome ────────────────────────────
            $recipient = $order->user?->email ?? $order->customer_email;
            if ($recipient) {
                try {
                    match ($request->status) {
                        'refunded' => Mail::to($recipient)->send(new OrderRefunded($order)),
                        'approved' => Mail::to($recipient)->send(new OrderCancelled($order)),
                        'rejected' => Mail::to($recipient)->send(new OrderCancellationRejected($order)),
                        default    => null,
                    };
                } catch (\Exception $e) {
                    Log::error('Failed to send cancellation-review email for order #' . $order->order_number . ' (status: ' . $request->status . '): ' . $e->getMessage());
                }
            } else {
                Log::warning('No recipient email for order #' . $order->order_number . ' — cancellation-review email not sent.');
            }
        }

        return back()->with('success', 'Cancellation request ' . $request->status . '.' . $refundMessage);
    }
    // ══════════════════════════════════════════════════════════════════════════
    //  Refund helpers
    // ══════════════════════════════════════════════════════════════════════════

    /**
     * Route the refund to the correct gateway based on payment method.
     * Returns a normalised result:
     *   handled        => was this a gateway we can auto-refund?
     *   success        => did the refund call succeed?
     *   refund_id      => gateway refund id (on success)
     *   refund_status  => gateway refund status (on success)
     *   message        => error / explanation (on failure or unhandled)
     */
    private function refundOrder(Order $order): array
    {
        $method = strtolower($order->payment_method ?? '');

        if ($method === 'paystack') {
            return $this->refundOrderViaPaystack($order);
        }

        if ($method === 'stripe') {
            return $this->refundOrderViaStripe($order);
        }

        return [
            'handled'       => false,
            'success'       => false,
            'refund_id'     => null,
            'refund_status' => null,
            'message'       => 'unsupported payment method: ' . ($order->payment_method ?? 'none'),
        ];
    }

    private function refundOrderViaPaystack(Order $order): array
    {
        if (empty($order->reference)) {
            return ['handled' => true, 'success' => false, 'refund_id' => null, 'refund_status' => null, 'message' => 'No payment reference on this order.'];
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . config('services.paystack.secret'),
                'Content-Type'  => 'application/json',
            ])->post('https://api.paystack.co/refund', [
                'transaction'   => $order->reference,
                'amount'        => (int) ($order->total * 100), // NGN -> kobo
                'currency'      => 'NGN',
                'customer_note' => 'Refund for order #' . $order->order_number,
                'merchant_note' => 'Refund issued by admin',
            ]);

            $data = $response->json();

            if ($response->successful() && ($data['status'] ?? false)) {
                Log::info('Paystack refund issued for order #' . $order->id, ['refund_id' => $data['data']['id'] ?? null]);
                return [
                    'handled'       => true,
                    'success'       => true,
                    'refund_id'     => $data['data']['id']     ?? null,
                    'refund_status' => $data['data']['status'] ?? 'pending',
                    'message'       => '',
                ];
            }

            Log::error('Paystack refund failed for order #' . $order->id, ['response' => $data]);
            return ['handled' => true, 'success' => false, 'refund_id' => null, 'refund_status' => null, 'message' => $data['message'] ?? 'Refund request was declined.'];

        } catch (\Exception $e) {
            Log::error('Paystack refund exception for order #' . $order->id . ': ' . $e->getMessage());
            return ['handled' => true, 'success' => false, 'refund_id' => null, 'refund_status' => null, 'message' => $e->getMessage()];
        }
    }

    /**
     * Refund a Stripe order via the REST API (no SDK dependency).
     * Uses the stored payment intent id (saved as payment_id / reference at checkout).
     * Stripe refunds to the original card and infers the amount from the intent,
     * but we pass it explicitly to be safe. Amount is in the smallest USD unit (cents).
     */
    private function refundOrderViaStripe(Order $order): array
    {
        $paymentIntent = $order->payment_id ?: $order->reference;

        if (empty($paymentIntent)) {
            return ['handled' => true, 'success' => false, 'refund_id' => null, 'refund_status' => null, 'message' => 'No Stripe payment intent on this order.'];
        }

        // Stripe charged in USD (see checkout). Refund the same USD amount in cents.
        $usdCents = (int) round(((float) $order->total_usd) * 100);

        try {
            $payload = ['payment_intent' => $paymentIntent];
            // Only send an amount if we have a valid USD figure; otherwise let
            // Stripe refund the full original charge automatically.
            if ($usdCents > 0) {
                $payload['amount'] = $usdCents;
            }

            $response = Http::asForm()
                ->withToken(config('services.stripe.secret'))
                ->post('https://api.stripe.com/v1/refunds', $payload);

            $data = $response->json();

            if ($response->successful() && !empty($data['id'])) {
                Log::info('Stripe refund issued for order #' . $order->id, ['refund_id' => $data['id']]);
                return [
                    'handled'       => true,
                    'success'       => true,
                    'refund_id'     => $data['id'],
                    'refund_status' => $data['status'] ?? 'pending',
                    'message'       => '',
                ];
            }

            $err = $data['error']['message'] ?? 'Refund request was declined.';
            Log::error('Stripe refund failed for order #' . $order->id, ['response' => $data]);
            return ['handled' => true, 'success' => false, 'refund_id' => null, 'refund_status' => null, 'message' => $err];

        } catch (\Exception $e) {
            Log::error('Stripe refund exception for order #' . $order->id . ': ' . $e->getMessage());
            return ['handled' => true, 'success' => false, 'refund_id' => null, 'refund_status' => null, 'message' => $e->getMessage()];
        }
    }
}