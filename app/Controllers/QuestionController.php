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
     * Display user questions
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return mixed
     */
    public function userQuestions(Request $request, Response $response, array $args)
    {
        // Authenticated user
        $user = $this->auth->getUser();

        // Set default active tab
        $activeTab = 'all';

        // Get question Query builder
        $questionsQB = $user->questions();

        if (!empty($args)) {
            switch ($args['tab']) {
                case 'open':
                    $activeTab = 'open';
                    $questionsQB->where('closed', false);
                    break;
                case 'closed':
                    $activeTab = 'closed';
                    $questionsQB->where('closed', true);
                    break;
            }
        }

        // Query for questions
        $questions = $questionsQB->orderBy('updated_at', 'desc')->get();

        return $this->view->render($response, 'question/user-questions.twig', compact('questions', 'activeTab'));
    }

    /**
     * Display all questions
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return mixed
     */
    public function allQuestions(Request $request, Response $response, array $args)
    {
        // Set default active tab
        $activeTab = 'all';

        // Get question Query builder
        $questionsQB = Question::query();

        if (!empty($args)) {
            switch ($args['tab']) {
                case 'open':
                    $activeTab = 'open';
                    $questionsQB->where('closed', false);
                    break;
                case 'closed':
                    $activeTab = 'closed';
                    $questionsQB->where('closed', true);
                    break;
            }
        }

        // Query for questions
        $questions = $questionsQB->orderBy('updated_at', 'desc')->get();

        return $this->view->render($response, 'question/all-questions.twig', compact('questions', 'activeTab'));
    }

    /**
     * Display create question form
     *
     * @param Request $request
     * @param Response $response
     * @return mixed
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
     * @return mixed
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

        return $response->withRedirect($this->router->pathFor('question.user-questions'));
    }

    /**
     * Display edit question form
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return $mixed
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
     * @return $mixed
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

        return $response->withRedirect($this->router->pathFor('question.user-questions'));
    }

    /**
     * Show delete confirmation
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return $mixed
     */
//    public function confirmDelete(Request $request, Response $response, array $args)
//    {
//        return $this->view->render($response, 'templates/confirmation.twig', [
//            'id' => $args['id'],
//            'routeName' => 'question.delete',
//            'message' => 'Are you sure that you want to delete this question? You can\'t revert this.'
//        ]);
//    }

    /**
     * Process question delete
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return $mixed
     */
//    public function delete(Request $request, Response $response, array $args)
//    {
//        // Delete question
//        if ($request->getParam('yes')) {
//            Question::destroy($args['id']);
//
//            $this->flash->addMessage('success', 'Question successfully deleted.');
//        }
//
//        return $response->withRedirect($this->router->pathFor('question.user-questions'));
//    }

    /**
     * Show question closing confirmation
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return $mixed
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
     * @return $mixed
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

        return $response->withRedirect($this->router->pathFor('question.user-questions'));
    }
}