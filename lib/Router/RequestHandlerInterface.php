<?php

namespace Lib\Router;

use Lib\Request;
use Lib\Response;

interface RequestHandlerInterface
{
    public function handle(Request $request): Response;
}
