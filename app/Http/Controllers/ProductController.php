<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;


class ProductController extends Controller
{
    public function index(){

        $get_data = Product::all();

        return view('users.product',compact('get_data'));
    }

    public function add_product(Request $request){

        $fileName = $request->file('img')->getClientOriginalName();
        $date = Carbon::now()->format('YmdHi');
        $fileName = "{$fileName}_{$date}.jpg";

        // Store the file in the public storage directory
        $storedFile = $request->file('img')->storeAs("public/products", $fileName);

        // Store the file path in the database
        $pathFile = "storage/products/{$fileName}";



        // dd($storedFile);


        $name = $request->name;
        $price = $request->price;
        // $img = $request->img;

        $create = Product::create([

            'name' => $name,
            'price' => $price,
            'image' => $pathFile,

        ]);

        $check = Product::where('name', $name)->exists();

        // dd($check);

        // if($check == true){
        //     return response()->json(['success' => true]);
        // }


        // Alert::success('Product Added Successfully');


        return redirect()->back()->with('message', 'Product Added Successfully');
    }

    public function  delete_product(Request $req, $id){

        // dd($id);

        $data = Product::find($id);
        $data->delete();


        return response()->json(['message' => 'Product Deleted Successfully!']);
    }
    public function  updateproduct(Request $req, $id){

        dd($id);




        return response()->json(['message' => 'Product Deleted Successfully!']);
    }



    public function toastSuccess()
    {
        $this->dispatchBrowserEvent('swal:fire', [
            'type' => 'success',
            'message' => 'Product Add Success',
            'showConfirmButton' => false,
        ]);
    }




}
