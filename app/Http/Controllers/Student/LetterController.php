<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Letter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class LetterController extends Controller
{
    public function index()
    {
        $studentId = Auth::user()->student_id;
        if (!$studentId) {
            abort(404, 'Data siswa tidak ditemukan.');
        }

        $letters = Letter::where('student_id', $studentId)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('student.letters.index', compact('letters'));
    }

    public function store(Request $request)
    {
        $studentId = Auth::user()->student_id;
        if (!$studentId) {
            abort(404, 'Data siswa tidak ditemukan.');
        }

        $request->validate([
            'type' => 'required|in:sick,permission',
            'description' => 'required|string',
            'file' => 'required|file|mimes:pdf,png,jpg,jpeg|max:2048',
            'submission_date' => 'required|date'
        ], [
            'type.required' => 'Kategori surat wajib dipilih.',
            'type.in' => 'Kategori surat tidak valid.',
            'description.required' => 'Keterangan/deskripsi wajib diisi.',
            'file.required' => 'File surat wajib diunggah.',
            'file.file' => 'File yang diunggah tidak valid.',
            'file.mimes' => 'Format file harus berupa PDF, PNG, JPG, atau JPEG.',
            'file.max' => 'Ukuran file maksimal adalah 2MB.',
            'file.uploaded' => 'Ukuran file terlalu besar. Maksimal ukuran file adalah 2MB.',
            'submission_date.required' => 'Tanggal mulai wajib diisi.',
            'submission_date.date' => 'Format tanggal tidak valid.'
        ]);

        $filePath = $request->file('file')->store('letters', 'public');

        Letter::create([
            'student_id' => $studentId,
            'type' => $request->type,
            'description' => $request->description,
            'file_path' => $filePath,
            'status' => 'pending',
            'submission_date' => $request->submission_date,
        ]);

        return redirect()->route('student.letters.index')->with('success', 'Surat izin berhasil dikirim.');
    }
}
