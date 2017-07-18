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
        // If user_id in session is set user is logged in
        if (isset($_SESSION['user_id'])) {
            return true;
        } else {
            // Attempt login with remember me cookie
            $this->loginWithCookie();
        }

        return isset($_SESSION['user_id']);
    }

    /**
     * Logout user
     */
    public function logout()
    {
        // Unset remember_token from DB
        $user = $this->getUser();
        if ($user) {
            $user->remember_token = null;
            $user->save();
        }

        // Clear session
        unset($_SESSION['user_id']);

        // Delete cookie
        $cookie = $_COOKIE['remember_me'] ?? false;
        if ($cookie) {
            setcookie('remember_me', '', time() - 3600);
        }
    }

    /**
     * Create random token and save it to DB and cookie
     *
     * @return bool
     */
    public function rememberLogin(): bool
    {
        $expiryTimestamp = time() + 60 * 60 * 24 * 30; // 30 days from now

        // Generate random token
        $token = bin2hex(random_bytes(20));

        // Save token to DB
        $user = $this->getUser();
        $user->remember_token = $token;
        $user->save();
        
        return setcookie('remember_me', $token, $expiryTimestamp, '/');
    }

    /**
     * Login user with remember_me cookie
     */
    protected function loginWithCookie()
    {
        $cookie = $_COOKIE['remember_me'] ?? false;

        if ($cookie) {
            $user = User::where('remember_token', $cookie)->first();

            if ($user) {
                $_SESSION['user_id'] = $user->id;
            }
        }
    }
}