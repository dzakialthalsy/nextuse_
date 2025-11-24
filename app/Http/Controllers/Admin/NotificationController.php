<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $organizationId = $request->session()->get('organization_id');
        $isAdmin = (bool) $request->session()->get('is_admin');

        if (! $organizationId || ! $isAdmin) {
            return response()->json([]);
        }

        return response()->json([]);
    }
}