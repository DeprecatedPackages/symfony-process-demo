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
 * Example of asynchronous Process usage
 */
class WebshotMultipleCommand extends Command
{
    const PROXY = 'http://my.proxy:8118/';

    protected function configure()
    {
        $this->setName('webshot:multiple')
            ->setDescription('Generate thumbnail of given URLs')
            ->addArgument('url', InputArgument::IS_ARRAY, 'Target websites URL, one or many')
            ->addOption('use-proxy', null, InputOption::VALUE_NONE, 'Use proxy when retrieving website')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $urls = $input->getArgument('url');
        $useProxy = $input->getOption('use-proxy');
        $outputFilePrefix = __DIR__ . '/../../output/output-';

        /** @var Process[] $processSet */
        $processSet = [];
        $i = 0;
        foreach ($urls as $url) {
            $builder = (new ProcessBuilder())
                ->setPrefix('./node_modules/.bin/webshot')
                ->setTimeout(5)
                ->add('--window-size=1280/1024')
                ->add($url)
                ->add($outputFilePrefix . $i++ . '.png')
            ;

            if ($useProxy) {
                $builder->setEnv('http_proxy', self::PROXY);
            }

            $process = $builder->getProcess();
            $process->start();
            $processSet[$url] = $process;
        }

        sleep(1);

        while (!empty($processSet)) {
            foreach ($processSet as $url => &$process) {
                // Nejprve zkontrolujeme, zda-li již nenastal timeout procesu
                $process->checkTimeout();
                // Pokud proces již neběží, odebereme jej z pole a vypíšeme výsledek
                if (!$process->isRunning()) {
                    unset($processSet[$url]);
                    if (!$process->isSuccessful()) {
                        $output->writeln('Chyba při zpracování URL ' . $url);
                        $output->write($process->getErrorOutput());
                    } else {
                        $output->writeln('Hotovo: ' . $url);
                    }

                    $output->writeln('Zbývajících procesů: ' . count($processSet));
                }
            }

            // Počkáme 100 ms do další kontroly běžících procesů
            usleep(100000);
        }

        return 0;
    }
}
