@extends('layouts.app')
@section('css')
    <link rel="stylesheet" href="{{ asset('css/tasks/create.css') }}">
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
                <h1>Task Add</h1>
                <a href="{{ route('tasks.index') }}" class="section__button-back">
                    ← 一覧に戻る
                </a>
            </div>
        </div>
        <form action="{{ route('tasks.store') }}" method="post" class="add-form">
            @csrf
            <div class="add-form__content">
                <div class="add-form__item">
                    <label for="title" class="add-form__label">タイトル</label>
                    <input type="text" id="title" name="title" value="{{ old('title') }}"class="add-form__input">
                </div>
                <div class="add-form__item">
                    <select name="category_id" class="add-form__select">
                        <option value="">category</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="add-form__item">
                    <select name="priority_id" class="add-form__select">
                        <option value="">priority</option>
                        @foreach ($priorities as $priority)
                            <option value="{{ $priority->id }}">{{ $priority->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="add-form__button">
                <button class="add-form__button--submit" type="submit">Add</button>
            </div>
        </form>
    </div>
@endsection
