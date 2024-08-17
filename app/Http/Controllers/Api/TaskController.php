<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;
use Ramsey\Uuid\Type\Integer;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */

     //Obtener las tareas
    public function index()
    {
        return response()->json(Task::all(),200);
    }

    /**
     * Store a newly created resource in storage.
     */

     //crear una nueva tarea
    public function store(Request $request)
    {
        $validated = $request ->validate([
            'title'=> 'required|string|max:255',
            'description'=> 'nullable|string',
            'status'=> 'required|in::peding, in-progress,completed',
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Integer $id)
    {
        $task =Task::find($id);
        if($task){
            return response()->json($task,200);
        }
        return response()->json(['message'=>'Task not found'],404);
    }

    /**
     * Update the specified resource in storage.
     */
    //actualizar tarea
    public function update(Request $request, Integer $id)
    {
        $task= Task::find($id);
        if(!$task){
            return response()->json(['message'=>'Task not found'],404);
        }
        $validated = $request ->validate([
            'title'=> 'required|string|max:255',
            'description'=> 'nullable|string',
            'status'=> 'required|in::peding, in-progress,completed',
        ]);
        $task->update($validated);
        return response()->json($task,200);
    }

    /**
     * Remove the specified resource from storage.
     */
    //Eliminar una tarea
    public function destroy(integer $id)
    {
        $task=Task::find($id);
        if($task){
            $task->delete();
            return response()->json(['message'=>'Task deleted'],200);
        }
        return response ()->json(['message'=> 'Task not found'],404);
    }
}
