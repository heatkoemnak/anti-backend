<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use Illuminate\Support\Facades\Auth;


class CartController extends Controller
{
    public function index()
    {
        $cartItems = Cart::where('user_id', Auth::id())->with('product')->get();
        return response()->json($cartItems);
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $cartItem = Cart::create([
            'user_id' => Auth::id(),
            'product_id' => $request->product_id,
            'quantity' => $request->quantity,
        ]);

        return response()->json($cartItem, 201);
    }

    public function show($id)
    {
        $cartItem = Cart::where('user_id', Auth::id())->where('id', $id)->with('product')->firstOrFail();
        return response()->json($cartItem);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $cartItem = Cart::where('user_id', Auth::id())->where('id', $id)->firstOrFail();
        $cartItem->update([
            'quantity' => $request->quantity,
        ]);

        return response()->json($cartItem);
    }

    public function destroy($id)
    {
        $cartItem = Cart::where('user_id', Auth::id())->where('id', $id)->firstOrFail();
        $cartItem->delete();

        return response()->json(null, 204);
    }
}
