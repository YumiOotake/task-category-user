<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use League\CommonMark\Extension\TaskList\TaskListItemMarker;
use SebastianBergmann\Invoker\TimeoutException;
use Tests\TestCase;

use function PHPUnit\Framework\assertClassHasStaticAttribute;

class TaskControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function ユーザーはタスク一覧を取得できる(): void
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
    public function タイトルは255文字まで入力できる(): void
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

// 検索テスト：「何件返ってきたか」「正しいタスクだけか」を確認したい
// → Viewに渡されたコレクションの中身を見る必要がある
// → assertDatabaseHasでは「件数」や「絞り込まれたか」が確認できない

    /** @test */
    public function キーワードでタスクを検索できる(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        Task::factory()->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'title' => '一致するタスク',
        ]);
        Task::factory()->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'title' => '一致しないタスク',
        ]);

        $response = $this->actingAs($user)->get(route(
            'tasks.search',
            [
                'keyword' => '一致する',
            ],
        ));

        $response->assertStatus(200);
        $response->assertViewHas('tasks', function ($tasks) {
            return $tasks->count() === 1 && $tasks->first()->title === '一致するタスク';
        });
    }

    /** @test */
    public function カテゴリーでタスクを検索できる(): void
    {
        $user = User::factory()->create();
        $category1 = Category::factory()->create();
        $category2 = Category::factory()->create();
        Task::factory()->create([
            'user_id' => $user->id,
            'category_id' => $category1->id,
            'title' => '一致するタスク',
        ]);
        Task::factory()->create([
            'user_id' => $user->id,
            'category_id' => $category2->id,
            'title' => '一致しないタスク',
        ]);

        $response = $this->actingAs($user)->get(route(
            'tasks.search',
            [
                'category_id' => $category1->id,
            ],
        ));

        $response->assertStatus(200);
        $response->assertViewHas('tasks', function ($tasks) use ($category1) {
            return $tasks->count() === 1 && $tasks->first()->category_id === $category1->id;
        });
    }

    /** @test */
    public function キーワードとカテゴリーを組み合わせて検索できる(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        Task::factory()->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'title' => '対象タスク',
        ]);
        Task::factory()->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'title' => '別タスク',
        ]);


        $response = $this->actingAs($user)->get(route('tasks.search', [
            'category_id' => $category->id,
            'keyword' => '対象',
        ]));

        // $response->assertStatus(200);
        $response->assertViewHas('tasks', function ($tasks) {
            return $tasks->count() === 1;
        });
    }

    /** @test */
    public function 検索結果が0件でも正常に表示される(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('tasks.search', [
            'keyword' => '存在しないタスク',
        ]));

        $response->assertStatus(200);
        $response->assertViewHas('tasks', function ($tasks) {
            return $tasks->count() === 0;
        });
    }
    /** @test */
    public function 他人のタスクは検索結果に含まれない(): void
    {
        $user1 = User::factory()->create();
        $otherUser = User::factory()->create();
        $category = Category::factory()->create();
        Task::factory()->create([
            'user_id' => $otherUser->id,
            'category_id' => $category->id,
            'title' => 'タスク',
        ]);


        $response = $this->actingAs($user1)->get(route('tasks.search', [
            'keyword' => 'タスク',
        ]));

        $response->assertStatus(200);
        $response->assertViewHas('tasks', function ($tasks) {
            return $tasks->count() === 0;
        });
    }

    /** @test */
    public function 優先度高い順に並び替えられる(): void
    {
        $user = User::factory()->create();
        $task1 = Task::factory()->create([
            'user_id' => $user->id,
            'priority' => 1,
        ]);
        $task2 = Task::factory()->create([
            'user_id' => $user->id,
            'priority' => 2,
        ]);
        $task3 = Task::factory()->create([
            'user_id' => $user->id,
            'priority' => 3,
        ]);

        $response = $this->actingAs($user)->get(route(
            'tasks.sort',
            ['sort' => 'priority_desc',]
        ));

        $response->assertStatus(200);
        $response->assertViewHas('tasks', function ($tasks) use ($task1, $task2, $task3) {
            return $tasks->pluck('id')->toArray() === [
                $task3->id,
                $task2->id,
                $task1->id
            ];
        });
    }

    /** @test */
    public function 優先度低い順に並び替えられる(): void
    {
        $user = User::factory()->create();
        $task1 = Task::factory()->create([
            'user_id' => $user->id,
            'priority' => 1,
        ]);
        $task2 = Task::factory()->create([
            'user_id' => $user->id,
            'priority' => 2,
        ]);
        $task3 = Task::factory()->create([
            'user_id' => $user->id,
            'priority' => 3,
        ]);

        $response = $this->actingAs($user)->get(route(
            'tasks.sort',
            ['sort' => 'priority_asc',]
        ));

        $response->assertStatus(200);
        $response->assertViewHas('tasks', function ($tasks) use ($task1, $task2, $task3) {
            return $tasks->pluck('id')->toArray() === [
                $task1->id,
                $task2->id,
                $task3->id
            ];
        });
    }

    /** @test */
    public function 新しい順に並び替えられる(): void
    {
        $user = User::factory()->create();
        $task1 = Task::factory()->create([
            'user_id' => $user->id,
            'created_at' => '2026-03-03 00:00:00'
        ]);
        $task2 = Task::factory()->create([
            'user_id' => $user->id,
            'created_at' => '2026-03-02 00:00:00',
        ]);
        $task3 = Task::factory()->create([
            'user_id' => $user->id,
            'created_at' => '2026-03-01 00:00:00',
        ]);

        $response = $this->actingAs($user)->get(route(
            'tasks.sort',
            ['sort' => 'created_desc',]
        ));

        $response->assertStatus(200);
        $response->assertViewHas('tasks', function ($tasks) use ($task1, $task2, $task3) {
            return $tasks->pluck('id')->toArray() === [
                $task1->id,
                $task2->id,
                $task3->id
            ];
        });
    }

    /** @test */
    public function 古い順に並び替えられる(): void
    {
        $user = User::factory()->create();
        $task1 = Task::factory()->create([
            'user_id' => $user->id,
            'created_at' => '2026-03-03 00:00:00'
        ]);
        $task2 = Task::factory()->create([
            'user_id' => $user->id,
            'created_at' => '2026-03-02 00:00:00',
        ]);
        $task3 = Task::factory()->create([
            'user_id' => $user->id,
            'created_at' => '2026-03-01 00:00:00',
        ]);

        $response = $this->actingAs($user)->get(route(
            'tasks.sort',
            ['sort' => 'created_asc',]
        ));

        $response->assertStatus(200);
        $response->assertViewHas('tasks', function ($tasks) use ($task1, $task2, $task3) {
            return $tasks->pluck('id')->toArray() === [
                $task3->id,
                $task2->id,
                $task1->id
            ];
        });
    }

    /** @test */
    public function 並び順未選択（空）の時正常に表示される()
    {
        $user = User::factory()->create();
        $tasks = Task::factory()->count(3)->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->get(route(
            'tasks.sort',
            ['sort' => '',]
        ));

        $response->assertStatus(200);
        $response->assertViewHas('tasks');
    }

    /** @test */
    public function 存在しないsort値が送られても正常に表示される()
    {
        $user = User::factory()->create();
        $tasks = Task::factory()->count(3)->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->get(route(
            'tasks.sort',
            ['sort' => '存在しないsort',]
        ));

        $response->assertStatus(200);
        $response->assertViewHas('tasks');
    }

}
