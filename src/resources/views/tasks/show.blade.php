@extends('layouts.app')
@section('css')
    <link rel="stylesheet" href="{{ asset('css/tasks/show.css') }}">
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
                <h1>Task詳細</h1>
            </div>
            <div class="section__button">
                <a href="{{ route('tasks.index') }}" class="section__button-back">
                    ← 一覧に戻る
                </a>
            </div>
        </div>
        <div class="task-detail">
            <div class="task-detail__item">
                <span class="task-detail__title">タイトル</span>
                <p class="task-detail__text">{{ $task->title }}</p>
            </div>
            <div class="task-detail__item">
                <span class="task-detail__title">カテゴリー</span>
                <p class="task-detail__text">{{ $task->category->name ?? '未分類' }}</p>
            </div>

            <div class="task-detail__item">
                <span class="task-detail__title">優先度</span>
                <div class="task-detail__text">
                    <span class="task-detail__text-span">高
                        @if ($task->priority === 3)
                            bg-red-100 text-red-800
                        @elseif($task->priority === 2)
                            bg-yellow-100 text-yellow-800
                        @else
                            bg-green-100 text-green-800
                            @endif">
                            @if ($task->priority === 3)
                                高
                            @elseif($task->priority === 2)
                                中
                            @else
                                低
                            @endif あとで実装する
                    </span>
                </div>
            </div>

            <div class="task-detail__item">
                <span class="task-detail__title">説明</span>
                <p class="task-detail__text">{{ $task->description ?? '説明はありません' }}</p>
            </div>

            <div class="task-detail__item">
                <span class="task-detail__title">登録日</span>
                <p class="task-detail__text">{{ $task->created_at->format('Y年m月d日') }}</p>
            </div>
        </div>

        <div class="task-detail__button">
            @can('update', $task)
                <a href="{{ route('tasks.edit', $task) }}" class="task-detail__button--edit">
                    編集
                </a>
            @endcan
            @can('delete', $task)
                <form action="{{ route('tasks.destroy', $task) }}" method="POST" onsubmit="return confirm('本当に削除しますか？');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="task-detail__button--delete">
                        削除
                    </button>
                </form>
            @endcan

        </div>
    </div>
@endsection
