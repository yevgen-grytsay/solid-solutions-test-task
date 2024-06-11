<?php

namespace Lib;

interface RequestInterface
{
    public function getPath(): string;

    public function getMethod(): string;

    public function getQueryParams(): array;

    public function isJson(): bool;
}
