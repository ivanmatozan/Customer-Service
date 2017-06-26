<?php

namespace App\Validation\Rules;

use App\Models\User;
use Respect\Validation\Rules\AbstractRule;

class EmailAvailable extends AbstractRule
{
    /**
     * Validate if email is available or if it already exists in DB
     *
     * @param $input
     * @return bool
     */
    public function validate($input): bool
    {
        return User::where('email', $input)->count() === 0;
    }
}