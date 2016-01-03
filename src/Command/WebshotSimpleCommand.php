<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

/**
 * Example of simple Process usage
 */
class WebshotSimpleCommand extends Command
{
    protected function configure()
    {
        $this->setName('webshot:simple')
            ->setDescription('Generate thumbnail of given URL')
            ->addArgument('url', InputArgument::REQUIRED, 'Target website URL')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $url = $input->getArgument('url');
        $outputFile = __DIR__ . '/../../output/output.png';

        $process = new Process(
            sprintf(
                './node_modules/.bin/webshot "%s" "%s"',
                $url,
                $outputFile
            )
        );
        $process->run();

        // Pokud proces nedoběhl v pořádku, vypíšeme chybu
        if (!$process->isSuccessful()) {
            if ($process->getExitCode() == 127) {
                $output->writeln('Příkaz webshot nenalezen. Nelze spustit:');
                $output->writeln($process->getCommandLine());
            } else {
                $output->write($process->getErrorOutput());
            }

            return 1;
        }

        $output->writeln('V pořádku hotovo!');

        return 0;
    }
}
