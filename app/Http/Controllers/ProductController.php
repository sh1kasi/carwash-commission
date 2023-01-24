<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Bundling;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Bundling_product;

class ProductController extends Controller
{
    public function index()
    {
        $service = Product::get();

        $trashed_service = Product::onlyTrashed()->get();
        

        return view('admin.serviceIndex', compact('service', 'trashed_service'));
    }

    public function form_index()
    {
        $product = Product::get();
        
        return view('admin.serviceForm', compact('product'));
    }

    public function product_store(Request $request)
    {
        
            // dd($request);

            $this->validate($request, [ 
                'work' => 'required',
                'price' => 'required',
                'service' => 'required | unique:products',
                'commission_type' => 'required',
                'commission_value' => 'required',
            ]);

            $product = new Product;
            $product->service = $request->service;
            $product->price = $request->price;
            $product->type_commission = $request->commission_type;
            $product->commission_value = $request->commission_value;
            $product->status = $request->work;
            $product->save();
        
        return redirect('/layanan')->with('success', 'Layanan Berhasil Tersimpan');
    }

    public function edit_index($id)
    {
        $product = Product::find($id);

        return view('admin.serviceEdit', compact('product', 'id'));
    }

    public function product_update(Request $request, $id)
    {
        $product = Product::find($id);
        // dd($request);

        $this->validate($request, [ 
            'work' => 'required',
            'price' => 'required',
            'service' => 'required',
            'commission_type' => 'required',
            'commission_value' => 'required',
        ]);

            $product->service = $request->service;
            $product->price = $request->price;
            $product->type_commission = $request->commission_type;
            $product->commission_value = $request->commission_value;
            $product->status = $request->work;
            $product->save();
        

        return redirect('/layanan')->with('success', 'Berhasil mengedit layanan');
    }

    public function destroy($id)
    {
        $product = Product::find($id);
        $cek_relasi = Bundling_product::where('product_id', $id)->exists();

        if ($cek_relasi == true) {
            return back()->with('failed', 'Layanan '. $product->service .' tidak bisa terhapus karena product ini memiliki bundle');
        } else {
            // Bundling_product::where('product_id', $id)->each(function($bundles) {
            //     $bundles->delete();
            // });
            $product->delete();
        }
        
        // dd(count($product->relation));
        return back()->with('success', 'Berhasil menghapus layanan '. $product->service);

    }

    public function restore($id)
    {
        $trashed_service = Product::withTrashed()->where('id', $id)->restore();

        return back()->with('success', 'Berhasil me-restore Layanan');
    }
}
