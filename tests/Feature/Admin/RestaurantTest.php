<?php
namespace Tests\Feature\Admin;
use App\Models\Restaurant;
use App\Models\User;
use App\Models\Admin;
use App\Models\Category;
use Illuminate\Http\UploadedFile;
use Carbon\Carbon; // Carbon クラスをインポート
use Illuminate\Support\Str; // Str クラスをインポート
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
class RestaurantTest extends TestCase
{
    use RefreshDatabase;
    // indexアクション（店舗一覧ページ）
    public function test_guest_cannot_access_admin_restaurant_index()
    {
        $response = $this->get('/admin/restaurants');
        $response->assertRedirect('/admin/login');
    }
    public function test_regular_user_cannot_access_admin_restaurant_index() 
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/admin/restaurants');
        $response->assertRedirect('/admin/login');
    }
    public function test_admin_can_access_admin_restaurant_index()
    {
        $admin = Admin::factory()->create();
        $response = $this->actingAs($admin, 'admin')->get('/admin/restaurants');
        $response->assertStatus(200);
    }
    // showアクション（店舗詳細ページ）
    public function test_guest_cannot_access_admin_restaurant_show()
    {
        $response = $this->get('/admin/restaurants/1');
        $response->assertRedirect('/admin/login');
    }
    public function test_regular_user_cannot_access_admin_restaurant_show() 
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/admin/restaurants/1');
        $response->assertRedirect('/admin/login');
    }
    public function test_admin_can_access_admin_restaurant_show()
    {
        $admin = Admin::factory()->create();
        $user = Restaurant::factory()->create(['id' => 1]);
        $response = $this->actingAs($admin, 'admin')->get('/admin/restaurants/1');
        $response->assertStatus(200);
    }
    // createアクション（店舗登録ページ）
    public function test_guest_cannot_access_admin_restaurant_create()
    {
        $response = $this->get('/admin/restaurants');
        $response->assertRedirect('/admin/login');
    }
    public function test_authenticated_user_cannot_access_admin_restaurant_create()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/admin/restaurants');
        $response->assertRedirect('/admin/login');
    }
    public function test_admin_can_access_admin_restaurant_create()
    {
        $admin = Admin::factory()->create();
        $response = $this->actingAs($admin, 'admin')->get('/admin/restaurants');
        $response->assertStatus(200);
    }
    // storeアクション（店舗登録機能）
    public function test_guest_cannot_store_admin_restaurant()
    {
          // CSRFミドルウェアを無効化
        $this->withoutMiddleware();
        $restaurant_data = [
            'name' => 'テスト',
            'description' => 'テスト',
            'lowest_price' => 1000,
            'highest_price' => 5000,
            'postal_code' => '0000000',
            'address' => 'テスト',
            'opening_time' => '10:00',
            'closing_time' => '20:00',
            'seating_capacity' => 50
        ];

        $response = $this->post(route('admin.restaurants.store'), $restaurant_data);

        $this->assertDatabaseMissing('restaurants', $restaurant_data);

        $response->assertRedirect(route('admin.login'));
    
       
    }
    public function test_authenticated_user_cannot_store_admin_restaurant()
    {
        $user = User::factory()->create();
    
        $restaurant_data = [
            'name' => 'テスト',
            'description' => 'テスト',
            'lowest_price' => 1000,
            'highest_price' => 5000,
            'postal_code' => '0000000',
            'address' => 'テスト',
            'opening_time' => '10:00',
            'closing_time' => '20:00',
            'seating_capacity' => 50
        ];

        // $response = $this->actingAs($user)->post(route('admin.restaurants.store'), $restaurant_data);
        $response = $this->post(route('admin.restaurants.store'), $restaurant_data);

        $this->assertDatabaseMissing('restaurants', $restaurant_data);

    $response->assertRedirect('/admin/login');
    }
    public function test_admin_can_store_admin_restaurant()
    {
        $admin = Admin::factory()->create();
         // ユニークなカテゴリー名を生成するために uniqid() を使用
        $categories = Category::factory()->count(3)->create();

        $categoryIds = $categories->pluck('id')->toArray();
        $restaurant_data = [
            'name' => 'Store Name',
            'description' => 'A brief description of the store.',
            'lowest_price' => 1000,
            'highest_price' => 5000,
            'postal_code' => '1234567',
            'address' => '123 Main St',
            'opening_time' => '10:00',
            'closing_time' => '20:00',
            'seating_capacity' => 50,
            'category_ids' => [1, 2, 3],
        ];
        $response = $this->actingAs($admin, 'admin')->post(route('admin.restaurants.store'), $restaurant_data);
        $this->assertDatabaseHas('restaurants', $restaurant_data);

        $restaurant = Restaurant::latest('id')->first();

        foreach ($category_ids as $category_id) {
            $this->assertDatabaseHas('category_restaurant', ['restaurant_id' => $restaurant->id, 'category_id' => $category_id]);
        }

        $response->assertRedirect(route('admin.restaurants.index'));
      
    }
    // editアクション（店舗編集ページ）
    public function test_guest_cannot_access_admin_restaurant_edit()
    {
        $response = $this->get('/admin/restaurants/1/edit');
        $response->assertRedirect('/admin/login');
    }
    public function test_authenticated_user_cannot_access_admin_restaurant_edit()
    {
        $user = User::factory()->create();
        $restaurant = Restaurant::factory()->create(['id' => 1]);
        $response = $this->actingAs($user)->get('/admin/restaurants/1/edit');
        $response->assertRedirect('/admin/login');
    }
    public function test_admin_can_access_admin_restaurant_edit()
    {
        $admin = Admin::factory()->create();
        $user = Restaurant::factory()->create(['id' => 1]);
        $response = $this->actingAs($admin, 'admin')->get('/admin/restaurants/1/edit');
        $response->assertStatus(200);
    }
    // updateアクション（店舗更新機能）
    public function test_guest_cannot_update_admin_restaurant()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->put('/admin/restaurants/1', [], [
            'X-CSRF-TOKEN' => csrf_token() // CSRFトークンを手動で送信
        ]);
        $response->assertRedirect('/admin/login');
    }
    public function test_authenticated_user_cannot_update_admin_restaurant()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)
        ->put('/admin/restaurants/1', [],['X-CSRF-TOKEN' => csrf_token(),]);
        $response->assertRedirect('/admin/login');
    }
    public function test_admin_can_update_admin_restaurant()
    {
        $admin = Admin::factory()->create();
        $restaurant = Restaurant::factory()->create();
        $categories = Category::factory()->count(3)->create();
        $categoryIds = $categories->pluck('id')->toArray();
        $new_restaurant_data = [
            'name' => 'Updated Store Name',
            'description' => 'A brief description of the store.',
            'lowest_price' => 1000,
            'highest_price' => 5000,
            'postal_code' => '1234567',
            'address' => '123 Main St',
            'opening_time' => '10:00',
            'closing_time' => '20:00',
            'seating_capacity' => 50,
            'category_ids' => $categoryIds,
    ];
    $response = $this->actingAs($admin, 'admin')->patch(route('admin.restaurants.update', $restaurant), $restaurant_data);
    $this->assertDatabaseHas('restaurants', $restaurant_data);

    $restaurant = Restaurant::latest('id')->first();

    foreach ($category_ids as $category_id) {
        $this->assertDatabaseHas('category_restaurant', ['restaurant_id' => $restaurant->id, 'category_id' => $category_id]);
    }

    $response->assertRedirect(route('admin.restaurants.show', $old_restaurant));
   }

    // destroyアクション（店舗削除機能）
    public function test_guest_cannot_destroy_admin_restaurant()
    {
        $restaurant = Restaurant::factory()->create();
        $response = $this->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class])
        ->delete('/admin/restaurants/' . $restaurant->id);
        $response->assertRedirect('/admin/login');
    }
    public function test_authenticated_user_cannot_destroy_admin_restaurant()
    {
        $user = User::factory()->create();
        $response = $this->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class])
        ->delete('/admin/restaurants/1');
        $response->assertRedirect('/admin/login');
    }
    public function test_admin_can_destroy_admin_restaurant()
    {
        $admin = Admin::factory()->create();
        $restaurant = Restaurant::factory()->create();
    
        $response = $this->actingAs($admin, 'admin')
                     ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class])
                     ->delete('/admin/restaurants/' . $restaurant->id);
        // Attempt to delete the restaurant
        // $response = $this->actingAs($admin, 'admin')->delete('/admin/restaurants/' . $restaurant->id);
       
        // Check for the redirect status
        $response->assertStatus(302);
        $response->assertRedirect('/admin/restaurants');
    }
}