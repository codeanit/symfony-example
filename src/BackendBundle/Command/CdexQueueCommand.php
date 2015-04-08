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
class CdexEnqueueCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('cdex:queue:enqueue')
            ->setDescription('Enqueue CDEX Transactions.')
//            ->addArgument('who', InputArgument::OPTIONAL, 'Who to greet.', 'World')
/*
    <info>php %command.full_name%</info>

    The optional argument specifies who to greet:

<info>php %command.full_name%</info> Fabien
*/
            ->setHelp(<<<EOF
The <info>%command.name%</info> command enqueue CDEX TXN to queue
EOF
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
//        $input->getOption('')

        $queueManager = $this->getContainer()->get('cdex_queue_manager');
        $queueManager->enqueueAll();
    }
}
