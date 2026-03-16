@extends('layouts.app')
@section('css')
    <link rel="stylesheet" href="{{ asset('css/tasks/edit.css') }}">
@endsection
@section('content')
    @if ($errors->any())
        <div class="task__error">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <div class="login-name">
        @auth
            <h2>{{ auth()->user()->name }}さん ログイン中</h2>
        @endauth
    </div>
    <div class="task__content">
        <div class="task__section">
            <div class="section__title">
                <h1>Task Edit</h1>
                <a href="{{ route('tasks.index') }}" class="section__button-back">
                    ← 一覧に戻る
                </a>
            </div>
        </div>
        <form action="{{ route('tasks.update', $task) }}" method="post" class="edit-form">
            @csrf
            @method('PATCH')
            <div class="edit-form__content">
                <div class="edit-form__item">
                    <input type="text" name="title" value="{{ old('title', $task->title) }}">
                </div>
                <div class="edit-form__item">
                    <select name="category_id">
                        <option value="">category</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}"
                                {{ old('category_id', $task->category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="edit-form__item">
                    <select name="priority_id">
                        <option value="">priority</option>
                        @foreach ($priorities as $priority)
                            <option
                                value="{{ $priority->id }}"{{ old('priority_id', $task->priority_id) == $priority->id ? 'selected' : '' }}>
                                {{ $priority->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="edit-form__button">
                <button class="edit-form__button--submit" type="submit">edit</button>
            </div>
        </form>
    </div>
@endsection
