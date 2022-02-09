<?php

namespace ShooglyPeg\DataValidator;

use League\Flysystem\AdapterInterface;

final class Config
{
    /**
     * @param array $config
     */
    public function __construct(
        private array $config
    ) {}

    /**
     * @return string
     */
    public function getSchemas(): string
    {
        return $this->config['schemas'];
    }

    /**
     * @return string
     */
    public function getPrefix(): string
    {
        return $this->config['prefix'];
    }

    /**
     * @return
     */
    public function getAdapter(): AdapterInterface
    {
        return $this->config['adapter'];
    }
}
