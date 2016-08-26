<?php

namespace Web\Action;

use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Views\PhpRenderer;

class LogoutAction
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
        session_unset();
        session_destroy();
        $response = $response->withRedirect('/login');

        return $response;
    }
}
