<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\WelcomeCouponAssigner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    use \Illuminate\Foundation\Auth\AuthenticatesUsers;

    // ✅ بعد تسجيل الدخول بنجاح، يعود للمسار الرئيسي
    protected $redirectTo = '/';

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Attempt to log the user into the application with remember support.
     */
    protected function attemptLogin(Request $request)
    {
        return $this->guard()->attempt(
            $this->credentials($request),
            $request->boolean('remember')
        );
    }

    // ✅ بعد نجاح تسجيل الدخول
    protected function authenticated(Request $request, $user)
    {
        if (Auth::check()) {
            app(WelcomeCouponAssigner::class)->assign($user->id);
            // 🔁 توجيه المستخدم مباشرة إلى الصفحة الرئيسية
            return redirect()->intended('/');
        }

        // 🚫 إذا فشل التحقق لأي سبب
        Auth::logout();
        return redirect('/login')->withErrors(['email' => 'Unauthorized login attempt.']);
    }

}
