<?php

namespace App\Http\Controllers;

use App\Models\NewsArticle;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    public function index(Request $request)
    {
        $query = NewsArticle::query();

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $news = $query->orderBy('published_at', 'desc')->paginate(9)->withQueryString();

        return view('pages.news.index', compact('news'));
    }

    public function show($slug)
    {
        $article = NewsArticle::where('slug', $slug)->firstOrFail();
        $article->increment('views_count');
        $recentNews = NewsArticle::where('id', '!=', $article->id)->orderBy('published_at', 'desc')->take(4)->get();

        return view('pages.news.show', compact('article', 'recentNews'));
    }
}
