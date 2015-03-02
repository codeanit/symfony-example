<?php 

namespace BackendBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use LibraryBundle\BusinessLogic\BDO\BdoBL;

class CronCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('demo:greet')
            ->setDescription('Greet someone')
            ->addArgument(
                'operation',
                InputArgument::OPTIONAL,
                'What operation to perform?'
            )
            ->addOption(
                'yell',
                null,
                InputOption::VALUE_NONE,
                'If set, the task will yell in uppercase letters'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $return = "";
        $operation = $input->getArgument('operation');

        if ($operation == 'BDO') {
            $output->writeln("Calling BDO");
            $return = $this->_bdoTest();
//            $text = 'Hello '. $operation;
        }

//        if ($input->getOption('yell')) {
//            $text = strtoupper($text);
//        }

        $output->writeln($return);
    }


    private function _bdoTest()
    {
        $this->businessLogic = new BdoBL();
        return $this->businessLogic->getEncryptedPassword("test");
    }
}
