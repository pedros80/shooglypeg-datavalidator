<?php

namespace ShooglyPeg\DataValidator\Interfaces;

interface DataValidator
{
    /**
     * @param string $data
     * @param string $schema
     * @return bool
     * @throws JsonSchemaValidationFailed
     */
    public function validate(string $data, string $schema): bool;
}
