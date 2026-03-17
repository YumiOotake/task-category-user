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
                    <label for="category" class="add-form__label">カテゴリー</label>
                    <select name="category_id" class="add-form__select" id="category">
                        <option value="">選択してください</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}"{{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="add-form__item">
                    <label for="priority" class="add-form__item-label">優先度</label>
                    <select name="priority" id="priority">
                        <option value="1" {{ old('priority') == 1 ? 'selected' : '' }}>低</option>
                        <option value="2" {{ old('priority') == 2 ? 'selected' : '' }}>中</option>
                        <option value="3" {{ old('priority') == 3 ? 'selected' : '' }}>高</option>
                    </select>
                </div>
                <div class="add-form__item">
                    <label for="description" class="add-form__item-label">説明</label>
                    <textarea name="description" id="description" rows="5" class="add-form__textarea">{{ old('description') }}</textarea>
                </div>
            </div>
            <div class="add-form__button">
                <button class="add-form__button--submit" type="submit">Add</button>
            </div>
        </form>
    </div>
@endsection
