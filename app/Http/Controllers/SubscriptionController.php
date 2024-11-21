<?php
namespace App\Http\Controllers;
use Stripe\Stripe;
use Stripe\Customer;
use Stripe\PaymentIntent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class SubscriptionController extends Controller
{
  // -----
  // create アクション
  // -----
  public function create() {
            $intent = Auth::user()->createSetupIntent();
    
            return view('subscription.create', compact('intent'));
        }
  // -----
  // store アクション
  // -----
  public function store(Request $request) {
            $request->user()->newSubscription('premium_plan', 'price_1QNZBeLgK93Ys8EgtIdiE0Hl')->create($request->paymentMethodId);
    
            return redirect()->route('home')->with('flash_message', '有料プランへの登録が完了しました。');
        }
  // -----
  // edit アクション
  // -----
      public function edit() {
          $user = Auth::user();
            $intent = $user->createSetupIntent();
    
            return view('subscription.edit', compact('user', 'intent'));
        }
  // -----
  // update アクション
  // -----
      public function update(Request $request) {
            $request->user()->updateDefaultPaymentMethod($request->paymentMethodId);
    
            return redirect()->route('home')->with('flash_message', 'お支払い方法を変更しました。');
        }
  // -----
  // cancel アクション
  // -----
  public function cancel()
  {
      return view('subscription.cancel');
  }
  // -----
  // destroy アクション
  // -----
  public function destroy(Request $request) {
            $request->user()->subscription('premium_plan')->cancelNow();
    
            return redirect()->route('home')->with('flash_message', '有料プランを解約しました。');
    }
}