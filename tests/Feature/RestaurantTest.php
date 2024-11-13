<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Http\Controllers\RestaurantController;
use Tests\TestCase;
use App\Models\User;
use App\Models\Admin;
use App\Models\Restaurant;
use App\Models\Category;

class RestaurantTest extends TestCase
{
    public function test_guest_can_access_restaurant_index_page()
    {
        $response = $this->get('/restaurants');

        $response->assertOk();
    }

    public function test_authenticated_user_can_access_restaurant_index_page()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->get('/restaurants');

        $response->assertOk();
    }

    public function test_authenticated_admin_cannot_access_restaurant_index_page()
    {
        $admin = Admin::factory()->create();

        $this->actingAs($admin, 'admin');

        $response = $this->get('/restaurants');

        $response->assertRedirect(route('admin.home'));
    }

    public function test_guest_can_access_restaurant_show_page()
    {
        $restaurant = Restaurant::factory()->create();
        $response = $this->get(route('restaurants.show', $restaurant));
        $response->assertOk();
    }

    public function test_authenticated_user_can_access_restaurant_show_page()
    {
        $user = User::factory()->create();
        $restaurant = Restaurant::factory()->create();
        $response = $this->actingAs($user)->get(route('restaurants.show', $restaurant));
        $response->assertOk();
    }
    
    public function test_admin_cannot_access_restaurant_show_page()
    {
        $admin = Admin::factory()->create();
        $this->actingAs($admin, 'admin');
        $response = $this->get('/restaurants'); 
        $response->assertRedirect(route('admin.home'));
    }
 }