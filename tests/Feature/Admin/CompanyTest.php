<?php

namespace Tests\Feature\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Http\Controllers\Admin\CompanyController;  // 追加
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Company;

class CompanyTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_admin_company_index()
    {
        $company = Company::factory()->create();

        $response = $this->get(route('admin.company.index'));

        $response->assertRedirect(route('admin.login'));
    }

    public function test_user_cannot_access_admin_company_index()
    {
        $user = User::factory()->create();
        $company = Company::factory()->create();
        $response = $this->actingAs($user)->get(route('admin.company.index'));
        $response->assertRedirect(route('admin.login'));
    }

    public function test_admin_can_access_admin_company_index()
    {
        $admin =Admin::factory()->create();
        $company = Company::factory()->create();
        $response = $this->actingAs($admin, 'admin')->get(route('admin.company.index'));
        $response->assertStatus(200);
    }

    public function test_guest_cannot_access_admin_company_edit()
    {
        $company = Company::factory()->create();
        $response = $this->get(route('admin.company.edit', $company));
        $response->assertRedirect(route('admin.login'));
    }

    public function test_user_cannot_access_admin_company_edit()
    {
        $user = User::factory()->create();
        $company = Company::factory()->create();
        $response = $this->actingAs($user)->get(route('admin.company.edit', $company));
        $response->assertRedirect(route('admin.login'));
    }

    public function test_admin_can_access_admin_company_edit()
    {
        $admin =Admin::factory()->create();
        $company = Company::factory()->create();
        $response = $this->actingAs($admin, 'admin')->get(route('admin.company.edit', $company));
        $response->assertStatus(200);
    }

    public function test_guest_cannot_access_company_update()
    {
            $company = Company::factory()->create();
            $updateData = [
                'name' => 'テスト更新',
                'postal_code' => '0000001',
                'address' => 'テスト',
                'representative' => 'テスト',
                'establishment_date' => 'テスト',
                'capital' => 'テスト',
                'business' => 'テスト',
                'number_of_employees' => 'テスト',
            ];

            $response = $this->patch(route('admin.company.update', $company), $updateData);
            $this->assertDatabaseMissing('companies', $updateData);
            $response->assertRedirect(route('admin.login'));
    }

    public function test_user_cannot_access_admin_company_update()
    {
            $user = User::factory()->create();
            $company = Company::factory()->create();
            $new_company = [
                'name' => 'テスト更新',
                'postal_code' => '0000001',
                'address' => 'テスト',
                'representative' => 'テスト',
                'establishment_date' => 'テスト',
                'capital' => 'テスト',
                'business' => 'テスト',
                'number_of_employees' => 'テスト',
            ];

            $response = $this->actingAs($user)->patch(route('admin.company.update',$company),$new_company);
            
            $this->assertDatabaseMissing('companies', $new_company);

            $response->assertRedirect(route('admin.login'));
            }

    public function test_admin_can_access_admin_company_update()
    {
        $admin =Admin::factory()->create();
        $company = Company::factory()->create();
 
            $new_company = [
                'name' => 'テスト更新',
                'postal_code' => '0000001',
                'address' => 'テスト',
                'representative' => 'テスト',
                'establishment_date' => 'テスト',
                'capital' => 'テスト',
                'business' => 'テスト',
                'number_of_employees' => 'テスト',
            ];

            $company = Company::factory()->create();
            $new_company = Company::factory()->make();

            $data = $new_company->toArray();
            
            $response = $this->actingAs($admin, 'admin')->patch(route('admin.company.update', $company), $data);

            $response->assertRedirect(route('admin.company.index'));

        }
}
