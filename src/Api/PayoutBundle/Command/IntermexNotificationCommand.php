<?php 

namespace Api\PayoutBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class IntermexNotificationCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('notification')
             ->setDescription('Call ThirdParty Notification Service')
             ->addArgument('service', InputArgument::OPTIONAL, 'Name of Third Party Service')
             ->addArgument('type', InputArgument::OPTIONAL, 'Method for Third Party Service');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $service_name = $input->getArgument('service');
        $type = $input->getArgument('type');
        if(strtolower($service_name == "intermex"))
        {
            $intermex = $this->getContainer()->get('intermex');
            if($type=='confirmation')
            {
                $text = $intermex->consultaCambios();                
                $output->writeln($text);
            }            
        }
    }
}
