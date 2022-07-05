<?php

declare(strict_types=1);

namespace ShooglyPeg\DataValidator\Exceptions;

use ShooglyPeg\DataValidator\Exceptions\JsonSchemaException;

final class JsonDataInvalid extends JsonSchemaException
{
    private function __construct(string $message)
    {
        parent::__construct($message, 400);
    }

    public static function fromError(string $error): JsonDataInvalid
    {
        return new JsonDataInvalid("Json Data Invalid: {$error}");
    }
}
