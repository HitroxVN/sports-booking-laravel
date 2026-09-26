<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\NewsArticle;
use Illuminate\View\View;

class NewsArticleController extends Controller
{
    public function index(): View
    {
        $articles = NewsArticle::query()
            ->orderByDesc('published_at')
            ->paginate(12);

        return view('customer.news.index', compact('articles'));
    }
}
