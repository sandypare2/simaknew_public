<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class LoginController extends Controller
{
    public function index()
    {
        return view('auth.login', [
            'title' => 'Login',
        ]);
    }
    // Show login form
    // public function showLoginForm()
    // {
    //     return view('auth.login');
    // }

    // Handle login request
    public function authenticate(Request $request)
    {
        // dd($request);
        // $credentials = $request->validate([
        //     'username' => 'required|max:255',
        //     'password' => 'required|max:255'
        // ]);

        // if ($userRequestAuth = User::where('username',$credentials['username'])->first()) { 
        //     if ($userRequestAuth->aktif == 1) {                                                
        //         if (Auth::attempt($credentials)) {
        //             $request->session()->regenerate();                        
        //             return redirect()->intended('dashboard');
        //         }else{   
        //             // dd(md5($credentials['password']));
        //             $user = User::where('username', $credentials['username'])
        //             ->where('user_pass',md5($credentials['password']))
        //             ->first();
        //             if ($user) {
        //                 Auth::login($user);
        //                 $request->session()->regenerate();
        //                 return redirect()->intended('dashboard');
        //             }else{
        //                 return back()->with('error', 'Username atau password salah!');
        //             }
        //         }
        //     }else{
        //         return back()->with('error', 'Maaf akun anda saat ini non aktif.');
        //     }
        // }else{
        //     return back()->with('error', 'Username pengguna tidak ditemukan');
        // }
        // return back()->with('error', 'Username atau password salah!');
        $credentials = $request->validate([
            'username' => 'required|max:255',
            'password' => 'required|max:255'
        ]);

        $credentials = $request->only('username', 'password');
        $user = User::where('username', $credentials['username'])->first();
        // $user = User::where('username', 'sandy')->first();
        // dd($user);

        if (! $user) {
            return back()->withErrors(['username' => 'These credentials do not match our records.']);
        }

        $plain = $request->password;
        $hashed = $user->password;    
                
        
        // ✅ Check if bcrypt
        if (str_starts_with($hashed, '$2y$') && Hash::check($plain, $hashed)) {
            Auth::login($user);
            return redirect()->intended('dashboard');
        }

        // ✅ If MD5 match (legacy)
        if (strlen($hashed) === 32 && md5($plain) === $hashed) {
            // upgrade to bcrypt
            $user->password = Hash::make($plain);
            $user->save();

            Auth::login($user);
            return redirect()->intended('dashboard');
        }
        return back()->withErrors(['password' => 'Invalid credentials.']);        
    }

    // Logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
