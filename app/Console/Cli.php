<?php namespace Valle\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface as Input;
use Symfony\Component\Console\Output\OutputInterface as Output;

/**
 * Clim Uma maneira rápida de usar o Slim
 * em CLI com seu container
 */
class Cli extends Command
{
    protected function configure()  
    {
        $this->setName('cli:run')
             ->setDescription('Outputs slim env code CLI ;)');
    }

    public function execute(Input $input, Output $output)
    {

        $config = new \Psy\Configuration([
            'defaultIncludes' => [
                __DIR__ . '/dump_cli.php'
            ],
            'updateCheck' => 'never'
        ]);
        $shell = new \Psy\Shell($config);

        $shell->run();
    }
}