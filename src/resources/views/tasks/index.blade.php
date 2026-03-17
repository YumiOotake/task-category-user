@extends('layouts.app')
@section('css')
    <link rel="stylesheet" href="{{ asset('css/tasks/index.css') }}">
@endsection
@section('content')
    <div class="login-name">
        @auth
            <h2>{{ auth()->user()->name }}さん ログイン中</h2>
        @endauth
    </div>
    <div class="task__content">
        <div class="task__section">
            <div class="section__title">
                <h1>Task一覧</h1>
            </div>
            <div class="section__button">
                <a href="{{ route('tasks.create') }}" class="section__button-add">Task Add</a>
            </div>
        </div>
        <div class="task-table">
            <table class="task-table__inner">
                <thead>
                    <tr class="task-table__row">
                        <th class="task-table__header">task</th>
                        <th class="task-table__header">Category</th>
                        <th class="task-table__header">Priority</th>
                        <th class="task-table__header"></th>
                        <th class="task-table__header"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($tasks as $task)
                        <tr class="task-table__row">
                            <td class="task-table__item">{{ $task->title }}</td>
                            <td class="task-table__item">{{ $task->category->name }}</td>
                            <td class="task-table__item">{{ $task->priority }}</td>

                            <td class="task-table__item">
                                <div class="task-table__detail">
                                    <a href="{{ route('tasks.show', $task) }}" class="task-table__detail-button">detail</a>
                                </div>
                            </td>
                            <td class="task-table__item">
                                <a href="{{ route('tasks.edit', $task) }}" class="task-table__update-button">edit</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="task-table__empty">
                                タスクがありません。「Add Task」ボタンからタスクを追加してください。
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
