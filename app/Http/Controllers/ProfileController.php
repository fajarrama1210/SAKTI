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
            'avatar' => ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ], [
            'avatar.required' => 'File foto profil wajib diunggah.',
            'avatar.image' => 'File harus berupa gambar.',
            'avatar.mimes' => 'Format gambar yang diperbolehkan hanya JPEG, PNG, JPG, dan WebP.',
            'avatar.max' => 'Ukuran gambar maksimal adalah 2 MB.',
        ]);

        $user = Auth::user();

        // Selalu gunakan disk 'public' agar file dapat diakses browser secara langsung.
        // Jika FILESYSTEM_DISK=s3, gunakan S3; selainnya (local/public/dll) gunakan 'public'.
        $configDisk = config('filesystems.default', 'local');
        $disk = ($configDisk === 's3') ? 's3' : 'public';

        // Hapus foto profil lama jika ada
        if ($user->avatar) {
            try {
                Storage::disk($disk)->delete($user->avatar);
                if ($disk !== 'public') {
                    Storage::disk('public')->delete($user->avatar);
                }
            } catch (\Exception $e) {
                // Abaikan error hapus
            }
        }

        // Upload foto profil baru — coba dengan kompresi dulu, fallback ke upload langsung
        $path = false;
        $uploadedFile = $request->file('avatar');

        // Langkah 1: Coba proses kompresi & resize
        $processedFile = null;
        try {
            $processedFile = $this->resizeAndCompressAvatar($uploadedFile);
        } catch (\Exception $e) {
            $processedFile = null;
        }

        // Langkah 2: Upload file (hasil kompresi atau file asli)
        try {
            if ($processedFile instanceof \Illuminate\Http\File) {
                $path = Storage::disk($disk)->putFile('avatars', $processedFile);
                @unlink($processedFile->getRealPath());
            } else {
                $path = $uploadedFile->store('avatars', $disk);
            }
        } catch (\Exception $e) {
            $path = false;
        }

        // Langkah 3: Jika S3 gagal, fallback ke disk public lokal
        if ($path === false && $disk === 's3') {
            try {
                $path = $uploadedFile->store('avatars', 'public');
                $disk = 'public';
            } catch (\Exception $fallbackEx) {
                return Redirect::back()->with('error', 'Gagal mengunggah foto profil. Silakan coba lagi.');
            }
        }

        if ($path === false) {
            return Redirect::back()->with('error', 'Gagal menyimpan foto profil ke storage. Silakan coba lagi.');
        }

        // Simpan path ke database
        DB::table('users')
            ->where('id', $user->id)
            ->update([
                'avatar' => $path,
                'updated_at' => now(),
            ]);

        return Redirect::route('profile.edit')->with('success', 'Foto profil Anda berhasil diperbarui!');
    }

    /**
     * Mengubah dimensi gambar menjadi maksimal 300x300 px dan melakukan kompresi ke format WebP (jika didukung) atau JPEG.
     */
    private function resizeAndCompressAvatar($file, $maxWidth = 300, $maxHeight = 300)
    {
        if (!extension_loaded('gd')) {
            return $file;
        }

        list($width, $height, $type) = @getimagesize($file->getRealPath());

        switch ($type) {
            case IMAGETYPE_JPEG:
                $srcImage = @imagecreatefromjpeg($file->getRealPath());
                break;
            case IMAGETYPE_PNG:
                $srcImage = @imagecreatefrompng($file->getRealPath());
                break;
            default:
                return $file;
        }

        if (!$srcImage) {
            return $file;
        }

        // Hitung rasio aspek baru
        $ratio = $width / $height;
        if ($width > $height) {
            $newWidth = $maxWidth;
            $newHeight = (int)($maxWidth / $ratio);
        } else {
            $newHeight = $maxHeight;
            $newWidth = (int)($maxHeight * $ratio);
        }

        $dstImage = imagecreatetruecolor($newWidth, $newHeight);

        // Pertahankan transparansi untuk format PNG
        if ($type == IMAGETYPE_PNG) {
            imagealphablending($dstImage, false);
            imagesavealpha($dstImage, true);
            $transparent = imagecolorallocatealpha($dstImage, 255, 255, 255, 127);
            imagefilledrectangle($dstImage, 0, 0, $newWidth, $newHeight, $transparent);
        }

        imagecopyresampled($dstImage, $srcImage, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        $tempPath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'avatar_' . uniqid() . (function_exists('imagewebp') ? '.webp' : '.jpg');

        if (function_exists('imagewebp')) {
            @imagewebp($dstImage, $tempPath, 80);
        } else {
            @imagejpeg($dstImage, $tempPath, 80);
        }

        @imagedestroy($srcImage);
        @imagedestroy($dstImage);

        return new \Illuminate\Http\File($tempPath);
    }
}
