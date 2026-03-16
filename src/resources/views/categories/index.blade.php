@extends('layouts.app')
@section('css')
    <link rel="stylesheet" href="{{ asset('css/categories/index.css') }}">
@endsection
@section('content')
    <div class="category__content">
        <div class="category__section">
            <div class="section__title">
                <h1>カテゴリ一覧</h1>
            </div>
            <div class="section__button">
                <a href="{{ route('categories.create') }}" class="section__button-add">Category Add</a>
            </div>
        </div>

        <div class="category-table">
            <table class="category-table__inner">
                <thead>
                    <tr class="category-table__row">
                        <th class="category-table__header">カテゴリ名</th>
                        <th class="category-table__header">タスク件数</th>
                        <th class="category-table__header"></th>
                        <th class="category-table__header"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($categories as $category)
                        <tr class="category-table__row">
                            <td class="category-table__item">{{ $category->name }}</td>
                            <td class="category-table__item">
                                <span class="category-table__count">{{ $category->tasks_count }}件</span>
                            </td>
                            <td class="category-table__item">
                                <a href="{{ route('categories.show', $category) }}" class="category-table__detail-button">detail</a>
                            </td>
                            <td class="category-table__item">
                                <a href="{{ route('categories.edit', $category) }}" class="category-table__update-button">edit</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="category-table__empty">
                                カテゴリがありません。「Category Add」ボタンからカテゴリを追加してください。
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
