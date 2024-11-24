<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User; 

class UserSeeder extends Seeder
{
    // /**
    //  * Run the database seeds.
    //  */
    // public function run(): void
    // {
    //     User::factory()->count(10)->create();
    // }

    public function run()
    {
        // 1から10までのユーザーを作成
        foreach (range(1, 10) as $id) {
            User::factory()->create([
                'id' => $id, // idを1から10に設定
                'name' => 'User ' . $id, // ユーザー名
                'email' => 'user' . $id . '@example.com', // メールアドレス
            ]);
        }
    }
}
