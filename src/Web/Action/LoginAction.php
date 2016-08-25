<?php

namespace Action;

use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Views\PhpRenderer;

class LoginAction
{
    /**
     * @var array
     */
    private $renderer;

    public function __construct(PhpRenderer $renderer)
    {
        $this->renderer = $renderer;
    }

    public function __invoke(Request $request, Response $response, $args)
    {
        // Verify if user is authenticated. If true, redirect to home
        if (isset($_SESSION['user'])) {
            $response = $response->withRedirect("/home");
            return $response;
        } else {
            return $this->renderer->render($response, 'login.phtml', $args);
        }
    }
}