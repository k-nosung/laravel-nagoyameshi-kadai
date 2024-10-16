<?php

namespace Tests\Feature\Feature\Admin;
use App\Models\User;
use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;
    // 会員一覧ページのテスト
    public function testAccessMemberListPage_UnauthenticatedUser_ShouldBeRedirected()
    {
        $response = $this->get('/admin/home');
        $response->assertRedirect('/admin/login'); // 未認証ユーザーはログインページにリダイレクトされる前提
    }

  public function testAccessMemberListPage_AuthenticatedUser_ShouldBeForbidden()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/admin/home');
        $response->assertRedirect('/admin/login');
    }

    public function testAccessMemberListPage_Admin_ShouldBeOk()
    {
        $admin = Admin::factory()->create();
        $response = $this->actingAs($admin, 'admin')->get('/admin/home');
        $response->assertOk();
    }

    // 会員詳細ページのテスト
    public function testAccessMemberDetailPage_UnauthenticatedUser_ShouldBeRedirected()
    {
        $user = User::factory()->create();
        $response = $this->get(route('admin.users.show', $user));
        $response->assertRedirect('/admin/login'); // 未認証ユーザーはログインページにリダイレクトされる前提
    }

    public function testAccessMemberDetailPage_AuthenticatedUser_ShouldBeForbidden()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get(route('admin.users.show', $user));
        $response->assertRedirect('/admin/login');
    }

    public function testAccessMemberDetailPage_Admin_ShouldBeOk()
    {
        $admin = Admin::factory()->create();
        $user = User::factory()->create(); // ユーザーを作成
        $response = $this->actingAs($admin, 'admin')->get(route('admin.users.show', $user));
        $response->assertOk();
    }
}
