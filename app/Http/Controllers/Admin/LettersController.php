<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Letter;
use Illuminate\Http\Request;

class LettersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $letters = Letter::with('student.user')
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('_admin.letters.index', compact('letters'));
    }

    /**
     * Display the specified letter detail.
     */
    public function show($id)
    {
        $letter = Letter::with('student')->findOrFail($id);
        return view('_admin.letters.show', compact('letter'));
    }

    /**
     * Update the status of the specified resource in storage.
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
        ]);

        $letter = Letter::findOrFail($id);
        $letter->update([
            'status' => $request->status
        ]);

        return redirect()->route('admin.letters.index')->with('success', 'Status surat izin berhasil diperbarui.');
    }
}
