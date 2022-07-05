<?php

namespace ShooglyPeg\DataValidator;

use League\Flysystem\FilesystemAdapter;

final class Config
{
    public function __construct(
        private array $config
    ) {}

    public function getSchemas(): string
    {
        return $this->config['schemas'];
    }

    public function getPrefix(): string
    {
        return $this->config['prefix'];
    }

    public function getAdapter(): FilesystemAdapter
    {
        return $this->config['adapter'];
    }
}
