<?php

namespace Lib;

interface ActionInterface
{
    public function index(RequestInterface $request): ResponseInterface;
}
