<?php

namespace App\Validation;

use Psr\Http\Message\ServerRequestInterface as Request;
use Respect\Validation\Exceptions\NestedValidationException;

class Validator
{
    /**
     * Array with validation error messages
     *
     * @var array
     */
    protected $errors = [];

    /**
     * Check form data with validation rules
     *
     * @param array $data Form data
     * @param array $rules Rules to check
     * @return Validator
     */
    public function validate(array $data, array $rules): Validator
    {
        foreach ($rules as $input => $rule) {
            try {
                $rule->setName(ucfirst($input))->assert($data[$input]);
            } catch (NestedValidationException $exception) {
                $this->errors[$input] = $exception->getMessages();
            }
        }

        // Add errors to session
        $_SESSION['validation_errors'] = $this->errors;

        return $this;
    }

    /**
     * Check if validation failed
     *
     * @return bool If validation failed returns true
     */
    public function failed(): bool
    {
        return !empty($this->errors);
    }
}