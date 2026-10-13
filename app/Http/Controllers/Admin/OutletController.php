<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Outlet;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OutletController extends Controller
{
    public function index(Request $request): View
    {
        $query = Outlet::query()->latest();

        if ($search = trim((string) $request->input('q'))) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%");
            });
        }

        return view('admin.outlets.index', [
            'outlets' => $query->paginate(20)->withQueryString(),
            'search'  => $search ?? '',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'    => ['required', 'string', 'max:120'],
            'code'    => ['required', 'string', 'max:20', 'unique:outlets,code'],
            'phone'   => ['nullable', 'string', 'max:20'],
            'email'   => ['nullable', 'email', 'max:120'],
            'address' => ['required', 'string', 'max:255'],
            'city'    => ['required', 'string', 'max:80'],
            'state'   => ['required', 'string', 'max:80'],
        ]);

        Outlet::create($data);

        return back()->with('success', 'Outlet created.');
    }

    public function toggleActive(Outlet $outlet): RedirectResponse
    {
        $outlet->update(['is_active' => ! $outlet->is_active]);

        return back()->with('success', 'Outlet status updated.');
    }
}