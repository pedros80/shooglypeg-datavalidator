<?php

declare(strict_types=1);

namespace ShooglyPeg\DataValidator\Exceptions;

use ShooglyPeg\DataValidator\Exceptions\JsonSchemaException;

final class JsonSchemaNotFound extends JsonSchemaException
{
    private function __construct(string $message)
    {
        parent::__construct($message, 404);
    }

    public static function fromSchema(string $schema): JsonSchemaNotFound
    {
        return new JsonSchemaNotFound("Json Schema '{$schema}.json' not found.");
    }
}
