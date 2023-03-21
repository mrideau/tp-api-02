<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Order\StoreOrderRequest;
use App\Http\Requests\Order\UpdateOrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\Product;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Order::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOrderRequest $request)
    {
        $products_ids = $request->products;

        $order = new Order();
        $order->customer()->associate($request->customer_id);
        $order->save();

        foreach ($products_ids as $product_id) {
            $order->products()->attach($product_id);
        }

        $res = new OrderResource($order);

        return $order;
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        return $order;
    }

    /**
     * Update the specified resource.
     */
    public function update(UpdateOrderRequest $request, Order $order)
    {
        $order->products()->detach();

        foreach ($request->products as $product_id) {
            if (Product::query()->find($product_id)) {
                $order->products()->attach($product_id);
            }
        }

        return new OrderResource($order);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        $order->delete();

        return response()->json([], 204);
    }
}
