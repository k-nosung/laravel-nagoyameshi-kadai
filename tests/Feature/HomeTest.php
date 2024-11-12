<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class HomeTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_guest_can_access_home_index()
    {
        $response = $this->get(route('home'));
        $response->assertStatus(200);
    }
    public function test_user_can_access_admin_home_index()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get(route('home'));
        $response->assertStatus(200);
    }
    public function test_admin_cannot_access_home_index()
    {
        $admin = Admin::factory()->create();
        $response =  $this->actingAs($admin, 'admin')->get('/');
        $response->assertRedirect('/admin/home');
    }
}
