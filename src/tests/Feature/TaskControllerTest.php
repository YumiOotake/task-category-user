<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use SebastianBergmann\Invoker\TimeoutException;
use Tests\TestCase;

use function PHPUnit\Framework\assertClassHasStaticAttribute;

class TaskControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function ユーザーはタスク一覧を取得できる():void
    {
        $user = User::factory()->create();
        $tasks = Task::factory()->count(3)->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->get(route('tasks.index'));

        $response->assertStatus(200);
        $response->assertViewHas('tasks');
    }

    /** @test */
    public function ユーザーはタスク詳細を取得できる(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->get(route('tasks.show', $task));

        $response->assertStatus(200);
        $response->assertViewHas('task');
    }

    /** @test */
    public function ユーザーはタスク作成画面を表示できる(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('tasks.create'));

        $response->assertStatus(200);
    }

    /** @test */
    public function ユーザーはタスクを作成できる(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();

        $response = $this->actingAs($user)->post(route('tasks.store'), [
            'category_id' => $category->id,
            'title' => 'テスト',
            'priority' => 1,
            'description' => 'テストの説明',
        ]);

        $response->assertRedirect(route('tasks.index'));
        $this->assertDatabaseHas('tasks', [
            'user_id' => $user->id,
            'title' => 'テスト',
        ]);
    }

    /** @test */
    public function タスクタイトルが空だとバリデーションエラーになる(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();

        $response = $this->actingAs($user)->post(route('tasks.store'), [
            'category_id' => $category->id,
            'title' => '',
            'priority' => 2,
        ]);

        $response->assertSessionHasErrors('title');
    }

    /** @test */
    public function 無効な優先度だとバリデーションエラーになる(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();

        $response = $this->actingAs($user)->post(route('tasks.store'), [
            'category_id' => $category->id,
            'title' => 'テスト',
            'priority' => 10,
        ]);

        $response->assertSessionHasErrors('priority');
    }

    /** @test */
    public function タイトルは255文字まで入力できる() : void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();

        $response = $this->actingAs($user)->post(route('tasks.store'), [
            'category_id' => $category->id,
            'title' => str_repeat('あ', 255),
            'priority' => 1,
        ]);

        $response->assertRedirect(route('tasks.index'));
        $this->assertDatabaseHas('tasks', [
            'user_id' => $user->id,
            'title' => str_repeat('あ', 255),
        ]);
    }

    /** @test */
    public function タイトルが256文字以上だとバリデーションエラーになる(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();

        $response = $this->actingAs($user)->post(route('tasks.store'), [
            'category_id' => $category->id,
            'title' => str_repeat('あ', 256),
            'priority' => 1,
        ]);

        $response->assertSessionHasErrors('title');
    }

    /** @test */
    public function ユーザーはタスク編集画面を表示できる(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->get(route('tasks.edit', $task));

        $response->assertStatus(200);
        $response->assertViewHas('task');
    }

    /** @test */
    public function ユーザーはタスクを更新できる(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->create([
            'user_id' => $user->id,
        ]);
        $category = Category::factory()->create();

        $response = $this->actingAs($user)->put(route('tasks.update', $task), [
            'title' => 'テスト編集',
            'priority' => 1,
            'category_id' => $category->id,
        ]);

        $response->assertRedirect(route('tasks.index'));
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'user_id' => $user->id,
            'priority' => 1,
            'title' => 'テスト編集',
        ]);
    }

    /** @test */
    public function ユーザーはタスクを削除できる(): void
    {
        $user = User::factory()->create();
        $task = Task::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->delete(route('tasks.destroy', $task));

        $response->assertRedirect(route('tasks.index'));
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    /** @test */
    public function 他人のタスク詳細にアクセスすると403エラーになる(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $task = Task::factory()->create([
            'user_id' => $otherUser->id,
        ]);

        $response = $this->actingAs($user)->get(route('tasks.show', $task));

        $response->assertForbidden(); //403
    }

    /** @test */
    public function 他人のタスク編集画面にアクセスすると403エラーになる(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $task = Task::factory()->create([
            'user_id' => $otherUser->id,
        ]);

        $response = $this->actingAs($user)->get(route('tasks.edit', $task));

        $response->assertForbidden(); //403
    }

    /** @test */
    public function 他人のタスクを更新しようとすると403エラーになる(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $task = Task::factory()->create([
            'user_id' => $otherUser->id,
        ]);
        $category = Category::factory()->create();

        $response = $this->actingAs($user)->put(route('tasks.update', $task), [
            'title' => '不正な更新',
            'priority' => 1,
            'category_id' => $category->id,
        ]);

        $response->assertForbidden(); //403
    }

    /** @test */
    public function 他人のタスクを削除しようとすると403エラーになる(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $task = Task::factory()->create([
            'user_id' => $otherUser->id,
        ]);

        $response = $this->actingAs($user)->delete(route('tasks.destroy', $task));

        $response->assertForbidden(); //403
    }
}
