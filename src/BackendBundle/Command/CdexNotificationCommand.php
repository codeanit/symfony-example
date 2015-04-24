<?php

namespace BackendBundle\Command;


use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CdexNotificationCommand
 * @package BackendBundle\Command
 */
class CdexNotificationCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('cdex:notify')
            ->setDescription('Notify the status of transaction.')
            ->addOption(
                'operation',
                null,
                InputOption::VALUE_REQUIRED                                                                                                                             ,
                "Notify 'queued', 'failed' or 'both'.",
                'both'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $operation = $input->getOption('operation');
        $cdexNotifier = $this->getContainer()->get('transaction.notifier');

        switch ($operation) {
            case 'queued':
            case 'failed':
            case 'both':
                $count = $cdexNotifier->notify($operation);
                $output->writeln('<info>'. $count .' Transactions notified successfully.</info>');
                break;

            default:
                throw new \Exception('Fatal Error :: Invalid Operation requested!!');
        }
    }
} 