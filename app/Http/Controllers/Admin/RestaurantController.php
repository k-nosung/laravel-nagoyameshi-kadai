<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Restaurant;
use App\Models\Category;
use App\Models\RegularHoliday;

class RestaurantController extends Controller
{

    // -----
    // index アクション
    // ------
     public function index(Request $request) {
        // 検索ボックスに入力されたキーワードを取得する
        $keyword = $request->input('keyword');

        // キーワードが存在すれば検索を行い、そうでなければ全件取得する
        if ($keyword) {
            $restaurants = Restaurant::where('name', 'like', "%{$keyword}%")->paginate(15);
        } else {
            $restaurants = Restaurant::paginate(15);
        }

        $total = $restaurants->total();

        return view('admin.restaurants.index', compact('restaurants', 'keyword', 'total'));
    }

    // ------
    // show アクション
    // ------

    public function show(Restaurant $restaurant) {
        return view('admin.restaurants.show', compact('restaurant'));
    }

    // ------
    // create アクション
    // ------

    public function create() {
        $categories = Category::all();
       
        $regular_holidays = RegularHoliday::all();
        return view('admin.restaurants.create',compact('categories', 'regular_holidays'));

    }

    // ------
    // store アクション
    // ------

    public function store(Request $request) {
        $request->validate([
            'name' => 'required',
            'image' => 'image|max:2048',
            'description' => 'required',
            'lowest_price' => 'required|numeric|min:0|lte:highest_price',
            'highest_price' => 'required|numeric|min:0|gte:lowest_price',
            'postal_code' => 'required|digits:7',
            'address' => 'required',
            'opening_time' => 'required|before:closing_time',
            'closing_time' => 'required|after:opening_time',
            'seating_capacity' => 'required|numeric|min:0',
        ]);

        $restaurant = new Restaurant();
        $restaurant->name = $request->input('name');
        if ($request->hasFile('image')) {
            $image = $request->file('image')->store('public/restaurants');
            $restaurant->image = basename($image);
        } else {
            $restaurant->image = '';
        }
        $restaurant->description = $request->input('description');
        $restaurant->lowest_price = $request->input('lowest_price');
        $restaurant->highest_price = $request->input('highest_price');
        $restaurant->postal_code = $request->input('postal_code');
        $restaurant->address = $request->input('address');
        $restaurant->opening_time = $request->input('opening_time');
        $restaurant->closing_time = $request->input('closing_time');
        $restaurant->seating_capacity = $request->input('seating_capacity');
        $restaurant->save();

        $category_ids = array_filter($request->input('category_ids'));
        $restaurant->categories()->sync($category_ids);   
        
        $regular_holiday_ids = $request->input('regular_holiday_ids');
        $restaurant->regular_holidays()->sync($regular_holiday_ids);

        return redirect()->route('admin.restaurants.index')->with('flash_message', '店舗を登録しました。');
    }


    // ------
    // edit アクション
    // ------
    public function edit(Restaurant $restaurant) {
     $categories = Category::all();

    // 設定されたカテゴリのIDを配列化する
     $category_ids = $restaurant->categories->pluck('id')->toArray();

     $regular_holidays = RegularHoliday::all();

     // ここにビューへの戻りを追加
     return view('admin.restaurants.edit', compact('restaurant','categories','category_ids','regular_holidays'));
    }

    // ------
    // update アクション
    // ------
    public function update(Request $request, Restaurant $restaurant) {

    //フォームからの入力値を取得
     //バリデーションの設定
        $request->validate([
          'name' => 'required',
          'image' => 'image|max:2048',
          'description' => 'required',
          'lowest_price' => 'required|numeric|min:0|lte:highest_price',
          'highest_price' => 'required|numeric|min:0|gte:lowest_price',
          'postal_code' => 'required|digits:7',
          'address' => 'required',
          'opening_time' => 'required|before:closing_time',
          'closing_time' => 'required|after:opening_time',
          'seating_capacity' => 'required|numeric|min:0',
        ]);

        //処理の内容
        // アップロードされたファイル（name="image"）が存在すれば処理を実行する
        if ($request->hasFile('image')) {
            // アップロードされたファイル（name="image"）をstorage/app/public/restaurantsフォルダに保存し、戻り値（ファイルパス）を変数$imageに代入する
                $image_path = $request->file('image')->store('public/restaurants');
            // ファイルパスからファイル名のみを取得し、Restaurantインスタンスのimageプロパティに代入する
                $restaurant->image = basename($image_path);
            }

         $restaurant->name = $request->input('name');
         $restaurant->description = $request->input('description');
         $restaurant->lowest_price = $request->input('lowest_price');
         $restaurant->highest_price = $request->input('highest_price');
         $restaurant->postal_code = $request->input('postal_code');
         $restaurant->address = $request->input('address');
         $restaurant->opening_time = $request->input('opening_time');
         $restaurant->closing_time = $request->input('closing_time');
         $restaurant->seating_capacity = $request->input('seating_capacity');
         $restaurant->save();

         $category_ids = array_filter($request->input('category_ids'));
         $restaurant->categories()->sync($category_ids);

         $regular_holiday_ids = $request->input('regular_holiday_ids');
         $restaurant->regular_holidays()->sync($regular_holiday_ids);


    //リダイレクト先とフラッシュメッセージ
        // 登録しましたというメッセージと共にビューへのリダイレクトを追加しましょう
        return redirect()->route('admin.restaurants.show', $restaurant)->with('flash_message', '店舗を編集しました。');
    }

    // ------
    // destroy アクション
    // ------
    public function destroy(Restaurant $restaurant) {
          
         //処理の内容 削除処理
         $restaurant->delete();

         // リダイレクト先とフラッシュメッセージ
       return redirect()->route('admin.restaurants.index')->with('flash_message', '店舗を登録しました。');
     }

    }