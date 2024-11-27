<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class NotSubscribed
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    // サブスク中ではない人はOK
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if ($request->user()?->subscribed('premium_plan')) {
            return redirect()->route('subscription/edit');
        }
        return $next($request);
    }
}