<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Checkout;
use App\Models\CheckoutItem;
use App\Models\User;
use App\Mail\OrderPlacedMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Hash;

class CheckoutController extends Controller
{
    // ✅ صفحة الشيك آوت
    public function index()
    {
        $cartItems = session('cart', []);
        $total = collect($cartItems)->sum(fn($item) => $item['price'] * $item['quantity']);
        $user = auth()->user();

        return view('checkout.index', compact('cartItems', 'total', 'user'));
    }

    // ✅ تأكيد الطلب
    public function confirm(Request $request)
    {
        $data = $request->all();
        $cartItems = session('cart', []);
        $total = collect($cartItems)->sum(fn($item) => $item['price'] * $item['quantity']);
        return view('checkout.confirm', compact('data', 'cartItems', 'total'));
    }

    // ✅ تنفيذ الطلب النهائي



    public function process(Request $request)
    {
        $cartItems = session('cart', []);
        if (empty($cartItems)) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        // ✅ التحقق الأساسي للبيانات المطلوبة دائماً
        // ✅ إذا المستخدم مسجل دخول لا نتحقق من الاسم أو الإيميل
        if (Auth::check()) {
            $request->validate([
                'phone_number' => 'required|string|max:20',
                'town'         => 'required|string|max:100',
                'country'      => 'required|string|max:100',
                'zipcode'      => 'required|string|max:20',
                'address'      => 'required|string|max:255',
            ]);
        } else {
            // ✅ إذا المستخدم غير مسجل دخول، نتحقق من الكل
            $request->validate([
                'name'         => 'required|string|max:255',
                'email'        => 'nullable|email',
                'phone_number' => 'required|string|max:20',
                'town'         => 'required|string|max:100',
                'country'      => 'required|string|max:100',
                'zipcode'      => 'required|string|max:20',
                'address'      => 'required|string|max:255',
                'password'     => 'nullable|min:6|confirmed',
            ]);
        }


        $userId = null;

        // 🟢 إذا المستخدم مسجل دخول مسبقاً
        if (Auth::check()) {
            $userId = Auth::id();
        }
        // 🟡 إذا المستخدم غير مسجل
        else {
            // 🟠 إذا اختار إنشاء حساب
            if ($request->has('create_account') && $request->filled('email') && $request->filled('password')) {

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
        $checkout = \App\Models\Checkout::create([
            'user_id'     => $userId,
            'name'        => $request->name ?? (Auth::check() ? Auth::user()->name : 'Guest'),
            'email'       => $request->email ?? (Auth::check() ? Auth::user()->email : null),
            'phone_number' => $request->phone_number,
            'town'        => $request->town,
            'country'     => $request->country,
            'zipcode'     => $request->zipcode,
            'address'     => $request->address,
            'total_price' => collect($cartItems)->sum(fn($i) => $i['price'] * $i['quantity']),
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

        // 🧹 تنظيف السلة
        session()->forget(['cart', 'cart_total', 'cart_count']);
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
}
