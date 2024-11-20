<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Asset;

class AssetController extends Controller
{
    /**
     * List all assets grouped by category.
     */
    public function listAssets()
    {
        $assets = Asset::all();

        return response()->json([
            'message' => 'Assets fetched successfully',
            'data' => $assets,
        ]);
    }

    /**
     * List featured assets.
     */
    public function featuredAssets()
    {
        $featuredAssets = Asset::inRandomOrder()->limit(6)->get();

        return response()->json([
            'message' => 'Featured assets fetched successfully',
            'data' => $featuredAssets,
        ]);
    }
}
