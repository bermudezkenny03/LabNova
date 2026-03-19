<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['gender', 'birthdate', 'address', 'addon_address', 'notes', 'user_id'])]
#[Hidden([])]

class UserDetail extends Model
{
    use SoftDeletes;

    // Define the relationship with the User model
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Static method to create a UserDetail record
    public static function createUserDetail($validated, $userId)
    {
        $userDetailData = array_filter($validated, fn($key) => in_array($key, (new self)->getFillable()), ARRAY_FILTER_USE_KEY);
        $userDetailData['user_id'] = $userId;

        return self::create($userDetailData);
    }

    // Static method to update a UserDetail record
    public static function updateUserDetail($validated, $user)
    {
        if ($user->userDetail) {
            $user->userDetail->update(array_filter($validated, fn($key) => in_array($key, (new self)->getFillable()), ARRAY_FILTER_USE_KEY));
        }
    }
}
