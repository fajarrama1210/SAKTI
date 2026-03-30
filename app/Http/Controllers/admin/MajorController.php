<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Entities\ResponseEntity;
use App\Http\Requests\MajorStoreRequest;
use App\Http\Requests\MajorUpdateRequest;
use App\Models\Major;
use App\UseCases\MajorUseCase;
use Illuminate\Http\Request;

class MajorController extends Controller
{
    protected $majorUseCase;

    public function __construct(MajorUseCase $majorUseCase)
    {
        $this->majorUseCase = $majorUseCase;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $majors = $this->majorUseCase->getPaginated();
        return view('_admin.major.list', compact('majors'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('_admin.major.add');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(MajorStoreRequest $request)
    {
        $result = $this->majorUseCase->store($request->validated());

        if (!$result['status']) {
            return redirect()->back()->withInput()->with('error', ResponseEntity::MSG_ERROR_SERVER);
        }

        return redirect()->route('admin.majors.index')->with('success', ResponseEntity::MSG_SUCCESS_CREATE);
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $major = $this->majorUseCase->getById($id);
        if (!$major) abort(404);

        return view('_admin.major.update', compact('major'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(MajorUpdateRequest $request, $id)
    {
        $result = $this->majorUseCase->update($id, $request->validated());

        if (!$result['status']) {
            return redirect()->back()->withInput()->with('error', ResponseEntity::MSG_ERROR_SERVER);
        }

        return redirect()->route('admin.majors.index')->with('success', ResponseEntity::MSG_SUCCESS_UPDATE);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $result = $this->majorUseCase->delete($id);

        if (!$result['status']) {
            $message = $result['message'] ?? ResponseEntity::MSG_ERROR_SERVER;
            return redirect()->back()->with('error', $message);
        }

        return redirect()->route('admin.majors.index')->with('success', ResponseEntity::MSG_SUCCESS_DELETE);
    }
}
