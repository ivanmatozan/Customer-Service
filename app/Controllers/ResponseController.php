<?php

namespace App\Controllers;

use App\Models\Question;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Models\Response as QuestionResponse;
use Respect\Validation\Validator as v;

/**
 * Response controller
 */
class ResponseController extends Controller
{
    /**
     * Process create response
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     */
    public function postCreate(Request $request, Response $response, array $args)
    {
        $question = Question::with(['user', 'responses'])->find($args['id']);

        // If question doesn't exist
        // or reply form isn't enabled
        // redirect back to question list
        if (!$question || !$this->helper->enableReplyForm($question)) {
            return $response->withRedirect($this->router->pathFor('question.list'));
        }

        // Validate form
        $validator = $this->validator->validate($request->getParams(), [
            'response' => v::notEmpty()
        ]);

        if ($validator->failed()) {
            $_SESSION['old_form_data'] = $request->getParams();
        } else {
            $questionResponse = new QuestionResponse([
                'text' => $request->getParam('response')
            ]);

            $user = $this->auth->getUser();
            $questionResponse->user()->associate($user);

            // Save response to DB
            $question->responses()->save($questionResponse);

            $this->flash->addMessage('success', 'Response successfully created.');
        }

        return $this->response->withRedirect($this->router->pathFor('question.show', ['id' => $args['id']]));
    }

    /**
     * Display edit response form
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     */
    public function getEdit(Request $request, Response $response, array $args)
    {
        $questionResponse = QuestionResponse::with('user', 'question')->find($args['id']);

        // Redirect if response doesn't exist
        // or if response can't be edited or deleted
        if (!$response || !$this->helper->isResponseEditableDeletable($questionResponse)) {
            $this->flash->addMessage('error', 'You can not edit this response.');

            return $response->withRedirect($this->router->pathFor('question.show', [
                'id' => $questionResponse->question->id
            ]));
        }

        return $this->view->render($response, 'response/edit.twig', compact('questionResponse'));
    }

    /**
     * Process response edit form
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     */
    public function postEdit(Request $request, Response $response, array $args)
    {
        $questionResponse = QuestionResponse::with('user', 'question')->find($args['id']);

        // Redirect if response doesn't exist
        // or if response can't be edited or deleted
        if (!$response || !$this->helper->isResponseEditableDeletable($questionResponse)) {
            $this->flash->addMessage('error', 'You can not edit this response.');

            return $response->withRedirect($this->router->pathFor('question.show', [
                'id' => $questionResponse->question->id
            ]));
        }

        // Validate form
        $validator = $this->validator->validate($request->getParams(), [
            'response' => v::notEmpty()
        ]);

        if ($validator->failed()) {
            $_SESSION['old_form_data'] = $request->getParams();

            return $response->withRedirect($this->router->pathFor('response.edit', ['id' => $args['id']]));
        }

        // Update response in DB
        $questionResponse->text = $request->getParam('response');
        $questionResponse->save();

        $this->flash->addMessage('success', 'Response successfully edited.');

        return $response->withRedirect($this->router->pathFor('question.show', [
            'id' => $questionResponse->question->id
        ]));
    }

    /**
     * Show delete confirmation
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     */
    public function confirmDelete(Request $request, Response $response, array $args)
    {
        return $this->view->render($response, 'templates/confirmation.twig', [
            'id' => $args['id'],
            'routeName' => 'response.delete',
            'message' => 'Are you sure that you want to delete this response? You can\'t revert this.'
        ]);
    }

    /**
     * Process response delete
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     */
    public function delete(Request $request, Response $response, array $args)
    {
        $questionResponse = QuestionResponse::with('user', 'question')->find($args['id']);

        // Redirect if response doesn't exist
        // or if response can't be edited or deleted
        if (!$response || !$this->helper->isResponseEditableDeletable($questionResponse)) {
            $this->flash->addMessage('error', 'You can not delete this response.');

            return $response->withRedirect($this->router->pathFor('question.show', [
                'id' => $questionResponse->question->id
            ]));
        }

        $questionId = $questionResponse->question->id;

        // Delete response
        if ($request->getParam('yes')) {
            $questionResponse->delete();

            $this->flash->addMessage('success', 'Response successfully deleted.');
        }

        return $response->withRedirect($this->router->pathFor('question.show', [
            'id' => $questionResponse->question->id
        ]));
    }
}