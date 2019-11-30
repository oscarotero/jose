<?php

namespace Jose\Controllers;

use Jose\Actions\ToggleHide as ToggleHideAction;
use Psr\Http\Message\ServerRequestInterface;

class ToggleHide extends Controller
{
    public function __invoke(ServerRequestInterface $request)
    {
        $toggleHide = new ToggleHideAction(
            $this->app->get('db'),
            $this->app->get('logger')
        );

        $data = $request->getParsedBody();

        echo $toggleHide($data['id']);
    }
}
