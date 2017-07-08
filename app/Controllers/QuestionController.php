<?php

namespace App\Controllers;

use App\Models\Question;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Respect\Validation\Validator as v;

/**
 * Question controller
 */
class QuestionController extends Controller
{
    /**
     * Display question list
     *
     * @param Request $request
     * @param Response $response
     */
    public function list(Request $request, Response $response)
    {
        // Authenticated user role
        $userRole = $this->auth->getUser()->role->name;

        // Query question depending on user role
        if ($userRole === 'admin') {
            $questionsQB = Question::query();
        } else {
            $questionsQB = Question::whereHas('user.role', function ($query) use ($userRole) {
                $query->where('role.name', $userRole);
            });
        }

        // Switch tabs
        switch ($request->getParam('tab', 'all')) {
            case 'open':
                $activeTab = 'open';
                $questionsQB->where('closed', false);
                break;
            case 'closed':
                $activeTab = 'closed';
                $questionsQB->where('closed', true);
                break;
            default:
                $activeTab = 'all';
        }

        $questions = $questionsQB->orderBy('closed', 'asc')->latest()->get();

        return $this->view->render($response, 'question/list.twig', compact('questions', 'activeTab'));
    }

    /**
     * Display create question form
     *
     * @param Request $request
     * @param Response $response
     */
    public function getCreate(Request $request, Response $response)
    {
        return $this->view->render($response, 'question/create.twig');
    }

    /**
     * Process create question form
     *
     * @param Request $request
     * @param Response $response
     */
    public function postCreate(Request $request, Response $response)
    {
        // Validate form data
        $validator = $this->validator->validate($request->getParams(), [
            'subject' => v::notEmpty(),
            'question' => v::notEmpty()
        ]);

        // Redirect back to form if validation failed
        if ($validator->failed()) {
            $_SESSION['old_form_data'] = $request->getParams();

            return $response->withRedirect($this->router->pathFor('question.create'));
        }

        // Insert into DB after validation passed
        Question::create([
            'subject' => $request->getParam('subject'),
            'text' => $request->getParam('question'),
            'user_id' => $this->auth->getUser()->id,
        ]);

        $this->flash->addMessage('success', 'Question successfully created.');

        return $response->withRedirect($this->router->pathFor('question.list'));
    }

    /**
     * Display edit question form
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     */
    public function getEdit(Request $request, Response $response, array $args)
    {
        $question = Question::find($args['id']);

        return $this->view->render($response, 'question/edit.twig', compact('question'));
    }

    /**
     * Process edit question form
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     */
    public function postEdit(Request $request, Response $response, array $args)
    {
        $question = Question::find($args['id']);

        // Validation rules
        $rules = [];

        // Check if subject is changed
        if ($request->getParam('subject') !== $question->subject) {
            $rules['subject'] = v::notEmpty();

            $question->subject = $request->getParam('subject');
        }

        // Check if question is changed
        if ($request->getParam('question') !== $question->text) {
            $rules['question'] = v::notEmpty();

            $question->text = $request->getParam('question');
        }

        // Don't try to validate and save if nothing changed
        if (!empty($rules)) {
            // Validate form data
            $validator = $this->validator->validate($request->getParams(), $rules);

            // Redirect back to form if validation failed
            if ($validator->failed()) {
                // Add input data to session
                $_SESSION['old_form_data'] = $request->getParams();

                return $response->withRedirect($this->router->pathFor('question.edit', ['id' => $args['id']]));
            }

            // Update question in DB
            $question->save();

            $this->flash->addMessage('success', 'Question successfully edited.');
        }

        return $response->withRedirect($this->router->pathFor('question.list'));
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
            'routeName' => 'question.delete',
            'message' => 'Are you sure that you want to delete this question? You can\'t revert this.'
        ]);
    }

    /**
     * Process question delete
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     */
    public function delete(Request $request, Response $response, array $args)
    {
        // Delete question
        if ($request->getParam('yes')) {
            Question::destroy($args['id']);

            $this->flash->addMessage('success', 'Question successfully deleted.');
        }

        return $response->withRedirect($this->router->pathFor('question.list'));
    }

    /**
     * Show question closing confirmation
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     */
    public function confirmClose(Request $request, Response $response, array $args)
    {
        // Render confirmation view
        return $this->view->render($response, 'templates/confirmation.twig', [
            'id' => $args['id'],
            'routeName' => 'question.close',
            'message' => 'Are you sure that you want to close question? You can\'t revert this.'
        ]);
    }

    /**
     * Process question closing
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     */
    public function close(Request $request, Response $response, array $args)
    {
        // Close question
        if ($request->getParam('yes')) {
            Question::where('id', $args['id'])->update([
                'closed' => true
            ]);

            $this->flash->addMessage('success', 'Question successfully closed.');
        }

        return $response->withRedirect($this->router->pathFor('question.list'));
    }

    /**
     * Display question detailed view
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     */
    public function show(Request $request, Response $response, array $args)
    {
        $question = Question::with(['responses' => function ($query) {
            $query->orderBy('created_at', 'asc');
        }])->find($args['id']);

        // Question's last response
        $lastResponse = $question->responses()->latest()->first();

        return $this->view->render($response, 'question/show.twig', [
            'question' => $question,
            'lastResponse' => $lastResponse
        ]);
    }
}