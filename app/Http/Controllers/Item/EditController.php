<?php

namespace App\Http\Controllers\Item;

use App\Http\Controllers\Controller;
use App\Models\Item;
use Illuminate\Http\Request;

class EditController extends Controller
{
    public function __invoke(Request $request, $id)
    {
        if (!$request->session()->has('organization_id')) {
            return redirect()->route('login');
        }

        $organizationId = $request->session()->get('organization_id');
        $item = Item::where('organization_id', $organizationId)->findOrFail($id);

        return view('edit', compact('item'));
    }
}


