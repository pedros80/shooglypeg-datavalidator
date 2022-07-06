<?php

namespace ShooglyPeg\DataValidator;

use League\Flysystem\Filesystem;
use League\Flysystem\StorageAttributes;
use Opis\JsonSchema\Errors\ErrorFormatter;
use Opis\JsonSchema\Validator;
use ShooglyPeg\DataValidator\Config;
use ShooglyPeg\DataValidator\Interfaces\DataValidator as DataValidatorInterface;
use ShooglyPeg\DataValidator\Exceptions\JsonDataInvalid;
use ShooglyPeg\DataValidator\Exceptions\JsonSchemaNotFound;
use ShooglyPeg\DataValidator\Exceptions\JsonSchemaValidationFailed;

final class DataValidator implements DataValidatorInterface
{
    private Validator $validator;
    private ErrorFormatter $errorFormatter;
    private Filesystem $filesystem;
    private string $prefix;

    public function __construct(Config $config)
    {
        $this->filesystem = new Filesystem($config->getAdapter());
        $this->validator  = new Validator();
        $this->validator->resolver()->registerPrefix(
            $config->getPrefix(),
            $config->getSchemas()
        );
        $this->errorFormatter = new ErrorFormatter();
        $this->prefix         = $config->getPrefix();
    }

    /**
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

    private function decodeData(string $data): object
    {
        $json = json_decode($data);

        if (JSON_ERROR_NONE !== json_last_error()) {
            throw JsonDataInvalid::fromError(json_last_error_msg());
        }

        return $json;
    }

    private function schemaExists(string $schema): bool
    {
        $found = $this->filesystem
                    ->listContents('')
                    ->filter(fn (StorageAttributes $attributes) => $attributes->isFile() && $attributes->path() === "{$schema}.json")
                    ->toArray();

        if (!$found) {
            throw JsonSchemaNotFound::fromSchema($schema);
        }

        return (bool) $found;
    }

    private function getSchemaUrl(string $schema): string
    {
        return "{$this->prefix}/{$schema}.json";
    }
}
