<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\UseCases\ClassroomUseCase;
use App\UseCases\MajorUseCase;
use App\Http\Requests\ClassroomStoreRequest;
use App\Http\Requests\ClassroomUpdateRequest;
use App\Entities\ResponseEntity;

class ClassroomController extends Controller
{
    protected $classroomUseCase;
    protected $majorUseCase;

    public function __construct(ClassroomUseCase $classroomUseCase, MajorUseCase $majorUseCase)
    {
        $this->classroomUseCase = $classroomUseCase;
        $this->majorUseCase = $majorUseCase;
    }

    public function index()
    {
        $classrooms = $this->classroomUseCase->getPaginated();
        return view('_admin.classroom.list', compact('classrooms'));
    }

    public function create()
    {
        // Ambil data jurusan untuk Dropdown
        $majors = $this->majorUseCase->getAll();
        return view('_admin.classroom.add', compact('majors'));
    }

    public function store(ClassroomStoreRequest $request)
    {
        $result = $this->classroomUseCase->store($request->validated());

        if (!$result['status']) {
            return redirect()->back()->withInput()->with('error', ResponseEntity::MSG_ERROR_SERVER);
        }

        return redirect()->route('admin.classrooms.index')->with('success', ResponseEntity::MSG_SUCCESS_CREATE);
    }

    public function edit($id)
    {
        $classroom = $this->classroomUseCase->getById($id);
        if (!$classroom) abort(404);

        $majors = $this->majorUseCase->getAll();
        return view('_admin.classroom.edit', compact('classroom', 'majors'));
    }

    public function update(ClassroomUpdateRequest $request, $id)
    {
        $result = $this->classroomUseCase->update($id, $request->validated());

        if (!$result['status']) {
            return redirect()->back()->withInput()->with('error', ResponseEntity::MSG_ERROR_SERVER);
        }

        return redirect()->route('admin.classrooms.index')->with('success', ResponseEntity::MSG_SUCCESS_UPDATE);
    }

    public function destroy($id)
    {
        $result = $this->classroomUseCase->delete($id);

        if (!$result['status']) {
            $message = $result['message'] ?? ResponseEntity::MSG_ERROR_SERVER;
            return redirect()->back()->with('error', $message);
        }

        return redirect()->route('admin.classrooms.index')->with('success', ResponseEntity::MSG_SUCCESS_DELETE);
    }
}
