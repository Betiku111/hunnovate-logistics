<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function index(): View
    {
        return view('admin.placeholder', [
            'title' => 'Payments',
            'subtitle' => 'Payment management coming in STEP 17.',
        ]);
    }
}