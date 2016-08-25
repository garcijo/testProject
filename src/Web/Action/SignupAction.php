<?php

namespace Web\Action;

use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Views\PhpRenderer;
use Web\Domain\UserMapper;
use Slim\PDO\Database;

class SignupAction
{
    /**
     * @var array
     */
    private $renderer;
    private $db;

    public function __construct(
        PhpRenderer $renderer,
        Database $db
    ) {
        $this->renderer = $renderer;
        $this->db = $db;
    }

    public function __invoke(Request $request, Response $response, $args)
    {
        $data = $request->getParsedBody();
        $user_data = [];
        $user_name = filter_var($data['name'], FILTER_SANITIZE_STRING);
        $user_email = filter_var($data['email'], FILTER_SANITIZE_STRING);
        $user_pass = filter_var($data['password'], FILTER_SANITIZE_STRING);
        // work out the component
        $user_mapper = new UserMapper($this->db);
        //First check the email doesn't exist yet
        $user = $user_mapper->searchUser($user_email);
        if (!empty($user->getName())) {
            $_POST['error'] = '<p class="error">That email address already exists!</p>';

            return $this->renderer->render($response, 'login.phtml', $args);
        } else {
            $user = $user_mapper->createUser($user_name, $user_email, $user_pass);
            $_SESSION['user'] = $user_name;
            $response = $response->withRedirect('/home');

            return $response;
        }
    }
}
