<?php

namespace App\Http\Middleware;

use App\Services\AuthService;
use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    protected $authService;
    
    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }
    
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $roles  Comma separated role ids
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $roles)
    {
        if (!$request->session()->has('user')) {
            return redirect()->route('login');
        }
        
        $roleIds = explode(',', $roles);
        $userId = $request->session()->get('user')['user_id'];
        
        if (!$this->authService->hasRole($userId, $roleIds)) {
            return redirect()->route('forbidden');
        }
        
        return $next($request);
    }
}