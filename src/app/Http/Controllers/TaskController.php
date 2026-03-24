<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaskRequest;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Models\Task;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;


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
     * Store a newly created resource in storage.　ここ本番ではいちいち消すの？
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(TaskRequest $request)
    {
        // dd($request->hasFile('image_path'), $request->file('image_path'));
        $validated = $request->validated();
        $validated['user_id'] = auth()->id();


        // dd($request->all(), $request->file('image_path'));
        if ($request->hasFile('image')) {
            try {
                $validated['image_path'] = $request->file('image')->store('images', 'public');
                // dd($validated['image_path']);
                Log::error('画像保存成功', [
                    'user_id' => auth()->id(),
                    'path' => $validated['image_path'],
                ]);
            } catch (\Exception $e) {
                Log::error('画像保存失敗', [
                    'user_id' => auth()->id(),
                    'error' => $e->getMessage(),
                ]);
                return back()->withErrors(['image' => '画像の保存に失敗しました']);
            }
        } else {
            $validated['image_path'] = null;
        }

        // Task::create($request->validated());
        // dd($validated);
        $task = Task::create($validated);

        Log::info('タスク作成', [
            'user_id' => auth()->id(),
            'task_id' => $task->id,
            'title' => $task->title,
        ]);

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
        $validated = $request->validated();

        if ($request->hasFile('image')) {
            try {
                if ($task->image_path) {
                    Storage::disk('public')->delete($task->image_path);
                }
                $validated['image_path'] = $request->file('image')->store('images', 'public');
                // dd($validated['image_path']);
                Log::error('画像保存成功', [
                    'user_id' => auth()->id(),
                    'path' => $validated['image_path'],
                ]);
            } catch (\Exception $e) {
                Log::error('画像保存失敗', [
                    'user_id' => auth()->id(),
                    'error' => $e->getMessage(),
                ]);
                return back()->withErrors(['image' => '画像の保存に失敗しました']);
            }
        } else {
            $validated['image_path'] = null;
        }

        $task->update($validated);
        Log::info('タスク更新', [
            'user_id' => auth()->id(),
            'task_id' => $task->id,
            'title' => $task->title,
        ]);
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

        if ($task->image_path) {
            Storage::disk('public')->delete($task->image_path);
        }

        Log::info('タスク削除', [
            'user_id' => auth()->id(),
            'task_id' => $task->id,
            'title' => $task->title,
        ]);
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
