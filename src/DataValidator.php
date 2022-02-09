<?php

namespace ShooglyPeg\DataValidator;

use League\Flysystem\Filesystem;
use Opis\JsonSchema\Errors\ErrorFormatter;
use Opis\JsonSchema\Validator;
use ShooglyPeg\DataValidator\Config;
use ShooglyPeg\DataValidator\Interfaces\DataValidator as DataValidatorInterface;
use ShooglyPeg\DataValidator\Exceptions\JsonDataInvalid;
use ShooglyPeg\DataValidator\Exceptions\JsonSchemaNotFound;
use ShooglyPeg\DataValidator\Exceptions\JsonSchemaValidationFailed;

final class DataValidator implements DataValidatorInterface
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
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->filesystem = new Filesystem($config->getAdapter());
        $this->validator = new Validator();
        $this->validator->resolver()->registerPrefix(
            $config->getPrefix(),
            $config->getSchemas()
        );
        $this->errorFormatter = new ErrorFormatter();
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
