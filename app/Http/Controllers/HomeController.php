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
            return redirect()->route("home")->with("msg", "invalid credentials");
        }
    }

    public function amends(Request $req)
    {
        $title = "amends";
        $state = $req->state;
        switch($state){
            case "slip":
                $no_SJs = Order::where("nomor_surat_jalan", "!=", "-")->get();
                break;
            case "invoice":
                $no_SJs = Invoice::all();
                break;
            case "payment":
                $no_SJs = Payment::all();
                break;
            case "repack":
                $no_SJs = Repack::where("no_repack", "!=", "-")->get()->map(function ($item) {
                    $item->nomor_surat_jalan = $item->no_repack;
                    unset($item->no_repack);
                    return $item;
                });                
                break;
            case "moving":
                $no_SJs = Moving::where("no_moving", "!=", "-")->get()->map(function ($item) {
                    $item->nomor_surat_jalan = $item->no_moving;
                    unset($item->no_moving);
                    return $item;
                });
                break;
        }

        return view("amends.amend", ["title" => $title, "state" => $state, "no_SJs" => $no_SJs]);
    }

    public function amend_update(Request $req)
    {
        $state = $req->state;
        $no_sj = $req->code;
        $mode = $req->mode;
        $payment_id = $req->payment_id;

        switch($state){
            case "slip":
                return redirect()->route("amend_slip", ["no_sj" => $no_sj]);
                break;
            case "invoice":
                return redirect()->route("amend_invoice", ["no_sj" => $no_sj]);
                break;
            case "payment":
                return redirect()->route("amend_payment", ["no_sj" => $no_sj, "payment_id" => $payment_id]);
                break;
            case "repack":
                return redirect()->route("amend_repack", ["no_repack" => $no_sj]);
                break;
            case "moving":
                return redirect()->route("amend_moving", ["no_moving" => $no_sj]);
                break;
            case "purchase":
                return redirect()->route("amend_purchase", ["no_PO" => $no_sj, "mode" => $mode]);
                break;
        }
    }

    public function amend_delete(Request $req)
    {
        $title = "delete";
        $state = $req->state;
        $no_sj = $req->code;
        $payment_id = $req->payment_id;

        if($state == "payment"){
            return view("master.delete", ["title" => $title, "data" => $state, "code" => $payment_id]);
        }
        //dd($state);
        return view("master.delete", ["title" => $title, "data" => $state, "code" => $no_sj]);
    }

    public function logout(Request $req)
    {
        $req->session()->flush();
        return redirect()->route("home");
    }
}
