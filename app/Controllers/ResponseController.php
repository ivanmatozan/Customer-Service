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
        // Validate form
        $validator = $this->validator->validate($request->getParams(), [
            'response' => v::notEmpty()
        ]);

        if ($validator->failed()) {
            $_SESSION['old_form_data'] = $request->getParams();
        } else {
            // Save response to DB
            $questionResponse = new QuestionResponse([
                'text' => $request->getParam('response')
            ]);

            $user = $this->auth->getUser();
            $questionResponse->user()->associate($user);

            $question = Question::find($args['id']);
            $question->responses()->save($questionResponse);

            $this->flash->addMessage('success', 'Response successfully created.');
        }

        return $this->response->withRedirect($this->router->pathFor('question.show', ['id' => $args['id']]));
    }
}