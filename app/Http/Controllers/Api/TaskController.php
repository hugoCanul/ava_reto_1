<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ResponseHelper;
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
        $task = Task::all();
        return ResponseHelper::success($task, 'Task retrieved succesfully');
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

        try{
            $task = Task::create($validated);
            return ResponseHelper::success($task, 'Task created successfully', 201);
        }catch(\Exception $e){
            return ResponseHelper::error('Error creating task', $e->getMessage(), 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Integer $id)
    {
        $task =Task::find($id);
        if($task){
            return ResponseHelper::success($task, 'Task retrieved succesfully', 200);
        }
        return ResponseHelper::error('Task not found', null, 404);
    }

    /**
     * Update the specified resource in storage.
     */
    //actualizar tarea
    public function update(Request $request, Integer $id)
    {
        $task= Task::find($id);
        if(!$task){
            return ResponseHelper::success('Task not found', null, 400);
        }
        $validated = $request ->validate([
            'title'=> 'required|string|max:255',
            'description'=> 'nullable|string',
            'status'=> 'required|in::peding, in-progress,completed',
        ]);

        try{
            $task->update($validated);
            return ResponseHelper::success('Task updated successfully');
        }catch(\Exception $e){
            return ResponseHelper::error('Error updating task', $e->getMessage(), 500);
        }
        
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
            return ResponseHelper::success(null, 'Task deleted successfully');
        }
        return ResponseHelper::error('Task not found', null, 404);
    }
}
