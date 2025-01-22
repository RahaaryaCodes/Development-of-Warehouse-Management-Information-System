<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        // Cek apakah pengguna sudah login
        if (Auth::check()) {
            // Dapatkan data pengguna yang sedang login
            $user = Auth::user();

            // Tampilkan dashboard dengan data pengguna
            return view('dashboard', compact('user'));
        } else {
            // Jika pengguna belum login, arahkan ke halaman login
            return redirect()->route('login');
        }
    }
}
