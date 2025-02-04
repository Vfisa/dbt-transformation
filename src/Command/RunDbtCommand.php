<?php

declare(strict_types=1);

namespace DbtTransformation\Command;

use DbtTransformation\DbtRunService;
use Keboola\Component\UserException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class RunDbtCommand extends Command
{
    /**
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingNativeTypeHint
     * @var string
     */
    protected static $defaultName = 'app:run-dbt-command';
    /**
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingNativeTypeHint
     * @var string
     */
    protected static $defaultDescription = 'Runs "dbt run" in DBT CLI';

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $helper = $this->getHelper('question');
        $questionModelNames = new Question('Enter names of models you want to run separated by space ' .
            '(optional, if not specified, all models will be run): ');
        $modelNames = $helper->ask($input, $output, $questionModelNames);

        $dbtRunService = new DbtRunService(sprintf('%s/dbt-project', CloneGitRepositoryCommand::DATA_DIR));
        try {
            $output->writeln($dbtRunService->run($modelNames ? explode(' ', $modelNames) : []));
        } catch (UserException $e) {
            $output->writeln($e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
