<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use RealRashid\SweetAlert\Facades\Alert;

class OrderController extends Controller
{
    public function checkout()
    {
        $user_id = Auth::id();
        $carts = Cart::where('user_id', $user_id)->get();

        if ($carts == null) {
            return Redirect::back();
        }

        $order = Order::create([
            'user_id' => $user_id,
        ]);

        foreach ($carts as $cart) {
            $product = Product::find($cart->product_id);
            $product->update([
                'stock' => $product->stock - $cart->amount,
            ]);

            Transaction::create([
                'amount' => $cart->amount,
                'order_id' => $order->id,
                'product_id' => $cart->product_id,
            ]);

            $cart->delete();
        }

        return Redirect::route('show_order', $order);
    }

    public function index_order()
    {
        $user = Auth::user();
        $is_admin = $user->is_admin;

        if ($is_admin) {
            $orders = Order::all();
        } else {
            $orders = Order::where('user_id', $user->id)->get();
        }

        return view('index_order', compact('orders'));
    }

    public function show_order(Order $order)
    {
        $user = Auth::user();
        $is_admin = $user->is_admin;

        if ($is_admin || $user->id == $user->id) {
            return view('show_order', compact('order'));
        }

        return Redirect::route('index_order');
    }

    public function submit_payment_receipt(Order $order, Request $request)
    {
        $file = $request->file('payment_receipt');
        $path = time().'_'.$order->id.'.'.$file->getClientOriginalExtension();

        Storage::disk('local')->put('public/'.$path, file_get_contents($file));

        $order->update([
            'payment_receipt' => $path,
        ]);

        return Redirect::back();
    }

    public function confirm_payment(Order $order)
    {
        $order->update([
            'is_paid' => true,
        ]);

        Alert::success('Payment Confirmed', 'Pembayaran telah dikonfirmasi!');

        return Redirect::back();
    }

    public function order_data()
    {
        $orders = Order::all();

        return view('order_data', compact('orders'));
    }

    public function filter(Request $request)
    {
        $start_date = $request->start_date;
        $end_date = $request->end_date;

        $orders = Order::whereDate('created_at', '>=', $start_date)
                     ->whereDate('created_at', '<=', $end_date)
                     ->get();

        return view('order_data', compact('orders'));
    }

    public function delete_order(Order $order)
    {
        $order->delete();

        return Redirect::route('list-order');
    }
}
