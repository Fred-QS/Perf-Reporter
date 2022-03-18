<?php

namespace App\Command\PerfReport;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\ExceptionInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Smile\Performers\PerformancesLogger;

#[AsCommand(
    name: 'app:perfReport:delete',
    description: 'Delete "reports/ folder.',
    aliases: ['app:report-delete'],
    hidden: false
)]
class DeleteFolderCommand extends Command
{
    protected static $defaultName = 'app:perfReport:delete';
    protected static $defaultDescription = 'Delete "reports/ folder.';

    protected function configure(): void
    {
        $this->setHelp('This command allows you to delete the reports/ folder, created by using the smilian/perfs-reporter package. The folder will be created automatically on next use of this package.')
        ;
    }

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