<?php

namespace Web\Action;

use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Views\PhpRenderer;
use Web\Domain\UserMapper;
use Slim\PDO\Database;

class LoginAction
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
        $user_email = filter_var($data['email'], FILTER_SANITIZE_STRING);
        $user_pass = filter_var($data['password'], FILTER_SANITIZE_STRING);

        if (empty($user_email) || empty($user_pass)) {
            $_POST['error'] = '<p class="error">Incorrect login!</p>';
            return $this->renderer->render($response, 'login.phtml', $args);
        } else {
            $user_mapper = new UserMapper($this->db);
            $user = $user_mapper->loginUser($user_email, $user_pass);
            $user_name = $user->getName();
            if (empty($user_name)) {
                $_POST['error'] = '<p class="error">Incorrect login!</p>';
                return $this->renderer->render($response, 'login.phtml', $args);
            } else {
                $_SESSION['user'] = $user_name;

                $response = $response->withRedirect('/home');
                return $response;
            }
        }
    }
}