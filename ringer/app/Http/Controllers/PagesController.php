<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Carbon;

class PagesController extends Controller
{
    public function app()
    {
        $user = User::find(auth()->user()->id);
        $userJSON = json_encode($user);
        $userJSON = str_replace('\'', '\\\'', $userJSON);
        return view('app', compact('userJSON'));
    }
}
