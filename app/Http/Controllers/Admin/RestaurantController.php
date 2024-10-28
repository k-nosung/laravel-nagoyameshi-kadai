<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin; 
use App\Models\Restaurant; 
use Illuminate\Http\Request;

class RestaurantController extends Controller
{
  public function test_example()
{
    // 管理者のダミーユーザーを作成または取得
    $admin = Admin::factory()->create();

    // actingAsで認証
    $this->actingAs($admin, 'admin');

    // テスト対象のリクエスト
    $response = $this->get('/admin/restaurants');

    // 期待する結果をアサート
    $response->assertStatus(200);
}
    public function index(Request $request)
  {
    $keyword = $request->input('keyword'); // 検索ボックスのキーワードを取得

    // クエリビルダーを使って、店舗名で部分一致検索を行う
    $query = Restaurant::query();
    
    if ($keyword) {
        $query->where('name', 'like', '%' . $keyword . '%'); // 部分一致検索
    }

    // ページネーションを適用し、1ページあたりの表示件数を10件に設定
    $restaurants = $query->paginate(10);
    $total = $restaurants->total(); // 総数を取得

    return view('admin.restaurants.index', compact('restaurants', 'keyword', 'total'));
  }
public function show(Restaurant $restaurant)
  {
    return view('admin.restaurants.show', compact('restaurant'));
  
  }
 public function create()
  {
    return view('admin.restaurants.create');
  }
  public function store(Request $request)
{
    // バリデーション
    $request->validate([
        'name' => 'required|string|max:255',
        'image' => 'nullable|image|mimes:jpg,jpeg,png,bmp,gif,svg,webp|max:2048',
        'description' => 'required|string',
        'lowest_price' => 'required|integer|min:0|lt:highest_price',
        'highest_price' => 'required|integer|min:0|gt:lowest_price',
        'postal_code' => 'required|digits:7',
        'address' => 'required|string|max:255',
        'opening_time' => 'required|date_format:H:i|before:closing_time',
        'closing_time' => 'required|date_format:H:i|after:opening_time',
        'seating_capacity' => 'required|integer|min:0',
    ]);

       // 画像の処理
       $imageName = ''; // 初期値を空文字に設定
       if ($request->hasFile('image')) {
           // アップロードされたファイルを指定フォルダに保存
           $imagePath = $request->file('image')->store('restaurants', 'public');
           // ファイル名を取得
           $imageName = basename($imagePath);
       } 
     // 新しい店舗の作成
     Restaurant::create([
         'name' => $request->name,
         'image' => $imageName, // 画像名を保存
         'description' => $request->description,
         'lowest_price' => $request->lowest_price,
         'highest_price' => $request->highest_price,
         'postal_code' => $request->postal_code,
         'address' => $request->address,
         'opening_time' => $request->opening_time,
         'closing_time' => $request->closing_time,
         'seating_capacity' => $request->seating_capacity,
     ]);
 
    
    // リダイレクトとフラッシュメッセージの設定
    return redirect()->route('admin.restaurants.index')->with('flash_message', '店舗を登録しました。');
  }
  public function edit(Restaurant $restaurant)
  {
    // 編集する店舗データをビューに渡す
    return view('admin.restaurants.edit', compact('restaurant'));
  }

  public function update(Request $request, Restaurant $restaurant)
  {
    // バリデーション
    $request->validate([
        'name' => 'required|string|max:255',
        'image' => 'nullable|image|mimes:jpg,jpeg,png,bmp,gif,svg,webp|max:2048',
        'description' => 'required|string',
        'lowest_price' => 'required|integer|min:0|lt:highest_price',
        'highest_price' => 'required|integer|min:0|gt:lowest_price',
        'postal_code' => 'required|digits:7',
        'address' => 'required|string|max:255',
        'opening_time' => 'required|date_format:H:i|before:closing_time',
        'closing_time' => 'required|date_format:H:i|after:opening_time',
        'seating_capacity' => 'required|integer|min:0',
    ]);

       // 画像の処理
       $imageName = $restaurant->image;
    if ($request->hasFile('image')) {
        // アップロードされたファイルを指定フォルダに保存
        $imagePath = $request->file('image')->store('restaurants', 'public');
        // ファイル名を取得
        $imageName = basename($imagePath);
    }
     
    // 店舗情報の更新
    $restaurant->update([
      'name' => $request->name,
      'image' => $imageName, // 画像も更新
      'description' => $request->description,
      'lowest_price' => $request->lowest_price,
      'highest_price' => $request->highest_price,
      'postal_code' => $request->postal_code,
      'address' => $request->address,
      'opening_time' => $request->opening_time,
      'closing_time' => $request->closing_time,
      'seating_capacity' => $request->seating_capacity,
  ]);
     // フラッシュメッセージ
     return redirect()->route('admin.restaurants.show', $restaurant->id)->with('flash_message', '店舗を更新しました。');
  }  
  public function destroy(Restaurant $restaurant)
  {
      // 管理者かどうかを確認
    if (!auth()->guard('admin')->check()) {
      return redirect()->route('admin.login')->withErrors(['error' => 'Forbidden']);
  }
    $restaurant->delete();
    return redirect()->route('admin.restaurants.index')->with('flash_message', '店舗を削除しました。');
}
}