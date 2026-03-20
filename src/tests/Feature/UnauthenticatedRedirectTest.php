<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UnauthenticatedRedirectTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 未認証ユーザーはタスク一覧にアクセスするとログインページにリダイレクトされる(): void
    {
        $response = $this->get(route('tasks.index'));

        $response->assertRedirect('login');
    }

    /** @test */
    public function 未認証ユーザーはタスク作成画面にアクセスするとログインページにリダイレクトされる(): void
    {
        $response = $this->get(route('tasks.create'));

        $response->assertRedirect('login');
    }

    /** @test */
    public function 未認証ユーザーはカテゴリー一覧にアクセスするとログインページにリダイレクトされる(): void
    {
        $response = $this->get(route('categories.index'));

        $response->assertRedirect('login');
    }

    /** @test */
    public function 未認証ユーザーはカテゴリー作成画面にアクセスするとログインページにリダイレクトされる(): void
    {
        $response = $this->get(route('categories.create'));

        $response->assertRedirect('login');
    }

    /** @test */
    public function 未ログインユーザーはソートページにアクセスできない()
    {
        $response = $this->get(route('tasks.sort',));

        $response->assertRedirect('login');
    }
}
