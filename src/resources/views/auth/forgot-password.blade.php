@extends('layouts.app')
@section('css')
    <link rel="stylesheet" href="{{ asset('css/auth/forgot.css') }}">
@endsection
@section('content')
    <div class="password-form__content">
        <div class="password-form__heading">
            <h2 class="password-form__heading-ttl">パスワードお忘れの方</h2>
        </div>
        <form method="POST" action="{{ route('password.email') }}" class="form">
            @csrf
            <div class="form__group">
                <div class="form__group-title">
                    <span class="form__label-item">メールアドレス</span>
                </div>
                <div class="form__group-content">
                    <div class="form__input-text">
                        <input type="email" name="email" class="form__input">
                    </div>
                    <div class="form__error">
                        @error('email')
                            {{ $message }}
                        @enderror
                    </div>
                </div>
            </div>
            <div class="form__button">
                <button type="submit" class="form__button-submit">送信</button>
            </div>
        </form>
    </div>
@endsection
