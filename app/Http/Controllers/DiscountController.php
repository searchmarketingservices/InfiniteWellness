<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Discount;
use Illuminate\Support\Facades\Validator;

class DiscountController extends Controller
{
    public function index()
    {
        $discount = Discount::all();
        return view('discount.index', compact('discount'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'amount_per' => 'required|numeric',
            'active' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $discount = new Discount();
        $discount->name = $request->name;
        $discount->amount_per = $request->amount_per;
        $discount->active = $request->active;
        $discount->save();

        return redirect()->back()->with('success', 'Discount Added successfully');
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'amount_per' => 'required|numeric',
            'active' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $discount = Discount::find($id);
        $discount->name = $request->name;
        $discount->amount_per = $request->amount_per;
        $discount->active = $request->active;
        $discount->save();

        return redirect()->back()->with('success', 'Discount Updated successfully');
    }

    public function destroy($id)
    {
        $discount = Discount::find($id);
        $discount->delete();
        return redirect()->back()->with('success', 'Discount Deleted successfully');
    }
}
