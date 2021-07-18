<?php

namespace ShooglyPeg\DataValidator;

use League\Flysystem\Filesystem;
use Opis\JsonSchema\Errors\ErrorFormatter;
use Opis\JsonSchema\Validator;
use ShooglyPeg\DataValidator\Domain\Exceptions\JsonDataInvalid;
use ShooglyPeg\DataValidator\Domain\Exceptions\JsonSchemaNotFound;
use ShooglyPeg\DataValidator\Domain\Exceptions\JsonSchemaValidationFailed;

final class DataValidator
{
    /**
     * @var Validator
     */
    private Validator $validator;

    /**
     * @var ErrorFormatter
     */
    private ErrorFormatter $errorFormatter;

    /**
     * @var Filesystem
     */
    private Filesystem $filesystem;

    /**
     * @param Validator $validator
     * @param ErrorFormatter $errorFormatter
     * @param Filesystem $filesystem
     */
    public function __construct(
        Validator $validator,
        ErrorFormatter $errorFormatter,
        Filesystem $filesystem
    ) {
        $this->validator      = $validator;
        $this->errorFormatter = $errorFormatter;
        $this->filesystem     = $filesystem;
    }

    /**
     * @param string $data
     * @param string $schema
     * @return bool
     * @throws JsonSchemaValidationFailed
     */
    public function validate(string $data, string $schema): bool
    {
        $this->schemaExists($schema);

        $json   = $this->decodeData($data);
        $result = $this->validator->validate($json, $this->getSchemaUrl($schema));

        if (!$result->isValid()) {
            throw JsonSchemaValidationFailed::fromErrors(
                array_map(function (array $error) {
                    return $error[0];
                }, $this->errorFormatter->format($result->error(), true))
            );
        }

        return true;
    }

    /**
     * @param string $data
     * @return object
     * @throws JsonDataInvalid
     */
    private function decodeData(string $data): object
    {
        $json = json_decode($data);

        if (JSON_ERROR_NONE !== json_last_error()) {
            throw JsonDataInvalid::fromError(json_last_error_msg());
        }

        return $json;
    }

    /**
     * @param string $schema
     * @return bool
     * @throws JsonSchemaNotFound
     */
    private function schemaExists(string $schema): bool
    {
        $found = array_filter($this->filesystem->listContents(), function ($file) use ($schema) {
            return $file['path'] === "{$schema}.json";
        });

        if (!$found) {
            throw JsonSchemaNotFound::fromSchema($schema);
        }

        return (bool) $found;
    }

    /**
     * @param string $schema
     * @return string
     */
    private function getSchemaUrl(string $schema): string
    {
        return "http://www.shooglypeg.co.uk/{$schema}.json";
    }
}
