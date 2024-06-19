<?php

namespace App\Http\Controllers;

use App\Http\Requests\storeTaskRequest;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {

            $status = request('status');
            $due_date = request('due_date');

            $tasks = Task::where(function ($query) use ($status, $due_date){

                if (!empty($status)) {
                    $query->where('status', $status);
                }
                if (!empty($due_date)) {
                    $query->where('due_date', $due_date);
                }

            })->get();

            return response()->json([
                'tasks' => $tasks
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Se presentó un inconveniente al consultar las tareas, por favor pongase en contacto con el equipo de soporte'
            ], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(storeTaskRequest $request)
    {
        try {
            DB::beginTransaction();
            $task = Task::create($request->all());
            DB::commit();

            return response()->json([
                'data' => $task,
                'message' => 'La tarea se creó correctamente'
            ], 201);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message' => 'Se presentó un inconveniente al consultar las tareas, por favor pongase en contacto con el equipo de soporte',
                $th->getLine(),
                $th->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $task = Task::find($id);

            if (!$task) {
                return response()->json(['message' => 'Tarea no encontrada'], 404);
            }

            return response()->json($task);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Se presentó un inconveniente al consultar la tarea, por favor pongase en contacto con el equipo de soporte'
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {

            DB::beginTransaction();

            $task = Task::find($id);

            if (!$task) {
                return response()->json(['message' => 'Tarea para actualizar no encontrada'], 404);
            }

            $task->update($request->all());

            DB::commit();

            return response()->json([
                'message' => 'Tarea actualizada correctamente'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Se presentó un inconveniente al actualizar la tarea, por favor pongase en contacto con el equipo de soporte'
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            DB::beginTransaction();

            $task = Task::find($id);

            if (!$task) {
                return response()->json(['message' => 'Tarea no encontrada'], 404);
            }

            $task->delete();

            DB::commit();

            return response()->json(['message' => 'Tarea eliminada correctamente']);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message' => 'Se presentó un inconveniente al actualizar la tarea, por favor pongase en contacto con el equipo de soporte'
            ], 500);
        }
    }
    public function task_deleted()
    {
        try {

            $task = Task::onlyTrashed()->get();

            return $task;

        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Se presentó un inconveniente obtener las tareas, por favor pongase en contacto con el equipo de soporte',
                $th->getMessage()
            ], 500);
        }
    }
}
