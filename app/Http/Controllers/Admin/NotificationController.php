<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(): View
    {
        return view('admin.placeholder', [
            'title' => 'Notifications',
            'subtitle' => 'Notification tools coming soon.',
        ]);
    }
}
