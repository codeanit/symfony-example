<?php

namespace BackendBundle\Library\Notification;


use Doctrine\ORM\EntityManager;
use BackendBundle\Entity\NotificationRequest;

/**
 * Class AbstractNotifier
 * @package BackendBundle\Library\Notification
 */
abstract class AbstractNotifier
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @param \Doctrine\ORM\EntityManager $em
     */
    public function setDoctrine(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @param $action
     * @param $notiStatus
     * @param $message
     * @param $transaction
     * @param bool $queue
     * @return bool
     * @throws \Exception
     */
    public function notify($action, $notiStatus, $message, $transaction, $queue = false)
    {
        $payload = [
            'status' => $notiStatus,
            'message' => $message,
        ];
        $notifyRequest = new NotificationRequest();

        $notifyRequest->setTransaction($transaction);
        $notifyRequest->setNotificationAction($action);
        $notifyRequest->setPayload($payload);

        try {
            if (! $queue) {
                $responseDump = $this->sendNotificationRequest($notifyRequest);
                $responseStatus = (isset($responseDump['status']) and strtolower($responseDump['status']) == NotificationRequest::STATUS_SUCCESS)
                    ? NotificationRequest::STATUS_SUCCESS : NotificationRequest::STATUS_FAILED;

                $notifyRequest->setNotificationStatus($responseStatus);
                $notifyRequest->setLastResponse($responseDump);
            } else {
                $notifyRequest->setNotificationStatus(NotificationRequest::STATUS_QUEUED);
            }

            $this->em->persist($notifyRequest);
            $this->em->flush();

            return true;
        } catch(\Exception $e) {
//            throw $e;
            //@todo log error
        }

        return false;
    }

    /**
     * @param NotificationRequest $notificationRequest
     * @param array $args
     * @return array
     */
    abstract public function sendNotificationRequest(NotificationRequest $notificationRequest, array $args = []);
}