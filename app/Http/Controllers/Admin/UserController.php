<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $keyword = $request->input('keyword');
        
        $query = User::query();
    
        if ($keyword) {
            $query->where(function($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                  ->orWhere('kana', 'like', "%{$keyword}%"); // フリガナのカラム名を適宜変更
            });
        }
    
        $users = $query->paginate(10);
        $total = $users->total(); // 総数を取得
    
        return view('admin.users.index', compact('users', 'keyword', 'total'));
    }
    public function show(User $user)
{
    // ユーザーの詳細を表示するビューを返す
    return view('admin.users.show', compact('user'));
}
}
