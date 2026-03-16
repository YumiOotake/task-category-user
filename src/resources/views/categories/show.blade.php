@extends('layouts.app')
@section('css')
    <link rel="stylesheet" href="{{ asset('css/categories/show.css') }}">
@endsection
@section('content')
    <div class="category__content">
        <div class="category__section">
            <div class="section__title">
                <h1>{{ $category->name }}</h1>
            </div>
            <div class="section__button">
                <a href="{{ route('categories.index') }}" class="section__button-back">← 一覧に戻る</a>
            </div>
        </div>

        <div class="category-detail">
            <div class="category-detail__summary">
                <span class="category-detail__label">タスク件数</span>
                <span class="category-detail__count">{{ $category->tasks->count() }}件</span>
            </div>
        </div>

        <div class="category-task-table">
            <table class="category-task-table__inner">
                <thead>
                    <tr class="category-task-table__row">
                        <th class="category-task-table__header">タイトル</th>
                        <th class="category-task-table__header">優先度</th>
                        <th class="category-task-table__header">詳細</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($category->tasks as $task)
                        <tr class="category-task-table__row">
                            <td class="category-task-table__item">{{ $task->title }}</td>
                            <td class="category-task-table__item">
                                <span class="category-task-table__priority category-task-table__priority--{{ $task->priority->slug ?? 'default' }}">
                                    {{ $task->priority->name ?? '未設定' }}
                                </span>
                            </td>
                            <td class="category-task-table__item">
                                <a href="{{ route('tasks.show', $task) }}" class="category-task-table__detail-button">detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="category-task-table__empty">
                                このカテゴリにタスクはありません。
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="category-detail__button">
            <a href="{{ route('categories.edit', $category) }}" class="category-detail__button--edit">編集</a>
            <form action="{{ route('categories.destroy', $category) }}" method="POST"
                onsubmit="return confirm('このカテゴリを削除しますか？');">
                @csrf
                @method('DELETE')
                <button type="submit" class="category-detail__button--delete">削除</button>
            </form>
        </div>
    </div>
@endsection
