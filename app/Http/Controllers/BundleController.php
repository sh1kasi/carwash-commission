<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Bundling;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Bundling_product;

class BundleController extends Controller
{
    public function index()
    {
        $bundle = Bundling::get();

        $bundling_price = 0;
        foreach ($bundle as $bundling) {
            foreach ($bundling->products as $data) {
                $bundling_price += $data->price;
            }
        }
        // dd($bundling_price);

        return view('admin.bundlingIndex', compact('bundle'));
    }

    public function form_index()
    {
        $product = Product::get();

        return view('admin.bundlingForm', compact('product'));
    }

    public function form_store(Request $request)
    {
        
        $this->validate($request, [
            'name' => 'required | unique:bundlings',
            'servicesCheckbox' => 'required',
        ]);
        
        // dd($request);

        $name = $request->name;
        $products_id = $request->servicesCheckbox;

        $total_price = 0;
        $product = Product::whereIn('id', $products_id)->get();
        foreach ($product as $data) {
            $total_price += $data->price;
        }

        // dd($total_price);

        $bundle = new Bundling;
        $bundle->name = $name;
        $bundle->total_price = $total_price;
        $bundle->save();
        $bundle->products()->attach($products_id);

        return redirect('/bundle')->with('success', 'Berhasil menambahkan Bundle');
    }

    public function edit_index($id)
    {

        $bundle = Bundling::find($id);
        $product = Product::get();

        $bundling_product = Bundling_product::where('bundling_id', $id)->get();

        $bundled = [];
        foreach ($bundling_product as $array) {
            $bundled[] = $array->product_id;
        }

        $update_value = implode(',',$bundled);
        // $updated = explode(',',$update_value);
        // dd($updated); 

        // dd($bundled);

        return view('admin.bundlingEdit', compact('bundle', 'product', 'bundled', 'update_value'));
    }

    public function bundle_update(Request $request, $id)
    {

        $bundle = Bundling::find($id);
        $products_id = $request->servicesCheckbox;

        $this->validate($request, [
            'name' => 'required',
            'servicesCheckbox' => 'required',
        ]);

        $name = $request->name;
        $products_id = $request->servicesCheckbox;
        $product = Product::whereIn('id', $products_id)->get();
        $detach_service = explode(',',$request->update_service);

        // dd($detach_service);

        $bundle_price = 0;
        foreach ($product as $prod_id) {
            $bundle_price += $prod_id->price;
        }

        $bundle->name = $name;
        $bundle->total_price = $bundle_price;
        $bundle->save();
        $bundle->products()->detach($detach_service);
        $bundle->products()->attach($products_id);
        // dd($bundle);
        
        return redirect('/bundle')->with('success', 'Berhasil mengedit Bundle');
        
    }

    public function destroy_bundle($id)
    {
        $bundle = Bundling::find($id);
        // dd($id);
        $bundle->delete();

        return back()->with('success', 'Berhasil menghapus bundle');

    }
}
