<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaskRequest;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Models\Task;


class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Task $task)
    {
        // $tasks = Task::with('category')->get();
        $tasks = auth()->user()->tasks()
            ->with('category')
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $categories = Category::all();

        return view('tasks.index', compact('tasks', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // $categories = Category::all();
        $categories = Category::orderBy('name')->get();
        return view('tasks.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(TaskRequest $request)
    {
        $validated = $request->validated();
        $validated['user_id'] = auth()->id();

        // Task::create($request->validated());
        Task::create($validated);
        return redirect()->route('tasks.index')->with('success', 'タスクを追加しました');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Task $task)
    {
        $this->authorize('view', $task);
        $task->load('category');

        return view('tasks.show', compact('task'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Task $task)
    {
        $this->authorize('update', $task);
        // $categories = Category::all();
        $categories = Category::orderBy('name')->get();
        return view('tasks.edit', compact('task', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(TaskRequest $request, Task $task)
    {
        $this->authorize('update', $task);

        $task->update($request->validated());
        return redirect()->route('tasks.index')->with('success', 'タスクを更新しました');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Task $task)
    {
        $this->authorize('delete', $task);

        $task->delete();

        return redirect()->route('tasks.index')->with('success', 'タスクを削除しました');
    }

    public function search(Request $request)
    {
        $tasks = auth()->user()->tasks()
            ->with('category')
            ->categorySearch($request->category_id)
            ->keywordSearch($request->keyword)
            ->paginate(10);

        $categories = Category::all();

        return view('tasks.index', compact('tasks', 'categories'));
    }

    public function sort(Request $request)
    {
        $query = auth()->user()->tasks()->with('category');

        if ($request->sort === 'priority_desc') {
            $query->orderBy('priority', 'desc');
        } elseif ($request->sort === 'priority_asc') {
            $query->orderBy('priority', 'asc');
        } elseif ($request->sort === 'created_desc') {
            $query->orderBy('created_at', 'desc');
        } elseif ($request->sort === 'created_asc') {
            $query->orderBy('created_at', 'asc');
        }

        $tasks = $query->paginate(10);
        $categories = Category::all();

        return view('tasks.index', compact('tasks', 'categories'));
    }


}
