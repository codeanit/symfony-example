<?php

namespace BackendBundle\Library\Notification\Notifier;

use BackendBundle\Entity\NotificationRequest;
use BackendBundle\Library\Notification\AbstractNotifier as BaseNotifier;
use GuzzleHttp\Client;
use GuzzleHttp\Message\ResponseInterface;

/**
 * Class TbNotifier
 * @package BackendBundle\Library\Notification\Notifier
 */
class TbNotifier extends BaseNotifier
{
    private $notificationUrl = 'http://172.16.1.50/fgm/Webservice/cdex.php';

    /**
     * @param NotificationRequest $notificationRequest
     * @param array $args
     * @return array
     */
    public function sendNotificationRequest(NotificationRequest $notificationRequest, array $args = [])
    {
        $payload = $notificationRequest->getPayload();
        $client = new Client();
        $options = [
            'body' => json_encode($payload),
        ];

        $request = $client->createRequest('POST', $this->$notificationUrl, $options);
        $response = $client->send($request);

        $response = json_decode($response->getBody(), true);

        return is_array($response) ? $response : [
            'status' => 'failed',
            'message' => 'Fatal Error :: Unexpected response from TB!!',
        ];
    }
}