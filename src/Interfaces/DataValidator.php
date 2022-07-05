<?php

namespace ShooglyPeg\DataValidator\Interfaces;

interface DataValidator
{
    /**
     * @throws JsonSchemaValidationFailed
     */
    public function validate(string $data, string $schema): bool;
}
