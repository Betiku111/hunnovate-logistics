<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(): View
    {
        return view('admin.placeholder', [
            'title' => 'Reports',
            'subtitle' => 'Reporting tools coming soon.',
        ]);
    }
}
