<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ClienteController extends Controller
{
    /**
     * Display the client welcome page
     */
    public function index()
    {
        $user = auth()->user();
        
        return view('client-welcome', [
            'user' => $user,
        ]);
    }
}
