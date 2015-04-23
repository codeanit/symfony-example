<?php

namespace BackendBundle\Library\Queue;


use BackendBundle\Entity\OperationsQueue;
use Doctrine\ORM\EntityManager;
use GuzzleHttp\Client;
use GuzzleHttp\Message\Response;
use Symfony\Bridge\Monolog\Logger;
use Symfony\Component\Validator\Constraints\DateTime;

/**
 * Class AbstractQueueWorker
 * @package BackendBundle\Library\Queue
 */
abstract class AbstractQueueWorker implements QueueWorkerInterface
{
    private $settings = [];

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected  $em;

    /**
     * @var \Symfony\Bridge\Monolog\Logger
     */
    protected $logger;

    /**
     * @param \Doctrine\ORM\EntityManager $em
     */
    public function setDoctrine(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @param Logger $logger
     */
    public function setLogger(Logger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param OperationsQueue $queue
     * @param array $args
     * @return mixed|void
     * @throws \Exception
     */
    public function processQueue(OperationsQueue $queue, $args = [])
    {
        $operation = strtolower($queue->getOperation());

        switch ($operation) {
            case 'create':
                return $this->createTransaction($queue, $args);

            case 'change':
                return $this->changeTransaction($queue, $args);

            case 'cancel':
                return $this->cancelTransaction($queue, $args);

            default:
                throw new \Exception('Fatal Error :: Undefined operation attempted!!');
        }
    }

    /**
     * @return array|mixed
     */
    public function getWorkerSetting($key = null)
    {
        if (empty($this->settings)) {
            $service = $this->em->getRepository('BackendBundle:Services')
                                ->findOneBy([
                                    'serviceName' => $this->getWorkerServiceName()
                                ]);

            if (! $service) {
                $this->settings = [];
            } else {
                $this->settings = (json_decode(base64_decode($service->getCredentials()), true)) ?
                    json_decode(base64_decode($service->getCredentials()), true): [];
                $this->settings['service_id'] = $service->getId();
            }
        }

        if ($key) {
            return (isset($this->settings[$key])) ? $this->settings[$key] : null;
        }

        return $this->settings;
    }

    public function setWorkerSetting($key, $value, $persist = false)
    {
        
    }

    private function saveWorkerSetting()
    {

    }

    /**
     * @param OperationsQueue $queue
     * @param bool $reExecute
     * @return bool
     */
    protected function updateExecutedQueue(OperationsQueue &$queue, $reExecute = false)
    {
        $queue->setIsExecutable($reExecute);
        $queue->setExecutionTimestamp(new \DateTime());
        $queue->setExecutionCount(($queue->getExecutionCount() + 1));

        $this->em->persist($queue);
        $this->em->flush();

        return true;
    }

    protected function notifyTb(array $params)
    {
        $tbUrl = 'http://172.16.1.50/fgm/Webservice/cdex.php';
        $client = new Client();
        $options = [
            'body' => json_encode($params),
        ];

        $request = $client->createRequest('POST', $tbUrl, $options);
        /**
         * @var Response
         */
        $response = $client->send($request);
        echo $response;

        $this->logger->addError('NOTI_RESPONSE_DUMP', [$response->getBody()]);
        $this->logger->addError('NOTI_RESPONSE_DUMP', $params);
    }

    /**
     * @param OperationsQueue $queue
     * @param array $args
     * @return mixed
     */
    abstract public function createTransaction(OperationsQueue $queue, $args = []);

    /**
     * @param OperationsQueue $queue
     * @param array $args
     * @return mixed
     */
    abstract public function changeTransaction(OperationsQueue $queue, $args = []);

    /**
     * @param OperationsQueue $queue
     * @param array $args
     * @return mixed
     */
    abstract public function cancelTransaction(OperationsQueue $queue, $args = []);

    /**
     * @param array $arg
     * @return mixed
     */
    abstract public function confirmTransaction();

    /**
     * @return string
     */
    abstract protected function getWorkerServiceName();
}