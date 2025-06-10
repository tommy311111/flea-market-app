<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\UserProfile;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\ProfileRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(RegisterRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        Auth::login($user);

        return redirect()->route('profile.edit');
    }

    public function showProfileForm()
    {
        $user = auth()->user();
        return view('user.edit',compact('user'));
    }

    public function storeProfile(ProfileRequest $request)
{
    $user = auth()->user();

    DB::transaction(function () use ($user, $request) {
        $user->update([
            'name' => $request->input('name', $user->name),
        ]);

        UserProfile::updateOrCreate(
            ['user_id' => $user->id],
            [ //画像追加する？
                'name'     => $request->input('name', $user->name),
                'image' => $request->input('image'),
                'postcode' => $request->input('postcode'),
                'address'  => $request->input('address'),
                'building' => $request->input('building'),
            ]
        );
    });

    return redirect()->route('index');
}

}
