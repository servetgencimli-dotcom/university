<?php

namespace App\Http\Controllers;

use App\Models\CampusBuilding;
use Illuminate\Http\Request;

class CampusController extends Controller
{
    public function index(Request $request)
    {
        $category = $request->get('category');
        $query = CampusBuilding::query();

        if ($category && $category !== 'all') {
            $query->where('category', $category);
        }

        $buildings = $query->orderBy('name')->get();
        $allBuildings = CampusBuilding::all();

        return view('pages.campus.map', compact('buildings', 'allBuildings', 'category'));
    }
}
