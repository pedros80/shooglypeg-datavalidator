<?php

declare(strict_types=1);

namespace ShooglyPeg\DataValidator\Exceptions;

use ShooglyPeg\DataValidator\Exceptions\JsonSchemaException;

final class JsonSchemaValidationFailed extends JsonSchemaException
{
    private function __construct(string $message)
    {
        parent::__construct($message, 400);
    }

    public static function fromErrors(array $errors): JsonSchemaValidationFailed
    {
        $errors = implode(', ', $errors);
        return new JsonSchemaValidationFailed("Json Schema Validation Failed: {$errors}");
    }
}
