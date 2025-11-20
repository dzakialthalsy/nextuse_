<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SyaratKetentuanController extends Controller
{
    public function index()
    {
        return view('syarat-ketentuan');
    }
}
