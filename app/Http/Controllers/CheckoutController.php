<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreCheckoutRequest;
use App\Models\Checkout;
use App\Models\CheckoutItem;
use App\Models\User;
use App\Mail\OrderPlacedMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Hash;
use App\Rules\Recaptcha;
use App\Models\Coupon;
use Carbon\Carbon;
use Illuminate\Support\Arr;

class CheckoutController extends Controller
{
    /**
     * Build a default contact data array using user profile and last checkout.
     */
    protected function contactDefaults(?User $user): array
    {
        if (!$user) {
            return [];
        }

        $lastOrder = Checkout::where('user_id', $user->id)->latest()->first();

        $base = [
            'name'         => $user->name,
            'email'        => $user->email,
            'phone_number' => $user->phone_number,
            'town'         => $user->town,
            'country'      => $user->country,
            'zipcode'      => $user->zipcode,
            'address'      => $user->address,
        ];

        if ($lastOrder) {
            foreach (['phone_number', 'town', 'country', 'zipcode', 'address'] as $field) {
                if (empty($base[$field]) && !empty($lastOrder->{$field})) {
                    $base[$field] = $lastOrder->{$field};
                }
            }
        }

        return $base;
    }

    public function checkEmail(Request $request)
    {
        $email = $request->query('email');
        $exists = false;
        if ($email) {
            $exists = User::where('email', $email)->exists();
        }
        return response()->json(['exists' => $exists]);
    }

    // ✅ صفحة الشيك آوت
    public function index()
    {
        $cartItems = session('cart', []);
        $total = collect($cartItems)->sum(fn($item) => $item['price'] * $item['quantity']);
        $applied = session('coupon');
        $discount = $applied['discount'] ?? 0;
        $totalAfter = max($total - $discount, 0);
        $user = auth()->user();
        $prefill = $this->contactDefaults($user);

        return view('checkout.index', compact('cartItems', 'total', 'totalAfter', 'discount', 'applied', 'user', 'prefill'));
    }

    // ✅ تأكيد الطلب
    public function confirm(StoreCheckoutRequest $request)
    {
        $user = Auth::user();
        $this->mergeContactDefaults($request, $user);
        // Validation is now handled by StoreCheckoutRequest

        // إذا البريد مسجّل مسبقاً ويجب تسجيل الدخول قبل المتابعة
        if (!Auth::check() && $request->filled('email')) {
            $existing = User::where('email', $request->email)->first();
            if ($existing) {
                if (!$request->filled('password')) {
                    return back()->withErrors(['password' => 'This email is registered. Please enter your password to continue.'])->withInput();
                }
                if (!Hash::check($request->password, $existing->password)) {
                    return back()->withErrors(['password' => 'Incorrect password for this email.'])->withInput();
                }
                Auth::login($existing);
                $user = $existing;
                $this->mergeContactDefaults($request, $user);
            }
        }

        $cartItems = session('cart', []);
        if (empty($cartItems)) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $data = $request->all();
        $total = collect($cartItems)->sum(fn($item) => $item['price'] * $item['quantity']);
        $applied = session('coupon');
        $discount = $applied['discount'] ?? 0;
        $totalAfter = max($total - $discount, 0);
        return view('checkout.confirmorder', compact('data', 'cartItems', 'total', 'discount', 'totalAfter', 'applied'));
    }

    // ✅ تنفيذ الطلب النهائي



    public function process(StoreCheckoutRequest $request)
    {
        $user = Auth::user();
        $this->mergeContactDefaults($request, $user);
        // Validation is now handled by StoreCheckoutRequest

        $cartItems = session('cart', []);
        $applied = session('coupon');
        if (empty($cartItems)) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $userId = null;

        // 🟢 إذا المستخدم مسجل دخول مسبقاً
        if (Auth::check()) {
            $userId = Auth::id();
        }
        // 🟡 إذا المستخدم غير مسجل
        else {
            $existing = $request->filled('email') ? User::where('email', $request->email)->first() : null;
            if ($existing) {
                // يلزم كلمة مرور صحيحة لتسجيل الدخول
                if (!$request->filled('password') || !Hash::check($request->password, $existing->password)) {
                    return back()->withErrors(['password' => 'This email is registered. Please enter the correct password to continue.'])->withInput();
                }
                Auth::login($existing);
                $userId = $existing->id;
            }
            // 🟠 إذا اختار إنشاء حساب
            elseif ($request->has('create_account') && $request->filled('email') && $request->filled('password')) {

                // تحقق إذا الإيميل مستخدم مسبقاً
                $existing = User::where('email', $request->email)->first();
                if ($existing) {
                    return back()->withErrors(['email' => 'This email is already registered. Please log in.'])->withInput();
                }

                // إنشاء الحساب وتسجيل الدخول فوراً
                $user = User::create([
                    'name'     => $request->name,
                    'email'    => $request->email,
                    'password' => Hash::make($request->password),
                ]);
                Auth::login($user);
                $userId = $user->id;
            }
            // ⚪ إذا لم يختر إنشاء حساب → يكمل كـ Guest
            else {
                $userId = null;
            }
        }

        // ✅ إنشاء الطلب
        $subtotal = collect($cartItems)->sum(fn($i) => $i['price'] * $i['quantity']);
        $discount = $applied['discount'] ?? 0;
        $couponId = $applied['coupon_id'] ?? null;
        $totalAfter = max($subtotal - $discount, 0);

        // تحديث بيانات المستخدم من الطلب الجديد لتسهيل الملء لاحقاً
        if ($userId && ($account = Auth::user())) {
            $updates = Arr::only($request->all(), ['phone_number','town','country','zipcode','address']);
            $dirty = false;
            foreach ($updates as $key => $value) {
                if ($value && $account->{$key} !== $value) {
                    $account->{$key} = $value;
                    $dirty = true;
                }
            }
            if ($dirty) {
                $account->save();
            }
        }

        $checkout = \App\Models\Checkout::create([
            'user_id'     => $userId,
            'name'        => $request->name ?? (Auth::check() ? Auth::user()->name : 'Guest'),
            'email'       => $request->email ?? (Auth::check() ? Auth::user()->email : null),
            'phone_number' => $request->phone_number,
            'town'        => $request->town,
            'country'     => $request->country,
            'zipcode'     => $request->zipcode,
            'address'     => $request->address,
            'total_price' => $totalAfter,
            'total_before_discount' => $subtotal,
            'discount_amount' => $discount,
            'coupon_id' => $couponId,
            'status'      => 'Pending',
        ]);

        foreach ($cartItems as $item) {
            \App\Models\CheckoutItem::create([
                'checkout_id' => $checkout->id,
                'product_id'  => $item['product_id'] ?? null,
                'name'        => $item['title'] ?? $item['name'] ?? 'Unnamed Product',
                'image'       => $item['image'] ?? null,
                'quantity'    => $item['quantity'],
                'price'       => $item['price'],
                'total_price' => $item['price'] * $item['quantity'],
            ]);
        }

        // ✉️ إرسال الإيميل
        \Illuminate\Support\Facades\Mail::to($checkout->email ?? 'no-reply@alikrecht.com')
            ->send(new \App\Mail\OrderPlacedMail($checkout, false));
        \Illuminate\Support\Facades\Mail::to('alikrecht.admin@gmail.com')
            ->send(new \App\Mail\OrderPlacedMail($checkout, true));

        // تحديث عداد الكوبون
        if ($couponId) {
            $coupon = Coupon::find($couponId);
            if ($coupon) {
                $coupon->increment('used_count');
                if ($coupon->usage_limit > 0 && $coupon->used_count >= $coupon->usage_limit) {
                    $coupon->status = false;
                    $coupon->save();
                }
            }
        }

        // 🧹 تنظيف السلة والكوبون
        session()->forget(['cart', 'cart_total', 'cart_count', 'coupon']);
        session()->save();

        return redirect()->route('checkout.thankyou', ['order' => $checkout->id]);
    }







    // ✅ صفحة الشكر
    public function thankYou($orderId)
    {
        $order = Checkout::with('items')->findOrFail($orderId);
        return view('checkout.thankyou', compact('order'));
    }

    // ✅ تحميل الفاتورة PDF
    public function downloadInvoice($orderId)
    {
        $order = Checkout::with('items')->findOrFail($orderId);
        $pdf = Pdf::loadView('pdf.invoice', compact('order'))->setPaper('a4');
        return $pdf->download("invoice_{$order->id}.pdf");
    }

    /**
     * Inject default contact values for authenticated users.
     */
    protected function mergeContactDefaults(Request $request, ?User $user): void
    {
        if (!$user) {
            return;
        }

        $defaults = $this->contactDefaults($user);
        $fields   = ['name','email','phone_number','town','country','zipcode','address'];

        $merge = [];
        foreach ($fields as $field) {
            if (!$request->filled($field) && !empty($defaults[$field])) {
                $merge[$field] = $defaults[$field];
            }
        }

        if ($merge) {
            $request->merge($merge);
        }
    }

    /**
     * Shared validation rules for checkout/confirm.
     */
    protected function validateForCheckout(Request $request, bool $finalStep = false): void
    {
        $siteKey = env('RECAPTCHA_SITE_KEY');
        $secret  = env('RECAPTCHA_SECRET') ?: env('RECAPTCHA_SECRET_KEY');
        $needsRecaptcha = !$finalStep ? false : (!Auth::check() && $siteKey && $secret);
        $recaptchaRule = $needsRecaptcha ? ['required', new Recaptcha] : ['nullable'];

        if (Auth::check()) {
            $request->validate([
                'name'         => 'nullable|string|max:255',
                'email'        => 'nullable|email',
                'phone_number' => 'required|string|max:20',
                'town'         => 'required|string|max:100',
                'country'      => 'required|string|max:100',
                'zipcode'      => 'required|string|max:20',
                'address'      => 'required|string|max:255',
            ]);

            // تخزين البيانات في ملف المستخدم لسهولة الملء مستقبلاً
            $user = Auth::user();
            $updates = Arr::only($request->all(), ['phone_number','town','country','zipcode','address']);
            $dirty = false;
            foreach ($updates as $key => $value) {
                if ($value && $user->{$key} !== $value) {
                    $user->{$key} = $value;
                    $dirty = true;
                }
            }
            if ($dirty) {
                $user->save();
            }
        } else {
            $request->validate([
                'name'         => 'required|string|max:255',
                'email'        => 'required|email',
                'phone_number' => 'required|string|max:20',
                'town'         => 'required|string|max:100',
                'country'      => 'required|string|max:100',
                'zipcode'      => 'required|string|max:20',
                'address'      => 'required|string|max:255',
                'password'     => 'nullable|min:6|confirmed',
                'g-recaptcha-response' => $recaptchaRule,
            ]);
        }
    }
}
