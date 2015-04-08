<?php 

namespace BackendBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use BackendBundle\Library\BDO\Bdo;

class CronCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('cron:update')
            ->setDescription('Execute update from command.')
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

        if ($operation == 'bdo') {
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
        $this->businessLogic = new Bdo();
        
        return $this->businessLogic->getEncryptedPassword("bdoRemit1!");
        
        //return $this->businessLogic->getSignedData();
    }
}
