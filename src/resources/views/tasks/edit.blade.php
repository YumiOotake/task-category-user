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
                    <label for="title" class="edit-form__item-label">タイトル</label>
                    <input type="text" name="title" id="title" value="{{ old('title', $task->title) }}">
                </div>
                <div class="edit-form__item">
                    <label for="category" class="edit-form__item-label">カテゴリー</label>
                    <select name="category_id" id="category">
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}"
                                {{ old('category_id', $task->category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="edit-form__item">
                    <label for="priority" class="edit-form__item-label">優先度</label>
                    <select name="priority" id="priority">
                        <option value="1" {{ old('priority', $task->priority) == 1 ? 'selected' : '' }}>低</option>
                        <option value="2" {{ old('priority', $task->priority) == 2 ? 'selected' : '' }}>中</option>
                        <option value="3" {{ old('priority', $task->priority) == 3 ? 'selected' : '' }}>高</option>
                    </select>
                </div>
                <div class="edit-form__item">
                    <label for="description" class="edit-form__item-label">説明</label>
                    <textarea name="description" id="description" rows="5">{{ old('description', $task->description) }}</textarea>
                </div>
            </div>
            <div class="edit-form__button">
                <button class="edit-form__button--submit" type="submit">edit</button>
            </div>
        </form>
    </div>
@endsection
