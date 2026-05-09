<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginForm() {
        return view('auth.login'); // We will build this view later
    }

    public function login(Request $request) {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ], [],['username' => 'اسم المستخدم', 'password' => 'كلمة المرور']);

        $credentials = $request->only('username', 'password');

        if (Auth::guard('admin')->attempt($credentials)) {
            $request->session()->regenerate();
            $request->session()->save();
            return redirect()->intended(route('admin.dashboard'))->with('success', 'تم تسجيل الدخول كمسؤول بنجاح.');
        }

        if (Auth::guard('employee')->attempt($credentials)) {
            if (Auth::guard('employee')->user()->status === 'inactive') {
                Auth::guard('employee')->logout();
                return back()->withErrors(['username' => 'هذا الحساب غير مفعل.']);
            }
            $request->session()->regenerate();
            $request->session()->save();
            return redirect()->intended(route('employee.dashboard'))->with('success', 'تم تسجيل الدخول بنجاح.');
        }

        return back()->withErrors(['username' => 'بيانات الدخول غير صحيحة.']);
    }

    public function logout(Request $request) {
        if (Auth::guard('admin')->check()) Auth::guard('admin')->logout();
        if (Auth::guard('employee')->check()) Auth::guard('employee')->logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'تم تسجيل الخروج بنجاح.');
    }
}