@extends('layouts.app')
@section('css')
    <link rel="stylesheet" href="{{ asset('css/categories/edit.css') }}">
@endsection
@section('content')
    @if ($errors->any())
        <div class="category__error">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="category__content">
        <div class="category__section">
            <div class="section__title">
                <h1>カテゴリ編集</h1>
                <a href="{{ route('categories.index') }}" class="section__button-back">← 一覧に戻る</a>
            </div>
        </div>

        <form action="{{ route('categories.update', $category) }}" method="POST" class="edit-form">
            @csrf
            @method('PATCH')
            <div class="edit-form__content">
                <div class="edit-form__item">
                    <label for="name" class="edit-form__label">カテゴリ名</label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="{{ old('name', $category->name) }}"
                        class="edit-form__input"
                    >
                </div>
            </div>
            <div class="edit-form__button">
                <button type="submit" class="edit-form__button--submit">更新</button>
            </div>
        </form>
    </div>
@endsection
