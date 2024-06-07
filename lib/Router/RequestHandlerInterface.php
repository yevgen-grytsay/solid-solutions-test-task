<?php

namespace Lib\Router;

use Lib\Request;
use Lib\Response;
use Stringable;

interface RequestHandlerInterface extends Stringable
{
    public function handle(Request $request): Response;
}
