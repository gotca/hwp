<?php

namespace App\Http\Controllers;

use App\Models\PhotoAlbum;
use Illuminate\Http\Request;

use App\Http\Requests;

class AlbumController extends Controller
{

    public function index()
    {
        $albums = PhotoAlbum::with('cover')
            ->withCount('photos')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('albumlist', compact('albums'));
    }

    public function photos(PhotoAlbum $album)
    {
        // $photos = $album->photos()->paginate(48);
        $game = $album->game()
            ->withCount(['album', 'updates', 'boxStats'])
            ->first();

        return view('album', compact('album', 'photos', 'game'));
    }
}
