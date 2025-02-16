<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Storage;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class MasterController extends Controller
{
    public function create(Request $req)
    {
        $title = "create";
        $data = $req->data;

        $result = $this->getData($data);

        if($data == "user"){
            return view("master.register", ["title" => $title, "data" => $data, "action" => "create"]);
        }
        return view("master.create", ["title" => $title, "keyNames" => $result[1], "data" => $data]);
    }

    public function create_data(Request $req)
    {
        $data = $req->data;
        $input_data = $req->input("input_data");

        switch($data){
            case "vendor":
                Vendor::create([
                    "vendorCode" => $input_data[0],
                    "vendorName" => $input_data[1],
                    "vendorAddress" => $input_data[2],
                    "vendorNPWP" => $input_data[3]
                ]);
                break;
            case "customer":
                Customer::create([
                    "customerCode" => $input_data[0],
                    "customerName" => $input_data[1],
                    "customerAddress" => $input_data[2],
                    "customerNPWP" => $input_data[3]
                ]);
                break;
            case "product":
                Product::create([
                    "productCode" => $input_data[0],
                    "productName" => $input_data[1]
                ]);
                break;
            case "storage":
                Storage::create([
                    "storageCode" => $input_data[0],
                    "storageName" => $input_data[1],
                    "storageAddress" => $input_data[2],
                    "storageNPWP" => $input_data[3]
                ]);
                break;  
            case "user":
                User::create([
                    "userID" => Str::uuid()->toString(),
                    "email" => $input_data[0],
                    "password" => $input_data[1],
                    "userType" => $input_data[2]
                ]);
                break;
        }

        session()->flash('msg', 'data created');
        return redirect()->route("dashboard");
    }

    public function read(Request $req)
    {
        $title = "read";
        $data = $req->data;

        $result = $this->getData($data);

        return view("master.read", ["title" => $title, "result" => $result[0], "keyNames" => $result[1], "data" => $data]);
    }

    public function update(Request $req)
    {
        $title = "read";
        $data = $req->data;
        $code = $req->code;

        $result = $this->getData($data,$code);

        if($data == "user"){
            return view("master.register", ["title" => $title, "result" => $result[2], "data" => $data, "action" => "update"]);
        }
        return view("master.update", ["title" => $title, "result" => $result[2], "keyNames" => $result[1], "data" => $data]);
    }

    public function update_data(Request $req)
    {
        $data = $req->data;
        $code = $req->code;
        $input_data = $req->input("input_data");

        switch($data){
            case "vendor":
                Vendor::where("vendorCode", $code)->update([
                    "vendorCode" => $input_data[0],
                    "vendorName" => $input_data[1],
                    "vendorAddress" => $input_data[2],
                    "vendorNPWP" => $input_data[3]
                ]);
                break;
            case "customer":
                Customer::where("customerCode", $code)->update([
                    "customerCode" => $input_data[0],
                    "customerName" => $input_data[1],
                    "customerAddress" => $input_data[2],
                    "customerNPWP" => $input_data[3]
                ]);
                break;
            case "product":
                Product::where("productCode", $code)->update([
                    "productCode" => $input_data[0],
                    "productName" => $input_data[1]
                ]);
                break;
            case "storage":
                Storage::where("storageCode", $code)->update([
                    "storageCode" => $input_data[0],
                    "storageName" => $input_data[1],
                    "storageAddress" => $input_data[2],
                    "storageNPWP" => $input_data[3]
                ]);
                break;  
            case "user":
                User::where("userID", $code)->get()->update([
                    "email" => $input_data[0],
                    "password" => $input_data[1],
                    "userType" => $input_data[2]
                ]);
                break;
        }

        session()->flash('msg', 'data updated');
        return redirect()->route("dashboard");
    }

    public function delete(Request $req)
    {
        $title = "read";
        $data = $req->data;
        $code = $req->code;

        $result = $this->getData($data,$code);

        return view("master.delete", ["title" => $title, "result" => $result[2], "data" => $data, "code" => $code]);
    }

    public function delete_data(Request $req)
    {
        $data = $req->data;
        $code = $req->code;

        switch($data){
            case "vendor":
                Vendor::where("vendorCode", $code)->delete();
                break;
            case "customer":
                Customer::where("customerCode", $code)->delete();
                break;
            case "product":
                Product::where("productCode", $code)->delete();
                break;
            case "storage":
                Storage::where("storageCode", $code)->delete();
                break;  
            case "user":
                User::where("userID", $code)->delete();
                break;
        }

        session()->flash('msg', 'data deleted');
        return redirect()->route("dashboard");
    }

    public function getData($data, $code = "NON")
    {
        switch($data){
            case "vendor":
                $result = Vendor::all();
                $keyNames = (new Vendor)->getFillable();
                $attr = Vendor::where("vendorCode", $code)->get()->toArray();
                break;
            case "customer":
                $result = Customer::all();
                $keyNames = (new Customer)->getFillable();
                $attr = Customer::where("customerCode", $code)->get()->toArray();
                break;
            case "product":
                $result = Product::all();
                $keyNames = (new Product)->getFillable();
                $attr = Product::where("productCode", $code)->get()->toArray();
                break;
            case "storage":
                $result = Storage::all();
                $keyNames = (new Storage)->getFillable();
                $attr = Storage::where("storageCode", $code)->get()->toArray();
                break;  
            case "user":
                $result = User::all();
                $keyNames = (new User)->getFillable();
                $attr = User::where("userID", $code)->get()->toArray();
                break;
        }

        return [$result, $keyNames, $attr[0] ?? null];
    }
}
