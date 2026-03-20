
<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
//  Each controller should have one responsibility.
//  a principle called Single Responsibility 
// — each class does one job. 

class GameController extends Controller
{
    public function index()
    {
        return view('game');
    }
}
