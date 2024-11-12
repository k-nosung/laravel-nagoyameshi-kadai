<?php
namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
class UserController extends Controller
{
    /**
     * 会員情報ページを表示
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // 現在ログイン中のユーザー情報を取得
        $user = Auth::user();
        // ビューにデータを渡す
        return view('user.index', compact('user'));
    }
    public function edit(User $user)
    {
        // 他人の会員情報を編集できない
        if ($user->id !== Auth::id()) {
            return redirect()->route('user.index')->with('error_message', '不正なアクセスです。');
        }
        // ビューにデータを渡す
        return view('user.edit', compact('user'));
    }
    public function update(Request $request, User $user)
    {
         // 他人の会員情報を更新できないようにする
            if ($user->id !== Auth::id()) {
            return redirect()->route('user.index')->with('error_message', '不正なアクセスです。');
            }
    
            // バリデーション
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'kana' => ['required', 'string', 'max:255', 'regex:/\A[ァ-ヴー\s]+\z/u'],
                'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
                'postal_code' => 'required|digits:7',
                'address' => 'required|string|max:255',
                'phone_number' => 'required|digits_between:10,11',
                'birthday' => 'nullable|digits:8',
                'occupation' => 'nullable|string|max:255',
            ]);
    
            // 会員情報を更新
            $user->update($validated);
            // フラッシュメッセージをセッションに保存
        return redirect()->route('user.index')->with('flash_message', '会員情報を編集しました。');
    }
}