@extends('layouts.app')
@section('css')
    <link rel="stylesheet" href="{{ asset('css/tasks/index.css') }}">
@endsection
@section('content')
    <div class="task__content">
        <div class="task__content-header">
            <div class="login-name">
                @auth
                    <h2>{{ auth()->user()->name }}さん ログイン中</h2>
                @endauth
            </div>
            <form action="{{ route('tasks.sort') }}" method="GET" class="sort-form">
                <div class="sort-form__item">
                    <div class="sort-form__item-select">
                        <select name="sort">
                            <option value="">並び順選択</option>
                            <option value="priority_desc" {{ request('sort') === 'priority_desc' ? 'selected' : '' }}>優先度高い順
                            </option>
                            <option value="priority_asc" {{ request('sort') === 'priority_asc' ? 'selected' : '' }}>優先度低い順
                            </option>
                            <option value="created_desc" {{ request('sort') === 'created_desc' ? 'selected' : '' }}>新しい順
                            </option>
                            <option value="created_asc" {{ request('sort') === 'created_asc' ? 'selected' : '' }}>古い順
                            </option>
                        </select>
                    </div>
                </div>
                <div class="sort-form__button">
                    <button class="sort-form__button--submit" type="submit">Sort</button>
                </div>
            </form>
        </div>

        <form class="search-form" action="{{ route('tasks.search') }}" method="get">
            <div class="search-form__content">
                <div class="search-form__item">
                    <input type="text" name="keyword" class="search-form__item-input" placeholder="検索ワード"
                        value="{{ request('keyword') }}">
                </div>
                <div class="search-form__item">
                    <select name="category_id" class="search-form__item-input">
                        <option value="">category選択</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}"
                                {{ request('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="search-form__button">
                    <button class="search-form__button--submit" type="submit">Search</button>
                </div>
            </div>
        </form>
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
                            <td class="task-table__item">
                                @php
                                    $priorityClass = match ($task->priority) {
                                        1 => 'low',
                                        2 => 'mid',
                                        3 => 'high',
                                        default => 'mid',
                                    };
                                @endphp
                                <span class="priority-badge priority-badge--{{ $priorityClass }}">
                                    {{ $task->priority_label }}
                                </span>
                            </td>
                            <td class="task-table__item">
                                <div class="task-table__detail">
                                    <a href="{{ route('tasks.show', $task) }}" class="task-table__button">detail</a>
                                </div>
                            </td>
                            <td class="task-table__item">
                                <a href="{{ route('tasks.edit', $task) }}" class="task-table__button">edit</a>
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
        <div class="paginate">
            {{ $tasks->appends(request()->query())->links() }}
        </div>
    </div>
@endsection
