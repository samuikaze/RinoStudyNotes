<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CharacterController extends Controller
{
    public function test()
    {
        return response()->json('hello world!', 200);
    }
}
