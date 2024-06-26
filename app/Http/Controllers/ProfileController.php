<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Product;
use App\Models\Order; 
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ProfileController extends Controller
{
    public function index()
    {
        try {
            $orders = Order::all();
            return view('orders.index', compact('orders'));
        } catch (\Exception $e) {
            Log::error('Error displaying orders: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menampilkan pesanan.');
        }
    }

    public function view()
    {
        return view('profile.view', ['user' => auth()->user()]);
    }

    public function edit()
    {
        return view('profile.edit', ['user' => auth()->user()]);
    }

    public function riwayatPesanan()
    {
        $userId = Auth::id();
        $orders = Order::where('user_id', $userId)->with('product')->get();

        // Pastikan setiap order memiliki data gambar dan nama produk
        foreach ($orders as $order) {
            $order->gambar = $order->product->image_path ?? 'default_image.jpg';
            $order->name = $order->product->name_product ?? 'Nama Produk Tidak Tersedia';
        }

        return view('profile.riwayatPesanan', compact('orders'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . auth()->id(),
            'address' => 'required|string|max:255'
        ]);

        $user = Auth::user();
        // Log::info('User found: ' . $user);
        $user->update($request->only(['name', 'email', 'address']));

        return redirect()->route('profile.view')->with('success', 'Profil berhasil diperbarui.');
    }

    public function show($id)
    {
        try {
            $product = Product::findOrFail($id);
            return view('products.show', ['product' => $product]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('products.index')->with('error', 'Produk tidak ditemukan.');
        }
    }

    public function delete($id)
    {
        $order = Order::find($id);
        if (!$order) {
            return response()->json(['error' => 'Pesanan tidak ditemukan'], 404);
        }

        if ($order->user_id !== auth()->id()) {
            return response()->json(['error' => 'Tidak memiliki izin'], 403);
        }

        $order->delete();
        return response()->json(['success' => 'Pesanan berhasil dihapus']);
    }
}
