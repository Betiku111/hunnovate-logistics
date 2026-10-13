<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function index(): View
    {
        return view('admin.placeholder', [
            'title' => 'Settings',
            'subtitle' => 'Settings management coming soon.',
        ]);
    }
}
