@extends('layouts.app')
@section('css')
    <link rel="stylesheet" href="{{ asset('css/auth/register.css') }}">
@endsection
@section('content')
    <div class="register-form__content">
        <div class="register-form__heading">
            <h2 class="register-form__heading-ttl">新規登録</h2>
        </div>
        <form action="{{ route('register') }}" method="POST" class="form">
            @csrf
            <div class="form__group">
                <div class="form__group-title">
                    <span class="form__label-item">名前</span>
                    <span class="form__label--required">必須</span>
                </div>
                <div class="form__group-content">
                    <input type="text" name="name" value="{{ old('name') }}" class="form__input">
                </div>
                <div class="form__error">
                    @error('name')
                        {{ $message }}
                    @enderror
                </div>
            </div>
            <div class="form__group">
                <div class="form__group-title">
                    <span class="form__label-item">メールアドレス</span>
                    <span class="form__label--required">必須</span>
                </div>
                <div class="form__group-content">
                    <input type="email" name="email" value="{{ old('email') }}" class="form__input">
                </div>
                <div class="form__error">
                    @error('email')
                        {{ $message }}
                    @enderror
                </div>
            </div>
            <div class="form__group">
                <div class="form__group-title">
                    <span class="form__label-item">パスワード</span>
                    <span class="form__label--required">必須</span>
                </div>
                <div class="form__group-content">
                    <div class="form__input-text">
                        <input type="password" name="password" class="form__input" />
                    </div>
                    <div class="form__error">
                        @error('password')
                            {{ $message }}
                        @enderror
                    </div>
                </div>
            </div>
            <div class="form__group">
                <div class="form__group-title">
                    <span class="form__label-item">確認用パスワード</span>
                </div>
                <div class="form__group-content">
                    <div class="form__input-text">
                        <input type="password" name="password_confirmation" class="form__input" />
                    </div>
                </div>
            </div>
            <div class="form__button">
                <button class="form__button-submit">登録</button>
            </div>
        </form>
    </div>
@endsection
