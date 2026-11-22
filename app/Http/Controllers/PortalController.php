<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class PortalController extends Controller
{
    /**
     * Beranda mandiri untuk tenant.
     */
    public function index(Request $request): View
    {
        return view('portal.index', [
            'user' => $request->user(),
        ]);
    }
}
