<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if admin is logged in using the 'admin' guard
        if (!Auth::guard('admin')->check()) {
            // If not authenticated, redirect to admin login page
            return redirect()->route('admin.login.form')->with('error', 'Please login to access admin panel.');
        }

        // Get the authenticated admin
        $admin = Auth::guard('admin')->user();

        // Check if admin account is active
        if (!$admin->active) {
            // If admin is not active, logout and redirect to login
            Auth::guard('admin')->logout();
            return redirect()->route('admin.login.form')->with('error', 'Your admin account has been deactivated.');
        }

        // If all checks pass, allow the request to continue
        return $next($request);
    }
}