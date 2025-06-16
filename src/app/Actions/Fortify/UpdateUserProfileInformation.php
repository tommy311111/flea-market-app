<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;

class UpdateUserProfileInformation implements UpdatesUserProfileInformation
{
    /**
     * Validate and update the given user's profile information.
     *
     * @param  array<string, string>  $input
     */
    public function update(User $user, array $input): void
    {
        Validator::make($input, [
           'name' => ['required', 'string', 'max:255'], 
           'image' => ['nullable', 'image', 'mimes:jpeg,png'], 
            'postcode' => ['required', 'string', 'regex:/^\d{3}-\d{4}$/'], // 例: 123-4567
            'address'     => ['required', 'string', 'max:255'],
            'building'    => ['required', 'string', 'max:255'],
        ])->validate();

         // ① ユーザー名を更新
    $user->name = $input['name'];
    $user->save();

    // ② プロフィール画像を保存（ある場合）
    $imagePath = $user->profile->image;
    if (isset($input['image'])) {
        $imagePath = $input['image']->store('images/profiles', 'public');
    }

    // ③ プロフィール更新
    $user->profile->update([
        'image' => $imagePath,
        'postcode' => $input['postcode'],
        'address' => $input['address'],
        'building' => $input['building'],
    ]);
        
    }

    /**
     * Update the given verified user's profile information.
     *
     * @param  array<string, string>  $input
     */
    protected function updateVerifiedUser(User $user, array $input): void
    {
        $user->forceFill([
            'name' => $input['name'],
            'email' => $input['email'],
            'email_verified_at' => null,
        ])->save();

        $user->sendEmailVerificationNotification();
    }
}
