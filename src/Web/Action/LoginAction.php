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
        $userEmail = filter_var($data['email'], FILTER_SANITIZE_STRING);
        $userPass = md5(filter_var($data['password'], FILTER_SANITIZE_STRING));

        if (empty($userEmail) || empty($userPass)) {
            $_POST['error'] = '<p class="error">Incorrect login!</p>';

            return $this->renderer->render($response, 'login.phtml', $args);
        } else {
            $userMapper = new UserMapper($this->db);
            $user = $userMapper->loginUser($userEmail, $userPass);
            $userName = $user->getName();
            if (empty($userName)) {
                $_POST['error'] = '<p class="error">Incorrect login!</p>';

                return $this->renderer->render($response, 'login.phtml', $args);
            } else {
                $_SESSION['user'] = $userName;
                $response = $response->withRedirect('/home');

                return $response;
            }
        }
    }
}
