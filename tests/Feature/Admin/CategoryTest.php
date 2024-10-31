<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\Category;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    
    use RefreshDatabase;

    // カテゴリ一覧ページ
    public function test_guest_user_cannot_access_admin_categories_index()
    {
        $response = $this->get('/admin/categories');
        $response->assertRedirect('/admin/login');
    }

    public function test_regular_user_cannot_access_admin_categories_index()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/categories');
        $response->assertRedirect('/admin/login');
    }

    public function test_admin_user_can_access_admin_categories_index()
    {
        $admin = Admin::factory()->create();
    
        $response = $this->actingAs($admin, 'admin')->get('/admin/categories');
        $response->assertStatus(200);
    }

    // カテゴリ登録機能
    public function test_guest_user_cannot_store_category()
    {
        $response = $this->post('/admin/categories', [
            'name' => 'New Category',
        ]);
        $response->assertRedirect('/admin/login');
    }

    public function test_regular_user_cannot_store_category()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->post('/admin/categories', [
            'name' => 'New Category',
        ]);
        $response->assertRedirect('/admin/login');
    }

    public function test_admin_user_can_store_category()
    {
        $admin = Admin::factory()->create();

        $response = $this->actingAs($admin, 'admin')->post('/admin/categories', [
            'name' => 'New Category',
        ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('categories', [
            'name' => 'New Category',
        ]);
    }


    // カテゴリ更新機能
    public function test_guest_user_cannot_update_category()
    {
        $category = Category::factory()->create();
        $response = $this->put("/admin/categories/{$category->id}", [
            'name' => 'Updated Category',
        ]);
        $response->assertRedirect('/admin/login');
    }

    public function test_regular_user_cannot_update_category()
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $response = $this->actingAs($user)->put("/admin/categories/{$category->id}", [
            'name' => 'Updated Category',
        ]);
        $response->assertRedirect('/admin/login');
    }

    public function test_admin_user_can_update_category()
    {
        $admin = Admin::factory()->create();
        $category = Category::factory()->create([
            'name' => 'Old Category',
        ]);

        $response = $this->actingAs($admin, 'admin')->put("/admin/categories/{$category->id}", [
            'name' => 'Updated Category',
        ]);
        $response->assertStatus(302);
        $this->assertDatabaseHas('categories', [
            'name' => 'Updated Category',
        ]);
    }

    // カテゴリ削除機能
    public function test_guest_user_cannot_delete_category()
    {
        $category = Category::factory()->create();
        $response = $this->delete("/admin/categories/{$category->id}");
        $response->assertRedirect('/admin/login');
    }

    public function test_regular_user_cannot_delete_category()
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $response = $this->actingAs($user)->delete("/admin/categories/{$category->id}");
        $response->assertRedirect('/admin/login');
    }

    public function test_admin_user_can_delete_category()
    {
        $admin = Admin::factory()->create();
        $category = Category::factory()->create();

        $response = $this->actingAs($admin, 'admin')->delete("/admin/categories/{$category->id}");
        $response->assertStatus(302);
        $this->assertDatabaseMissing('categories', [
            'id' => $category->id,
        ]);
    }
}