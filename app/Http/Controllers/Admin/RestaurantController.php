<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin; 
use App\Models\Restaurant; 
use Illuminate\Http\Request;

class RestaurantController extends Controller
{
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
      $validated = $request->validate([
          'name' => 'required|string|max:255',
          'image' => 'nullable|file|mimes:jpg,jpeg,png,bmp,gif,svg,webp|max:2048',
          'description' => 'required|string',
          'lowest_price' => 'required|numeric|min:0|lte:highest_price',
          'highest_price' => 'required|numeric|min:0|gte:lowest_price',
          'postal_code' => 'required|numeric|digits:7',
          'address' => 'required|string',
          'opening_time' => 'required|date_format:H:i|before:closing_time',
          'closing_time' => 'required|date_format:H:i|after:opening_time',
          'seating_capacity' => 'required|numeric|min:0',
      ]);

      $restaurant = new Restaurant($validated);

      if ($request->hasFile('image')) {
          $image = $request->file('image')->store('public/restaurants');
          $restaurant->image = basename($image);
      } else {
          $restaurant->image = '';
      }

      $restaurant->save();
    
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
      $request->validate([
          'name' => 'required|string|max:255',
          'image' => 'nullable|file|mimes:jpg,jpeg,png,bmp,gif,svg,webp|max:2048',
          'description' => 'required|string',
          'lowest_price' => 'required|numeric|min:0|lte:highest_price',
          'highest_price' => 'required|numeric|min:0|gte:lowest_price',
          'postal_code' => 'required|numeric|digits:7',
          'address' => 'required|string',
          'opening_time' => 'required|date_format:H:i|before:closing_time',
          'closing_time' => 'required|date_format:H:i|after:opening_time',
          'seating_capacity' => 'required|numeric|min:0',
      ]);

      if ($request->hasFile('image')) {
          $image = $request->file('image')->store('public/restaurants');
          $restaurant->image = basename($image);
      }

      $restaurant->update($request->all());
   
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