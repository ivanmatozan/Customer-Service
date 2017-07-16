<?php

namespace App\Services;

use App\Models\Question;
use App\Models\Response as ResponseModel;
use Psr\Container\ContainerInterface as Container;

/**
 * Class QuestionHelper
 */
class QuestionHelper
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * QuestionHelper constructor
     *
     * @param Container $container
     */
    function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Check if logged in user owns accessed question
     *
     * @param Question $question
     * @return bool If user isn't question owner return false, true otherwise
     */
    public function isQuestionOwner(Question $question): bool
    {
        // Currently logged in user
        $user = $this->container->auth->getUser();

        // Check if user owns accessed question
        return $question->user->id == $user->id;
    }

    /**
     * Check if reply form on question detail page is enabled
     *
     * @param Question $question
     * @return bool
     */
    public function enableReplyForm(Question $question): bool
    {
        if ($this->isQuestionClosed($question)) {
            return false;
        }

        // Role of currently logged in user
        $role = $this->container->auth->getUser()->role->name;

        // User with role user can reply only to own questions
        if ($role == 'user' && !$this->isQuestionOwner($question)) {
            return false;
        }

        return $this->enableReplyOrEditDelete($question);
    }

    /**
     * Check if reponse can be edited or deleted
     *
     * @param Question $question
     * @return bool
     */
    public function enableResponseEditDelete(Question $question): bool
    {
        if ($this->isQuestionClosed($question)) {
            return false;
        }

        // Role of currently logged in user
        $role = $this->container->auth->getUser()->role->name;

        // User with role user can only edit|delete responses from own questions
        if ($role == 'user' && !$this->isQuestionOwner($question)) {
            return false;
        }

        return !$this->enableReplyOrEditDelete($question);
    }

    /**
     * Check if question is closed
     *
     * @param Question $question
     * @return bool
     */
    public function isQuestionClosed(Question $question)
    {
        return (bool)$question->closed;
    }

    /**
     * Check which functionality to enable,
     * question reply or edit|delete for responses
     *
     * @param Question $question
     * @return bool For true enable replay, false enable edit|delete
     */
    protected function enableReplyOrEditDelete(Question $question): bool
    {
        // Currently logged in user
        $user = $this->container->auth->getUser();

        // Role of currently logged in user
        $role = $user->role->name;

        // Question's last response
        $lastResponse = $question->responses()->latest()->first();

        // If no responses and user is admin
        if (!$lastResponse && $role == 'admin') {
            return true;
        }

        if ($lastResponse) {
            // Role of last response author
            $lastResponseRole = $lastResponse->user->role->name;

            // If last response is from user
            // and currently logged in user is admin
            if ($lastResponseRole == 'user' && $role == 'admin') {
                return true;
            }

            // If last response is from admin
            // and currently logged in user has role user
            if ($lastResponseRole == 'admin' && $role == 'user') {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if response can be edited or deleted
     *
     * @param ResponseModel $response
     * @return bool
     */
    public function isResponseEditableDeletable(ResponseModel $response): bool
    {
        $question = $response->question;
        $lastResponse = $question->responses()->latest()->first();

        return (
            $this->isResponseOwner($response) &&
            $this->enableResponseEditDelete($question) &&
            // Check if it's last response
            ($response->id == $lastResponse->id)
        );
    }

    /**
     * Check if logged in user owns accessed response
     *
     * @param ResponseModel $response
     * @return bool If user isn't response owner return false, true otherwise
     */
    public function isResponseOwner(ResponseModel $response): bool
    {
        // Currently logged in user
        $user = $this->container->auth->getUser();

        // Check if user owns accessed response
        return $response->user->id == $user->id;
    }
}