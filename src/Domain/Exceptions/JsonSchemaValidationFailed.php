<?php

declare(strict_types=1);

namespace ShooglyPeg\DataValidator\Domain\Exceptions;

use ShooglyPeg\DataValidator\Domain\Exceptions\JsonSchemaException;

final class JsonSchemaValidationFailed extends JsonSchemaException
{
    /**
     * @param string $message
     */
    private function __construct(string $message)
    {
        parent::__construct($message, 400);
    }

    /**
     * @param array $errors
     * @return JsonSchemaValidationFailed
     */
    public static function fromErrors(array $errors): JsonSchemaValidationFailed
    {
        $errors = implode(', ', $errors);
        return new JsonSchemaValidationFailed("Json Schema Validation Failed: {$errors}");
    }
}
