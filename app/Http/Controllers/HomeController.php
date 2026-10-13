<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Rider;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        return view('home.index', [
            'stats' => $this->statistics(),
            'testimonials' => $this->testimonials(),
            'serviceAreas' => $this->serviceAreas(),
        ]);
    }

    /**
     * Homepage stats — DB-driven when data exists, else fallback.
     * Cached 1 hour to avoid hammering DB on every homepage hit.
     */
    private function statistics(): array
    {
        return Cache::remember('homepage.stats', 3600, function () {
            $deliveries = Order::where('status', 'delivered')->count();
            $businesses = User::where('role', 'customer')->count();
            $riders = Rider::where('status', 'verified')->count();

            return [
                [
                    'label' => 'Deliveries Completed',
                    'value' => $deliveries >= 100 ? number_format($deliveries).'+' : '10,000+',
                    'icon' => 'package-check',
                ],
                [
                    'label' => 'Business Customers',
                    'value' => $businesses >= 100 ? number_format($businesses).'+' : '500+',
                    'icon' => 'building-2',
                ],
                [
                    'label' => 'Verified Riders',
                    'value' => $riders >= 50 ? number_format($riders).'+' : '100+',
                    'icon' => 'bike',
                ],
                [
                    'label' => 'Successful Deliveries',
                    'value' => '98%',
                    'icon' => 'check-circle',
                ],
            ];
        });
    }

    /**
     * Testimonials — seeded fallback until STEP 19 builds the real review system.
     */
    private function testimonials(): array
    {
        return [
            [
                'name' => 'Adaeze Okonkwo',
                'role' => 'Fashion Retailer, Lagos',
                'rating' => 5,
                'initial' => 'A',
                'text' => 'Hunnovate has completely transformed how I deliver to my customers. Same-day pickup, live tracking, and professional riders every single time.',
            ],
            [
                'name' => 'Ibrahim Yusuf',
                'role' => 'Pharmacy Owner, Abuja',
                'rating' => 5,
                'initial' => 'I',
                'text' => 'Their motorcycle delivery service is unbeatable for urgent medical supplies. We have cut our delivery delays by 70%.',
            ],
            [
                'name' => 'Chiamaka Eze',
                'role' => 'E-commerce Manager, Port Harcourt',
                'rating' => 5,
                'initial' => 'C',
                'text' => 'We ship 200+ orders a week. Hunnovate handles them all with reliability we have never seen elsewhere. Excellent platform.',
            ],
            [
                'name' => 'Tunde Bakare',
                'role' => 'Logistics Coordinator, Ibadan',
                'rating' => 5,
                'initial' => 'T',
                'text' => 'The truck service for heavy shipments has been a game-changer for our distribution. Transparent pricing, always on time.',
            ],
        ];
    }

    /**
     * Service areas — hardcoded for now, DB-driven in later steps.
     */
    private function serviceAreas(): array
    {
        return [
            ['city' => 'Lagos',          'state' => 'Lagos State',          'status' => 'active'],
            ['city' => 'Abuja',          'state' => 'FCT',                  'status' => 'active'],
            ['city' => 'Port Harcourt',  'state' => 'Rivers State',         'status' => 'active'],
            ['city' => 'Ibadan',         'state' => 'Oyo State',            'status' => 'active'],
            ['city' => 'Kano',           'state' => 'Kano State',           'status' => 'active'],
            ['city' => 'Benin City',     'state' => 'Edo State',            'status' => 'active'],
        ];
    }
}
