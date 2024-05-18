<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Like;

use App\Models\Decision;
class LikeController extends Controller
{
    public function index()
    {
        $likes = Like::all();
        return response()->json(['likes' => $likes], 200);
    }
    


    
    
    
}
