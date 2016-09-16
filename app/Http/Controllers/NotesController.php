<?php

namespace App\Http\Controllers;

use App\Models\Note;
use Illuminate\Http\Request;

use App\Http\Requests;

class NotesController extends Controller
{
    public function note(Note $note)
    {
        return view('note', compact('note'));
    }
}
