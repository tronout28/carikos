<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\User;
use App\Models\Kost;

class OrderController extends Controller
{
    public function __construct()
    {
        \Midtrans\Config::$serverKey = config('midtrans.server_key');
        \Midtrans\Config::$isProduction = false;
        \Midtrans\Config::$isSanitized = true;
        \Midtrans\Config::$is3ds = true;
    }

    public function checkoutKost(Request $request){
        $user = auth()->user();

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not authenticated'], 401);
        }

        $request->validate([
            'kost_id' => 'required|exists:kosts,id',
        ]);

        $kost = Kost::find($request->kost_id);
        if (!$kost) {
            return response()->json(['success' => false, 'message' => 'Kost not found'], 404);
        }

        $totalPrice = $kost->price;

        $order = Order::create([
            'user_id' => $user->id,
            'kost_id' => $kost->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone_number' => $user->phone_number,
            'total_price' => $totalPrice,
            'status' => 'unpaid',
        ]);

        $transactionOrderId = 'ORDER-' . $order->id . '-' . time();

        $params = [
            'transaction_details' => [
                'order_id' => $transactionOrderId,
                'gross_amount' => $totalPrice,
            ],
            'customer_details' => [
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone_number,
            ],
        ];

        if (!$order->snap_token) {
            $snapToken = \Midtrans\Snap::getSnapToken($params);
            $order->update(['snap_token' => $snapToken]);
        } else {
            $snapToken = $order->snap_token;
        }

        $snapViewUrl = route('snap.view', ['orderId' => $order->id]);

        return response()->json([
            'success' => true,
            'data' => $order,
            'kost_info' => $kost,
            'snap_token' => $snapToken,
            'snap_url' => $snapViewUrl,
        ]);
    }

    public function snapView($orderId)
    {
        $order = Order::find($orderId);
        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Order not found'], 404);
        }

        $params = [
            'transaction_details' => [
                'order_id' => 'ORDER-' . $order->id . '-' . time(),
                'gross_amount' => $order->total_price,
            ],
            'customer_details' => [
                'name' => $order->name,
                'email' => $order->email,
                'phone' => $order->phone_number,
            ],
        ];

        if (!$order->snap_token) {
            $snapToken = \Midtrans\Snap::getSnapToken($params);
            $order->update(['snap_token' => $snapToken]);
        } else {
            $snapToken = $order->snap_token;
        }

        return view('snap_view', [
            'snapToken' => $snapToken,
            'order_id' => $order->id,
        ]);
    }

    public function callback(Request $request){
        if (!$request->has(['order_id', 'status_code', 'gross_amount', 'signature_key', 'transaction_status'])) {
            return response()->json(['error' => 'Missing required parameters'], 400);
        }

        $serverKey = config('midtrans.server_key');
        $hashed = hash('sha512', $request->order_id . $request->status_code . $request->gross_amount . $serverKey);

        if ($hashed !== $request->signature_key) {
            return response()->json(['error' => 'Invalid signature key'], 403);
        }

        $orderId = explode('-', str_replace('ORDER-', '', $request->order_id))[0];
        $order = Order::with('user')->find($orderId); 

        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        if (in_array($request->transaction_status, ['capture', 'settlement'])) {
            $order->update(['status' => 'paid']);
            return response()->json(['success' => true, 'message' => 'Payment success']);
        } else {
            $order->update(['status' => 'pending']);
            return response()->json(['success' => false, 'message' => 'Payment pending']);
        }
    }

    public function invoiceView($id){
        $order = Order::find($id);
        if (!$order) {
            abort(404, "Order not found");
        }

        $user = User::find($order->user_id);
        if (!$user) {
            abort(404, "User not found");
        }

        $kost = Kost::find($order->kost_id);
        if (!$kost) {
            abort(404, "Kost not found");
        }

        return view('invoice', [
            'order' => $order,
            'user' => $user,
            'kost' => $kost,
        ]);
    }
}
