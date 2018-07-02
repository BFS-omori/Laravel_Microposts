<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    
    //Favorite登録
    public function store(Request $request, $fid)
    {
        \Auth::user()->favorite($fid);
        return redirect()->back();
    }
    
    //UnFavorte
    public function destroy($fid)
    {
        \Auth::user()->unfavorite($fid);
        return redirect()->back();
    }
    //
}
