<?php

namespace App\Http\Controllers;

use App\Models\PackageOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;

class PackageOrderController extends Controller
{
    public function store(Request $request, $slug)
    {
        try {
            $validated = $request->validate([
                'company_name' => 'required|string|max:255',
                'email'        => 'required|email|max:255',
                // 'phone'        => 'required|string|max:20|phone:*',
                'phone'        => 'required|string|max:20',
                'postal_code'  => 'required|string|max:20',
                'address'      => 'required|string',
                'location'     => 'required|string|max:255',
            ]);

            // The slug acts as package_name
            $data = array_merge($validated, ['package_name' => $slug]);

            $order = PackageOrder::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Package order submitted successfully.',
                'data'    => $order
            ]);
        } catch (Exception $e) {
            Log::error('Package order submission failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to submit package order.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function index()
    {
        $emails = PackageOrder::latest()->pluck('email');

        return response()->json([
            'success' => true,
            'emails'  => $emails
        ]);
    }

    public function allShow()
    {
        $orders = PackageOrder::latest()
            ->take(10)
            ->get(['package_name', 'email', 'company_name', 'location', 'created_at']);

        $orders->transform(function ($order) {
            return [
                'package_name' => $order->package_name,
                'company_name' => $order->company_name,
                'email'        => $order->email,
                'location'     => $order->location,
                'created_at'   => $order->created_at->format('d/m/Y'),
            ];
        });

        return response()->json([
            'success' => true,
            'orders' => $orders,
        ]);
    }


    public function silverAllShow()
    {
        $silverOrders = PackageOrder::where('package_name', 'silver')
            ->latest()
            ->take(10)
            ->get(['package_name', 'email', 'company_name', 'location', 'created_at']);

        $silverOrders->transform(function ($order) {
            return [
                'package_name' => $order->package_name,
                'email'        => $order->email,
                'company_name' => $order->company_name,
                'location'     => $order->location,
                'created_at'   => $order->created_at->format('d/m/Y'),
            ];
        });

        return response()->json([
            'success' => true,
            'silverOrders' => $silverOrders,
        ]);
    }

    public function bronzeAllShow()
    {
        $bronzeOrders = PackageOrder::where('package_name', 'bronze')
            ->latest()
            ->take(10)
            ->get(['package_name', 'email', 'company_name', 'location', 'created_at']);

        $bronzeOrders->transform(function ($order) {
            return [
                'package_name' => $order->package_name,
                'email'        => $order->email,
                'company_name' => $order->company_name,
                'location'     => $order->location,
                'created_at'   => $order->created_at->format('d/m/Y'),
            ];
        });

        return response()->json([
            'success' => true,
            'bronzeOrders' => $bronzeOrders,
        ]);
    }
}
