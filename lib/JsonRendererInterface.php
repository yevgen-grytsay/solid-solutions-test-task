<?php

namespace Lib;

interface JsonRendererInterface
{
    public function render(mixed $value): string;
}
