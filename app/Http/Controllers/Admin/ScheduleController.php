<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\UseCases\ScheduleUseCase;
use App\UseCases\ClassroomUseCase;
use App\Http\Requests\ScheduleStoreRequest;
use App\Http\Requests\ScheduleUpdateRequest;
use App\Entities\ResponseEntity;

class ScheduleController extends Controller
{
    protected $scheduleUseCase;
    protected $classroomUseCase;

    public function __construct(ScheduleUseCase $scheduleUseCase, ClassroomUseCase $classroomUseCase)
    {
        $this->scheduleUseCase = $scheduleUseCase;
        $this->classroomUseCase = $classroomUseCase;
    }

    public function index()
    {
        $schedules = $this->scheduleUseCase->getPaginated();
        return view('_admin.schedule.list', compact('schedules'));
    }

    public function create()
    {
        $classrooms = $this->classroomUseCase->getAll();
        return view('_admin.schedule.add', compact('classrooms'));
    }

    public function store(ScheduleStoreRequest $request)
    {
        $result = $this->scheduleUseCase->store($request->validated());

        if (!$result['status']) {
            return redirect()->back()->withInput()->with('error', ResponseEntity::MSG_ERROR_SERVER);
        }

        return redirect()->route('admin.schedules.index')->with('success', ResponseEntity::MSG_SUCCESS_CREATE);
    }

    public function edit($id)
    {
        $schedule = $this->scheduleUseCase->getById($id);
        if (!$schedule) abort(404);

        $classrooms = $this->classroomUseCase->getAll();
        return view('_admin.schedule.edit', compact('schedule', 'classrooms'));
    }

    public function update(ScheduleUpdateRequest $request, $id)
    {
        $result = $this->scheduleUseCase->update($id, $request->validated());

        if (!$result['status']) {
            return redirect()->back()->withInput()->with('error', ResponseEntity::MSG_ERROR_SERVER);
        }

        return redirect()->route('admin.schedules.index')->with('success', ResponseEntity::MSG_SUCCESS_UPDATE);
    }

    public function destroy($id)
    {
        $result = $this->scheduleUseCase->delete($id);

        if (!$result['status']) {
            $message = $result['message'] ?? ResponseEntity::MSG_ERROR_SERVER;
            return redirect()->back()->with('error', $message);
        }

        return redirect()->route('admin.schedules.index')->with('success', ResponseEntity::MSG_SUCCESS_DELETE);
    }
}
