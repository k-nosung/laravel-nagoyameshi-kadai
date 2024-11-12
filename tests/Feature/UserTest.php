<?php
namespace Tests\Feature;
use App\Models\User;
use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;
class UserTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');
        $response->assertOk();
    }
    // 1.indexアクション（会員情報ページ）
    // 未ログインのユーザーは会員側の会員情報ページにアクセスできない
    public function test_guest_cannot_access_user_index()
   {
       $response = $this->get(route('user.index'));
       $response->assertRedirect(route('login'));
   }
  
    
    // ログイン済みの一般ユーザーは会員側の会員情報ページにアクセスできる
    public function test_user_can_access_user_index()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $response = $this->get(route('user.index'));
        $response->assertOk();
    }
    // ログイン済みの管理者は会員側の会員情報ページにアクセスできない
    
    public function test_admin_cannot_access_admin_user_index()
    {   
        $admin = Admin::factory()->create();
        $this->actingAs($admin, 'admin');
        $user = User::factory()->create();
        $response = $this->get(route('user.index'));
        $response->assertRedirect(route('admin.home'));
    }
    // 2.editアクション（会員情報編集ページ）
    // 未ログインのユーザーは会員側の会員情報編集ページにアクセスできない
    public function test_guest_cannot_access_user_edit()
    {
        $user = User::factory()->create();
        $response = $this->get(route('user.edit', $user));
        $response->assertRedirect(route('login'));
    }
    
    
    // ログイン済みの一般ユーザーは会員側の他人の会員情報編集ページにアクセスできない
    public function test_user_cannot_access_other_user_edit()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $other_user = User::factory()->create();
        $response = $this->get(route('user.edit', $other_user));
        $response->assertRedirect(route('user.index'));
    }
    // ログイン済みの一般ユーザーは会員側の自身の会員情報編集ページにアクセスできる
    public function test_user_can_access_user_edit()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $response = $this->get(route('user.edit', $user));
        $response->assertOk();
    }
    // ログイン済みの管理者は会員側の会員情報編集ページにアクセスできない
    public function test_admin_cannot_access_admin_user_edit()
    {
        $admin = Admin::factory()->create();
        $this->actingAs($admin, 'admin');
        $user = User::factory()->create();
        $response = $this->get(route('user.edit', $user));
        $response->assertRedirect(route('admin.home'));
    }
    
    // 3.updateアクション（会員情報更新機能）
    // 未ログインのユーザーは会員情報を更新できない
    public function test_guest_cannot_update_user()
    {
        $user = User::factory()->create();
        $response = $this->put(route('user.update', $user), [
            'name' => '更新テスト',
        ]);
        $response->assertRedirect(route('login'));
    }
    // ログイン済みの一般ユーザーは他人の会員情報を更新できない
    public function test_user_cannot_update_other_user()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $other_user = User::factory()->create();
        $user->name = '更新テスト';
        $response = $this->put(route('user.update', $other_user), $user->toArray());
        $response->assertRedirect(route('user.index'));
    }
    // ログイン済みの一般ユーザーは自身の会員情報を更新できる
    public function test_user_can_update_user()
    {
        $user = User::factory()->create([
            'phone_number' => '080-1234-5678'  // 明示的に電話番号を設定
        ]);
    
        // ログインした状態でリクエストを送る
        $this->actingAs($user);
    
        // 名前を更新
        $user->name = '更新テスト';
        $user->phone_number = '08012345678';  // 明示的に電話番号を設定
    
        // 更新をリクエスト
        $response = $this->put(route('user.update', $user), $user->toArray());
    
        // リダイレクトがユーザー一覧ページへ行くことを確認
        $response->assertRedirect(route('user.index'));
    
        // データベースに更新された内容が反映されているか確認
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => '更新テスト',  // 更新した名前を確認
            'phone_number' => '08012345678'  // 設定した電話番号を確認
        ]);
        
    }
    // ログイン済みの管理者は会員情報を更新できない
    public function test_admin_cannot_update_user()
    {
        $admin = Admin::factory()->create();
        $this->actingAs($admin, 'admin');
        $user = User::factory()->create();
        $user->name = '更新テスト';
        $response = $this->put(route('user.update', $user), $user->toArray());
        $response->assertRedirect(route('admin.home'));
    }
}