<?php

namespace App\Http\Controllers;

abstract class Controller
{
    /**
     * Only accept filter values that exist in the allowed list.
     *
     * @param  list<string>  $allowed
     */
    protected function allowedFilter(mixed $value, array $allowed): ?string
    {
        return is_string($value) && in_array($value, $allowed, true) ? $value : null;
    }
}
