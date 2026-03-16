@extends('layouts.app')
@section('css')
    <link rel="stylesheet" href="{{ asset('css/auth/reset.css') }}">
@endsection
@section('content')
    <div class="password-form__content">
        <div class="password-form__heading">
            <h2 class="password-form__heading-ttl">パスワード再設定</h2>
        </div>
        <form method="POST" action="{{ route('password.update') }}" class="form">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <input type="hidden" name="email" value="{{ request()->email }}">
            <div class="form__group">
                <div class="form__group-ttl">
                    <span class="form__label--item">パスワード</span>
                </div>
                <div class="form__group-content">
                    <div class="form__input--text">
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
                <div class="form__group-ttl">
                    <span class="form__label--item">確認用パスワード</span>
                </div>
                <div class="form__group-content">
                    <div class="form__input--text">
                        <input type="password" name="password_confirmation" class="form__input" />
                    </div>
                </div>
            </div>
            <div class="form__button">
                <button class="form__button-submit">パスワード変更</button>
            </div>
        </form>
    </div>
    @endsection
