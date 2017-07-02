<?php

namespace App\Services;

use App\Models\User;

/**
 * Service for authenticating users
 */
class Authentication
{
    /**
     * Attempt to login user
     *
     * @param string $email
     * @param string $password
     * @return bool
     */
    public function attempt(string $email, string $password): bool
    {
        $user = User::where('email', $email)->first();

        if (!$user) {
            return false;
        }

        if (!password_verify($password, $user->password)) {
            return false;
        }

        $_SESSION['user_id'] = $user->id;

        return true;
    }

    /**
     * Get logged in user
     *
     * @mixed null|User
     */
    public function getUser()
    {
        if ($this->isLogged()) {
            return User::with('role')->find($_SESSION['user_id']);
        }
    }

    /**
     * Check if user is logged in
     *
     * @return bool
     */
    public function isLogged(): bool
    {
        return isset($_SESSION['user_id']);
    }

    /**
     * Logout user
     */
    public function logout()
    {
        unset($_SESSION['user_id']);
    }
}