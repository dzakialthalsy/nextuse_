<?php

namespace App\Http\Controllers;

use App\Support\ProductCatalog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FilterController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $products = ProductCatalog::filter(
            $request->query('q'),
            $request->query('category'),
            $request->query('condition')
        );

        return response()->json([
            'data' => $products,
            'meta' => [
                'count' => count($products),
            ],
        ]);
    }
}

