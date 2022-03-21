<?php

namespace App\Command\PerfReporter;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\ExceptionInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Smile\PerfreporterBundle\Performers\PerformancesLogger;

#[AsCommand(
    name: 'app:perfReport:delete',
    description: 'Delete "reports/ folder.',
    aliases: ['app:report-delete'],
    hidden: false
)]
class DeleteFolderCommand extends Command
{
    /**
     * @var string
     */
    protected static string $defaultName = 'app:perfReport:delete';
    /**
     * @var string
     */
    protected static string $defaultDescription = 'Delete "reports/ folder.';

    /**
     * @return void
     */
    protected function configure(): void
    {
        $this->setHelp('This command allows you to delete the reports/ folder, created by using the smilian/perfs-reporter package. The folder will be created automatically on next use of this package.')
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln([
            '',
            'Delete Reports Folder',
            '=====================',
            '',
        ]);

        try {

            $delete = PerformancesLogger::deleteReports();
            $output->writeln([
                $delete,
                ''
            ]);
            return Command::SUCCESS;

        } catch (ExceptionInterface $e) {

            $output->writeln([
                'Error : ' . $e->getMessage(),
                ''
            ]);
            return Command::FAILURE;
        }
    }
}