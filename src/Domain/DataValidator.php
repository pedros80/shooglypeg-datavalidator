<?php

namespace ShooglyPeg\DataValidator\Domain;

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
