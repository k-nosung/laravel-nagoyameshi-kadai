<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Restaurant; 
use App\Models\Category; 

class RestaurantController extends Controller
{
    public function index(Request $request)
    {
        // 検索ボックスに入力されたキーワード
        $keyword = $request->input('keyword');
        // 選択されたカテゴリのID
        $category_id = $request->input('category_id');
        // 選択された予算
        $price = $request->input('price');

        // クエリビルダを作成
        $query = Restaurant::query();

        // キーワードが存在する場合は、店舗名または店舗住所またはカテゴリ名で部分一致検索を行う
        if ($keyword) {
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'LIKE', "%{$keyword}%")
                  ->orWhere('address', 'LIKE', "%{$keyword}%")
                  ->orWhereHas('categories', function ($q) use ($keyword) {
                      $q->where('name', 'LIKE', "%{$keyword}%");
                  });
            });
        }

        // カテゴリIDが存在する場合は、カテゴリのIDで検索を行う
        if ($category_id) {
            $query->whereHas('categories', function ($q) use ($category_id) {
                $q->where('category_id', $category_id);
            });
        }

        // 予算が存在する場合は、最低価格で検索を行う
        if ($price) {
            $query->where('lowest_price', '<=', $price);
        }

        // 並べ替え
        $sorts = [
            '掲載日が新しい順' => 'created_at desc',
            '価格が安い順' => 'lowest_price asc',
            '評価が高い順' => 'rating desc',
            '予約が多い順' => 'popular desc',
        ];
        $sort_query = [];
        $sorted = "created_at desc";
        if ($request->has('select_sort')) {
            $slices = explode(' ', $request->input('select_sort'));
            $sort_query[$slices[0]] = $slices[1];
            $sorted = $request->input('select_sort');
        }

        // ページネーション適用済みのrestaurantsテーブルのデータを取得
        $restaurants = $query->sortable($sort_query)->paginate(15);

        // 取得したデータの総数
        $total = $restaurants->total();

        // カテゴリのデータを取得
        $categories = Category::all();

        // ビューにデータを渡す
        return view('restaurants.index', [
            'keyword' => $keyword,
            'category_id' => $category_id,
            'price' => $price,
            'sorts' => $sorts,
            'sorted' => $sorted,
            'restaurants' => $restaurants,
            'categories' => $categories,
            'total' => $total,
        ]);
    }
    public function show(Restaurant $restaurant)
    {
        // ビューにデータを渡す
        return view('restaurants.show', [
            'restaurant' => $restaurant,
        ]);
    }
}
