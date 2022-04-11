<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\User\AfterRegister;

class UserController extends Controller
{
    public function login() {
        return view('auth.user.login');
    }

    public function google() {
        return Socialite::driver('google')->redirect();
    }

    public function handleProviderCallback() {
        $callback = Socialite::driver('google')->stateless()->user();
        // data parsing from callback
        $data = [
            'name' => $callback->getName(),
            'email' => $callback->getEmail(),
            'avatar' => $callback->getAvatar(),
            'email_verified_At' => date('Y-m-d H:i:s', time())
        ];

        // $user = User::firstOrCreate(['email' => $data['email']], $data);
        // // alasan digunakan firstOrCreate apabila sistem menemukan email yang, maka sitem tidak perlu membuat data baru,apabila tidak ketemu maka akan di buat data baru

        $user = User::whereEmail($data['email'])->first();
        if(!$user) {
            $user = User::create($data);
            Mail::to($user->email)->send(new AfterRegister($user));
        }

        Auth::login($user, true);
        return redirect(route('welcome'));

    }
}
