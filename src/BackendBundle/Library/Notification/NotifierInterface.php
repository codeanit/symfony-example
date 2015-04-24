<?php

namespace BackendBundle\Library\Notification;
use BackendBundle\Entity\NotificationRequest;

/**
 * Interface NotifierInterface
 * @package BackendBundle\Library\Notification
 */
interface NotifierInterface {
    /**
     * @param $action
     * @param $notiStatus
     * @param $message
     * @param $transactionCode
     * @param bool $queue
     * @return mixed
     */
    public function notify($action, $notiStatus, $message, $transactionCode, $queue = false);

    /**
     * @param NotificationRequest $notificationRequest
     * @return mixed
     */
    public function notifyExisting(NotificationRequest $notificationRequest);

    /**
     * @return string
     */
    public function getSource();
} 