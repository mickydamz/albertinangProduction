<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Faq;

class FaqSeeder extends Seeder
{
    public function run()
    {
        $faqs = [
            ['category' => 'orders',   'sort_order' => 1,  'question' => 'How do I place an order?',                'answer' => "Placing an order is simple:\n1. Browse and select your desired products\n2. Add items to your cart by clicking \"Add to Cart\"\n3. Review your cart and proceed to checkout\n4. Provide your delivery and contact information\n5. Choose your preferred payment method and confirm\n\nYou'll receive an order confirmation once your order is processed."],
            ['category' => 'orders',   'sort_order' => 2,  'question' => 'What payment methods do you accept?',      'answer' => "We accept a variety of secure payment methods:\n\nBank Transfer: Direct bank transfers\nCard Payments: Visa and MasterCard via secure gateway\nCash on Delivery: Available for select locations\nPayment on Collection: Pay in-store when you collect\n\nAll transactions are processed through secure, encrypted channels."],
            ['category' => 'orders',   'sort_order' => 3,  'question' => 'Can I change or cancel my order?',          'answer' => "Before dispatch: Orders can be cancelled or modified — contact us immediately.\nAfter dispatch: Orders cannot be cancelled but may be returned upon delivery.\n\nCall us on 08064066170 or email Info@Albertinang.com with your order number for fastest resolution."],
            ['category' => 'orders',   'sort_order' => 4,  'question' => 'Do products come with a warranty?',         'answer' => "Yes — all our products come with manufacturer warranties:\n\nAir Conditioners: 1–2 year warranty\nTelevisions: 1 year warranty\nWashing Machines & Fridges: 1–2 year warranty\nGenerators & Inverters: 6 months – 1 year\n\nWarranty terms are stated on each product page. For warranty claims, contact us with your order details."],
            ['category' => 'shipping', 'sort_order' => 5,  'question' => 'How long does delivery take?',              'answer' => "Delivery times depend on your location:\n\nEnugu (same day): Orders placed before 12 PM\nSouth-East Nigeria: 1–3 business days\nLagos & other states: 3–5 business days\nRemote locations: 5–7 business days\n\nYou'll receive tracking information once your order is dispatched."],
            ['category' => 'shipping', 'sort_order' => 6,  'question' => 'What are your delivery/shipping costs?',    'answer' => "Shipping costs are calculated at checkout based on your location and order size.\n\nFree delivery on orders over ₦350,000\nEnugu local delivery: from ₦1,500\nNationwide: from ₦3,500 depending on destination\n\nExact costs are shown at checkout before you confirm your order."],
            ['category' => 'shipping', 'sort_order' => 7,  'question' => 'How can I track my order?',                 'answer' => "You can track your order by:\n1. Logging into your account dashboard\n2. Viewing your order status under \"My Orders\"\n3. Contacting us directly with your order number\n\nOur team is also available on 08064066170 for live order updates."],
            ['category' => 'returns',  'sort_order' => 8,  'question' => 'What is your return policy?',               'answer' => "We offer a straightforward return process:\n\nItems must be unused and in original packaging\nReturn requests must be initiated within 7 days of delivery\nDefective or damaged products are eligible for free replacement\nItems must be accompanied by proof of purchase\n\nContact us at Info@Albertinang.com or call 08064066170 to initiate a return."],
        ];

        foreach ($faqs as $faq) {
            Faq::firstOrCreate(
                ['question' => $faq['question']],
                array_merge($faq, ['is_active' => true])
            );
        }
    }
}
