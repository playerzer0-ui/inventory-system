<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class HomeController extends Controller
{
    public function index()
    {
        return view('home', ["title" => "home"]);
    }

    public function login(Request $req)
    {
        $email = $req->input("email");
        $password = $req->input("password");
        $user = User::where("email", $email)->first();

        if($user && Hash::check($password, $user->password)){
            $req->session()->put("email", $user->email);
            $req->session()->put("userType", $user->userType);
            return redirect()->route("dashboard");
        }
        else{
            return redirect()->route("home");
        }
    }

    public function amends(Request $req)
    {
        
    }

    public function logout(Request $req)
    {
        $req->session()->flush();
        return redirect()->route("home");
    }
}
