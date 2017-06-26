<?php

namespace App\Validation\Rules;

use Respect\Validation\Rules\AbstractRule;

class ConfirmPassword extends AbstractRule
{
    /**
     * Password to confirm $input with
     *
     * @var string
     */
    protected $password;

    public function __construct(string $password)
    {
        $this->password = $password;
    }

    /**
     * Check if password and confirm password inputs have same value
     *
     * @param $input
     * @return bool
     */
    public function validate($input): bool
    {
        return $this->password === $input;
    }
}