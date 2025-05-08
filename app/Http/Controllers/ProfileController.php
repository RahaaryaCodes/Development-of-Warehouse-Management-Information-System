<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        return view('pengaturan.profile.index', compact('user'));
    }

    public function edit()
    {
        $user = auth()->user();
        return view('pengaturan.profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        // Validasi input
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'photo' => 'nullable|image|mimes:jpg,png,jpeg|max:2048',
        ]);

        // Proses upload foto jika ada
        if ($request->hasFile('photo') && $request->file('photo')->isValid()) {
            try {
                // Hapus foto lama jika ada
                if ($user->photo && Storage::disk('public')->exists($user->photo)) {
                    Storage::disk('public')->delete($user->photo);
                }

                // Simpan foto baru dengan nama yang unik
                $photoName = time() . '_' . $user->id . '.' . $request->photo->extension();
                $photoPath = $request->file('photo')->storeAs('profile', $photoName, 'public');
                
                // Update path foto di database
                $user->photo = $photoPath;
                
                // Log informasi untuk debugging
                Log::info('Profil foto berhasil diupdate', [
                    'user_id' => $user->id,
                    'file_name' => $photoName,
                    'path' => $photoPath,
                    'exists' => Storage::disk('public')->exists($photoPath)
                ]);
            } catch (\Exception $e) {
                Log::error('Error saat mengupload foto profil: ' . $e->getMessage());
                return redirect()->back()->with('error', 'Gagal mengupload foto. Silakan coba lagi.');
            }
        }

        // Update nama dan email
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->save();

        // Bersihkan cache browser dengan menambahkan parameter query di URL redirect
        return redirect()->route('profile', ['v' => time()])->with('success', 'Profil berhasil diperbarui.');
    }

    public function editPassword()
    {
        return view('pengaturan.profile.password');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $user = auth()->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Password lama salah.']);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return redirect()->route('profile')->with('success', 'Password berhasil diubah.');
    }

}