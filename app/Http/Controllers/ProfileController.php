<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    public function updateAvatar(Request $request): RedirectResponse
    {
        $request->validate([
            'avatar' => ['required', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
        ], [
            'avatar.required' => 'File foto profil wajib diunggah.',
            'avatar.image' => 'File harus berupa gambar.',
            'avatar.mimes' => 'Format gambar yang diperbolehkan hanya JPEG, PNG, dan JPG.',
            'avatar.max' => 'Ukuran gambar maksimal adalah 2 MB.',
        ]);

        $user = Auth::user();
        $disk = env('FILESYSTEM_DISK', 's3');

        // Hapus foto profil lama jika ada
        if ($user->avatar) {
            try {
                if (Storage::disk($disk)->exists($user->avatar)) {
                    Storage::disk($disk)->delete($user->avatar);
                }
            } catch (\Exception $e) {
                // Abaikan jika error saat menghapus (misal koneksi S3 gagal/file tidak ada)
            }
        }

        // Upload foto profil baru
        $path = false;
        try {
            $path = $request->file('avatar')->store('avatars', $disk);
        } catch (\Exception $e) {
            // Abaikan/tangkap exception jika dilempar
        }

        if ($path === false) {
            // Jika disk s3 gagal, coba fallback ke public disk
            if ($disk === 's3') {
                try {
                    $path = $request->file('avatar')->store('avatars', 'public');
                    if ($path !== false) {
                        DB::table('users')
                            ->where('id', $user->id)
                            ->update([
                                'avatar' => $path,
                                'updated_at' => now(),
                            ]);
                        return Redirect::route('profile.edit')->with('success', 'Foto profil Anda berhasil diperbarui!');
                    }
                } catch (\Exception $fallbackEx) {
                    return Redirect::back()->with('error', 'Gagal mengunggah foto profil: ' . $fallbackEx->getMessage());
                }
            }
            return Redirect::back()->with('error', 'Gagal mengunggah foto profil ke penyimpanan.');
        }

        // Update path di database
        DB::table('users')
            ->where('id', $user->id)
            ->update([
                'avatar' => $path,
                'updated_at' => now(),
            ]);

        return Redirect::route('profile.edit')->with('success', 'Foto profil Anda berhasil diperbarui!');
    }
}
