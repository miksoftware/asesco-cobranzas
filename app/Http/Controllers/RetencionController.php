<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class RetencionController extends Controller
{
    public function index()
    {
        $gestores = User::select('id', 'name')->get();
        return view('retenciones.index', compact('gestores'));
    }
}
