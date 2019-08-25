<?php

namespace App\Http\Controllers;

use App\Link;
use App\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BrowseController extends Controller
{
    public function index(Request $request)
    {
        $links = Link::latest()
            ->withPrivate(auth()->check())
            ->with('tags')
            ->paginate(25);

        return view('home')->with([
            'page_title' => $request->input('page', 1) > 1 ? 'Page n°' . $request->input('page', 1) : 'Accueil',
            'links' => $links,
        ]);
    }

    public function link(Request $request, string $hash)
    {
        $link = Link::withPrivate(auth()->check())
                ->with('tags')
                ->hashIdIs($hash)
                ->firstOrFail();

        return view('link')->with([
            'page_title' => sprintf('%s - #%s', $link->title, $link->hash_id),
            'link' => $link,
        ]);
    }

    public function tag(Request $request, string $tag)
    {
        $tag = Tag::named($tag)->firstOrFail();

        $links = Link::latest()
            ->withAllTags($tag)
            ->withPrivate(auth()->check())
            ->with('tags')
            ->paginate(25);

        abort_if($links->isEmpty(), 404);

        return view('tag')->with([
            'page_title' => sprintf('Taggé %s - Page n°%d', $tag->name, $request->input('page', 1)),
            'tag' => $tag,
            'links' => $links,
        ]);
    }
}