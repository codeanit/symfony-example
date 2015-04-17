<?php

namespace BackendBundle\Command;

use BackendBundle\Library\Queue\QueueManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Hello World command for demo purposes.
 *
 * You could also extend from Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand
 * to get access to the container via $this->getContainer().
 *
 * @author Tobias Schultze <http://tobion.de>
 */
class CdexQueueCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('cdex:queue')
            ->setDescription('Enqueue CDEX Transactions.')
            ->addArgument('operation', InputArgument::OPTIONAL, 'What operation.', 'both')
/*
    <info>php %command.full_name%</info>

    The optional argument specifies who to greet:

<info>php %command.full_name%</info> Fabien
*/
            ->setHelp(<<<EOF
The <info>%command.name%</info> command enqueue CDEX TXN to queue

<info>php %command.full_name%</info>

    The optional argument specifies what operation:

<info>php %command.full_name%</info> [enqueue|execute|both]
EOF
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $operation = $input->getArgument('operation');
        $queueManager = $this->getContainer()->get('cdex_queue_manager');
        $queuedTxn = 0;
        $processedTxn = 0;

        switch ($operation) {
            case 'enqueue':
                $queuedTxn = $queueManager->enqueueAll();
                break;

            case 'execute':
                $processedTxn = (int) $queueManager->processAll();
                break;

            case 'both':
                $queuedTxn = $queueManager->enqueueAll();
                $processedTxn = (int) $queueManager->processAll();
                break;

            case 'confirm':
                $queuedTxn = $queueManager->confirmTransactionStatus();
                break;


            default:
                $output->writeln('<error>Invalid Operation "' . $operation . '".</error>');
        }

        if ($queuedTxn > 0 ) {
            $output->writeln('<info>'. $queuedTxn .' transactions queued for processing.</info>');
        }
        if ($processedTxn > 0 ) {
            $output->writeln('<info>'. $processedTxn .' queues processed.</info>');
        }
    }
}
