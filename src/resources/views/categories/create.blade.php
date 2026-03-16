@extends('layouts.app')
@section('css')
    <link rel="stylesheet" href="{{ asset('css/categories/create.css') }}">
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
                <h1>カテゴリ追加</h1>
                <a href="{{ route('categories.index') }}" class="section__button-back">← 一覧に戻る</a>
            </div>
        </div>

        <form action="{{ route('categories.store') }}" method="POST" class="add-form">
            @csrf
            <div class="add-form__content">
                <div class="add-form__item">
                    <label for="name" class="add-form__label">カテゴリ名</label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="{{ old('name') }}"
                        class="add-form__input"
                    >
                </div>
            </div>
            <div class="add-form__button">
                <button type="submit" class="add-form__button--submit">Add</button>
            </div>
        </form>
    </div>
@endsection
