<?php

namespace Lib;

use RuntimeException;

class DefaultJsonRenderer implements JsonRendererInterface
{
    private int $flags;
    public function __construct(int $flags = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
    {
        $this->flags = $flags;
    }

    public function render(mixed $value): string
    {
        $result = json_encode($value, $this->flags);
        if (false === $result) {
            throw new RuntimeException('Can not encode JSON');
        }

        return $result;
    }
}
