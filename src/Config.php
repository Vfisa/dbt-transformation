<?php

declare(strict_types=1);

namespace DbtTransformation;

use InvalidArgumentException;
use Keboola\Component\Config\BaseConfig;

class Config extends BaseConfig
{
    public function getGitRepositoryUrl(): string
    {
        return $this->getValue(['parameters', 'git', 'repo']);
    }

    public function getGitRepositoryBranch(): ?string
    {
        try {
            return $this->getValue(['parameters', 'git', 'branch']);
        } catch (InvalidArgumentException $e) {
            return null;
        }
    }

    public function getDbtSourceName(): string
    {
        return $this->getValue(['parameters', 'dbt', 'sourceName']);
    }
}
