<?php

namespace App\Http\Controllers;

use App\Models\ResearchPaper;
use Illuminate\Http\Request;

class ResearchController extends Controller
{
    public function index(Request $request)
    {
        $query = ResearchPaper::with('author.faculty');

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('abstract', 'like', "%{$search}%")
                    ->orWhere('co_authors', 'like', "%{$search}%");
            });
        }

        $papers = $query->orderBy('publication_date', 'desc')->paginate(10)->withQueryString();
        $categories = ResearchPaper::select('category')->distinct()->pluck('category');

        return view('pages.research.index', compact('papers', 'categories'));
    }
}
