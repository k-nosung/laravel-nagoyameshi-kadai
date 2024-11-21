<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
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
        if ($request->user()?->subscribed('premium_plan')) {
            return redirect()->route('subscription.edit');
        }
        return $next($request);
    }
}