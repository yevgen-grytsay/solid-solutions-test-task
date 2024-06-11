<?php

namespace Lib;

interface ResponseInterface
{
    public function getStatus(): int;
    public function getStatusText(): string;
    public function getHeaders(): array;
    public function render(): string;
}
