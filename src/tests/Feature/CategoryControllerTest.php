<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CategoryControllerTest extends TestCase
{
    use RefreshDatabase;// ← テストのたびにDBをリセットする

    /** @test
     */
    public function ユーザーはカテゴリを一覧取得できる(): void
    {
        $user = User::factory()->create();
        Category::factory()->count(3)->create();

        // $userとしてログインした状態でGETリクエストを送る
        $response = $this->actingAs($user)->get(route('categories.index'));

        // ステータスコードが200（正常）か確認
        $response->assertStatus(200);
        // Viewに'categories'という変数が渡されてるか確認
        $response->assertViewHas('categories');
    }

    /** @test */
    public function ユーザーはカテゴリー詳細を取得できる(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();

        $response = $this->actingAs($user)->get(route('categories.show', $category));

        $response->assertStatus(200);
        $response->assertViewHas('category');
    }

    public function ユーザーはカテゴリ追加画面を取得できる(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('categories.create'));

        $response->assertStatus(200);
    }

    /** @test */
    public function ユーザーはカテゴリを追加できる():void
    {
        $user = User::factory()->create(); //ユーザーひとり作る

        //そのユーザーとして'name' => 'テストカテゴリー',をpostで追加
        $response = $this->actingAs($user)->post(route('categories.store'), [
            'name' => 'テストカテゴリー',
        ]);

        //indexにリダイレクトするか、カテゴリーテーブルに追加されてるか
        $response->assertRedirect(route('categories.index'));
        $this->assertDatabaseHas('categories', [
            'name' => 'テストカテゴリー',
        ]);
    }

    /** @test */
    public function カテゴリーが空だとバリデーションエラーになる():void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('categories.store'), [
            'name' => '',
        ]);

        $response->assertSessionHasErrors('name');
    }

    /** @test */
    public function カテゴリー名は255文字以内まで入力できる():void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('categories.store'), [
            'name' => str_repeat('あ', 255),
        ]);

        $response->assertRedirect(route('categories.index'));
        $this->assertDatabaseHas('categories', [
            'name' => str_repeat('あ', 255),
        ]);
    }

    /** @test */
    public function カテゴリー名が256文字以上だとバリデーションエラーになる(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('categories.store', [
            'name' => str_repeat('あ', 256),
        ]));

        $response->assertSessionHasErrors('name');
    }

    /** @test */
    public function ユーザーはカテゴリー編集画面を取得できる() : void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();

        $response = $this->actingAs($user)->get(route('categories.edit', $category));

        $response->assertStatus(200);
        $response->assertViewHas('category');
    }

    /** @test */
    public function ユーザーはカテゴリーを更新できる(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();

        $response = $this->actingAs($user)->put(route('categories.update', $category), [
            'name' => 'テスト',
        ]);

        $response->assertRedirect(route('categories.index'));
        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'テスト',
        ]);
    }

    /** @test */
    public function ユーザーはカテゴリーを削除できる(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();

        $response = $this->actingAs($user)->delete(route('categories.destroy', $category));

        $response->assertRedirect(route('categories.index'));
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    /** @test */
    public function タスクが紐づいているカテゴリーは削除できない(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        Task::factory()->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
        ]);

        $response = $this->actingAs($user)->delete(route('categories.destroy', $category));

        $response->assertRedirect(route('categories.index'));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('categories', ['id' => $category->id]);
    }
}
