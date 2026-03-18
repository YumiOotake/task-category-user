<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PhpParser\Lexer\TokenEmulator\TokenEmulator;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 登録画面を表示できる(): void
    {
        $response = $this->get(route('register'));

        $response->assertStatus(200);
    }

    /** @test */
    public function 新規ユーザーを登録できる(): void
    {

        $response = $this->post(route('register'), [
            'name' => 'テスト',
            'email' => 'test@test.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('tasks.index'));
        $this->assertDatabaseHas('users', [
            'name' => 'テスト',
            'email' => 'test@test.com',
        ]);
        $this->assertAuthenticated();
    }

    /** @test */
    public function 名前が空だとバリデーションエラーになる(): void
    {
        $response = $this->post(route('register'), [
            'name' => '',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors('name');
        $this->assertGuest();
    }

    /** @test */
    public function メールアドレスが空だとバリデーションエラーになる(): void
    {
        $response = $this->post(route('register'), [
            'name' => 'テスト',
            'email' => '',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    /** @test */
    public function 無効なメールアドレス形式だとバリデーションエラーになる(): void
    {
        $response = $this->post(route('register'), [
            'name' => 'テスト',
            'email' => 'invalid-email',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    /** @test */
    public function 既に登録済みのメールアドレスだとバリデーションエラーになる(): void
    {
        $user = User::factory()->create([
            'email' => 'existing@example.com',
        ]);

        $response = $this->post(route('register'), [
            'name' => 'テスト',
            'email' => 'existing@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    /** @test */
    public function パスワードが8文字未満だとバリデーションエラーになる(): void
    {
        $response = $this->post(route('register'), [
            'name' => 'テスト',
            'email' => 'test@example.com',
            'password' => 'short',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors('password');
        $this->assertGuest();
    }
    /** @test */
    public function パスワード確認が一致しないとバリデーションエラーになる(): void
    {
        $response = $this->post(route('register'), [
            'name' => 'テスト',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'different-password',
        ]);

        $response->assertSessionHasErrors('password');
        $this->assertGuest();
    }
}
