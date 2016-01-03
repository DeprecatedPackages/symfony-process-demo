<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;

/**
 * Example of Process and ProcessBuilder usage
 */
class WebshotProcessBuilderCommand extends Command
{
    const PROXY = 'http://my.proxy:8118/';

    protected function configure()
    {
        $this->setName('webshot:builder')
            ->setDescription('Generate thumbnail of given URL')
            ->addArgument('url', InputArgument::REQUIRED, 'Target website URL')
            ->addOption('use-proxy', null, InputOption::VALUE_NONE, 'Use proxy when retrieving website')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $url = $input->getArgument('url');
        $useProxy = $input->getOption('use-proxy');
        $outputFile = __DIR__ . '/../../output/output.png';

        $builder = (new ProcessBuilder())
            ->setPrefix('./node_modules/.bin/webshot')
            ->setTimeout(5)
            ->add('--window-size=1280/1024')
            ->add($url)
            ->add($outputFile)
        ;

        if ($useProxy) {
            $builder->setEnv('http_proxy', self::PROXY);
        }

        $process = $builder->getProcess();

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
