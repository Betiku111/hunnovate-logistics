<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class SupportController extends Controller
{
    public function index(): View
    {
        return view('admin.placeholder', [
            'title' => 'Support',
            'subtitle' => 'Support tools coming soon.',
        ]);
    }
}
