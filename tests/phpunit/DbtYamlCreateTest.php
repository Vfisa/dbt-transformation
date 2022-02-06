<?php

declare(strict_types=1);

namespace Keboola\DbExtractor\Tests;

use DbtTransformation\DbtYamlCreateService\DbtProfileYamlCreateService;
use DbtTransformation\DbtYamlCreateService\DbtSourceYamlCreateService;
use Generator;
use Keboola\Component\UserException;
use PHPUnit\Framework\TestCase;

class DbtYamlCreateTest extends TestCase
{
    protected string $dataDir = __DIR__ . '/../data';
    protected string $providerDataDir = __DIR__ . '/data';

    /**
     * @dataProvider profileYamlDataProvider
     * @throws \Keboola\Component\UserException
     */
    public function testCreateProfileYamlFromConfigData(
        array $config,
        string $generatedFilePath,
        string $expectedSourceFilePath
    ): void
    {
        $service = new DbtProfileYamlCreateService();
        $service->dumpYaml(
            $this->dataDir,
            sprintf('%s/dbt_project.yml', $this->providerDataDir),
            $config['authorization']['workspace']);
        self::assertFileEquals($expectedSourceFilePath, $generatedFilePath);
    }

    /**
     * @dataProvider profileYamlDataProvider
     * @throws \Keboola\Component\UserException
     */
    public function testCreateProfileYamlMissingDbtProjectFile(array $config): void
    {
        $this->expectException(UserException::class);
        $this->expectErrorMessage(sprintf('Missing file %s/non-exist.yml in your project', $this->providerDataDir));

        $service = new DbtProfileYamlCreateService();
        $service->dumpYaml(
            $this->dataDir,
            sprintf('%s/non-exist.yml', $this->providerDataDir),
            $config['authorization']['workspace']);
    }

    /**
     * @dataProvider sourceYamlDataProvider
     */
    public function testCreateSourceYamlFromConfigData(
        array $config,
        string $generatedFilePath,
        string $expectedSourceFilePath
    ): void
    {
        $service = new DbtSourceYamlCreateService();
        $service->dumpYaml($this->dataDir, $config['authorization']['workspace'], $config['storage']['input']['tables']);
        self::assertFileEquals($expectedSourceFilePath, $generatedFilePath);
    }

    /**
     * @return Generator<string, string, string>
     * @throws \JsonException
     */
    public function ProfileYamlDataProvider(): Generator
    {
        yield [
            'config' => $this->getConfig(),
            'generatedFilePath' => sprintf('%s/.dbt/profile.yml', $this->dataDir),
            'expectedSourceFilePath' => sprintf('%s/expectedProfile.yml', $this->providerDataDir),
        ];
    }

    /**
     * @return Generator<string, string, string>
     * @throws \JsonException
     */
    public function sourceYamlDataProvider(): Generator
    {
        $config = $this->getConfig();

        yield [
            'config' => $config,
            'generatedFilePath' => sprintf('%s/models/src_%s.yml', $this->dataDir, $config['authorization']['workspace']['schema']),
            'expectedSourceFilePath' => sprintf('%s/expectedSource.yml', $this->providerDataDir),
        ];
    }

    /**
     * @return mixed
     * @throws \JsonException
     */
    protected function getConfig()
    {
        return json_decode(
            file_get_contents(sprintf('%s/config.json', $this->providerDataDir)),
            true,
            512,
            JSON_THROW_ON_ERROR
        );
    }
}