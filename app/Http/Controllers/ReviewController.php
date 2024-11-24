<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Restaurant;
use App\Models\Review;
class ReviewController extends Controller
{
    //indexアクション
    public function index(Restaurant $restaurant)
{
    if (auth()->user()->subscribed('premium_plan')) {
        // 有料プランに登録済みの場合
        $reviews = $restaurant->reviews()->orderBy('created_at', 'desc')->paginate(5);
    } else {
        // 有料プランに未登録の場合
        $reviews = $restaurant->reviews()->orderBy('created_at', 'desc')->take(3)->get();
        $reviews = collect($reviews); // コレクションに変換
    }
    return view('reviews.index', compact('restaurant', 'reviews'));
}
//createアクション
public function create(Restaurant $restaurant)
{
     // ユーザーが無料会員ならサブスクリプションページにリダイレクト
     if (auth()->user()->is_free_member()) {
        return redirect()->route('subscription.create');
    }
    return view('reviews.create', compact('restaurant'));
}
//storeアクション
public function store(Request $request, Restaurant $restaurant)
{
    // バリデーション
    $request->validate([
        'score' => 'required|integer|between:1,5',
        'content' => 'required|string',
    ]);
    // レビューの作成
    Review::create([
        'content' => $request->content,
        'score' => $request->score,
        'restaurant_id' => $restaurant->id,
        'user_id' => auth()->id(),
    ]);
    // フラッシュメッセージを設定してリダイレクト
    return redirect()->route('restaurants.reviews.index', $restaurant)->with('flash_message', 'レビューを投稿しました。');
}
//editアクション
public function edit(Restaurant $restaurant, Review $review)
{
    return view('reviews.edit', compact('restaurant', 'review'));
}
//updateアクション
public function update(Request $request, Restaurant $restaurant, Review $review)
{
    // 他人のレビューを編集できないようにする
    if ($review->user_id !== auth()->id()) {
        return redirect()->route('restaurants.reviews.index', $restaurant)->with('error_message', '不正なアクセスです。');
    }
    // バリデーション
    $request->validate([
        'score' => 'required|integer|between:1,5',
        'content' => 'required|string',
    ]);
    // レビューの更新
    $review->update([
        'content' => $request->content,
        'score' => $request->score,
    ]);
    // フラッシュメッセージを設定してリダイレクト
    return redirect()->route('restaurants.reviews.index', $restaurant)->with('flash_message', 'レビューを編集しました。');
}
//destroyアクション
public function destroy(Restaurant $restaurant, Review $review)
{
    $review->delete();
    return redirect()->route('restaurants.reviews.index', $restaurant)->with('flash_message', 'レビューを削除しました。');
}
}