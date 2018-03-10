<?php

namespace Jose\Controllers;

use Jose\Actions\ToggleSave as ToggleSaveAction;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Middlewares\Utils\Factory;

class ToggleSave extends Controller
{
    public function __invoke(ServerRequestInterface $request)
    {
        $toggleSave = new ToggleSaveAction(
            $this->app->get('db'),
            $this->app->get('logger')
        );

        $data = $request->getParsedBody();

        echo $toggleSave($data['id']);
    }
}
