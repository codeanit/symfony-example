<?php

namespace BackendBundle\Library\Notification;

use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\Container;


/**
 * Class Notifier
 * @package BackendBundle\Library\Notification
 */
class Notifier {

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var Container
     */
    private $container;

    /**
     * @param EntityManager $em
     * @param Container $container
     */
    public function __construct(EntityManager $em, Container $container)
    {
        $this->em = $em;
        $this->container = $container;
    }

    /**
     * @param string $type
     * @return array
     */
    public function fetchNotificationRequest($type)
    {
        return $this->em->getRepository('BackendBundle:NotificationRequest')
                        ->getNotificationRequests([
                            'type' => $type
                        ]);
    }

    /**
     * @param $source
     * @return NotifierInterface|null
     * @throws \Exception
     */
    public function getNotifier($source)
    {
        $notifier = null;
        $source = strtolower($source);

        switch ($source) {
            case 'tb':
                $notifier = $this->container->get('transaction.notifier.tb');
                break;

            default:
                throw new \Exception('Fatal Error :: Unknown source requested!!');
        }

        return $notifier;
    }

    /**
     * @param string $type
     * @return int|mixed
     */
    public function notify($type = 'both')
    {
        $notifications = $this->fetchNotificationRequest($type);
        $count = 0;

        foreach ($notifications as $notification) {
            if ($this->getNotifier($notification->getReciever())
                        ->notifyExisting($notification)) {
                $count += 1;
            }
        }

        return $count;
    }
} 