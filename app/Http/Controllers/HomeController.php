<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Moving;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Repack;
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
        $title = "amends";
        $state = $req->state;
        switch($state){
            case "slip":
                $no_SJs = Order::all();
                break;
            case "invoice":
                $no_SJs = Invoice::all();
                break;
            case "payment":
                $no_SJs = Payment::all();
                break;
            case "repack":
                $no_SJs = Repack::all();
                break;
            case "moving":
                $no_SJs = Moving::all();
                break;
        }

        return view("amends.amend", ["title" => $title, "state" => $state, "no_SJs" => $no_SJs]);
    }

    public function logout(Request $req)
    {
        $req->session()->flush();
        return redirect()->route("home");
    }
}
