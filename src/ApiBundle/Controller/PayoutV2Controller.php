<?php

namespace ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;

class PayoutV2Controller extends Controller
{
    /**
     * @param Request $request
     * @return array
     */
    public function postCreateAction(Request $request)
    {
        $transactionData = $request->get('transaction');
        $status = 'Failed';
        $message = 'Unable to create the Transaction.';
        $statusCode = 500;
        $responseData = [];

        $uuid = $this->generateUuid();
        $transactionData = array_merge([
            'source_transaction_id'=> mt_rand(1, 9999999),
            'transaction_source'=> $request->get('source'), //isset($data['source'])?$data['source']:'',
            'transaction_service'=> $request->get('service'), //isset($data['service'])?$data['service']:'',
            'processing_status'=> $request->get('processing_status', 'hold'),
            'created_at'=> date('Y-m-d H:i:s'),
            'uuid' => $uuid,
        ], $transactionData);

        $logger = $this->get('logger');

        $logger->addError('LOG_POST_DATA', $transactionData);

        try {
            if (! $this->createTransaction($transactionData)) {
                throw new \Exception('Fatal Error :: Unable to create new Transaction.');
            }

            $status = 'Ok';
            $statusCode = 200;
            $message = 'Transaction Successfully Created.';
            $responseData['transaction_uuid'] = $uuid;

        } catch(\Exception $e) {
            $logger->addError('ACTUAL_EXCEPTION', [$e->getMessage(), $e->getFile(), $e->getLine()]);
            // @TODO - Log the transaction
        }

        return [
            'status' => $status,
            'code' => $statusCode,
            'data' => $responseData,
            'message' => $message,
        ];
    }

    /**
     * @param Request $request
     * @return array
     */
    public function postModifyAction(Request $request)
    {
        $status = 'Failed';
        $statusCode = 500;
        $message = 'Unable to modify the Transaction.';
        $debug = [];
        $postData = [];
        $responseData = [];

        try {


            $postData = array_merge([
                'source_transaction_id'=> mt_rand(1, 9999999),
                'transaction_source'=> $request->get('source'), //isset($data['source'])?$data['source']:'',
                'transaction_service'=> $request->get('service'), //isset($data['service'])?$data['service']:'',
                'processing_status'=> $request->get('processing_status', 'hold'),
                'created_at'=> date('Y-m-d H:i:s'),

            ], $postData);
//            $postData['action'] = 'modify';

            if (! $this->createTransaction($postData)) {
                throw new \Exception('Fatal Error :: Unable to create new Transaction.');
            }

            $status = 'Ok';
            $statusCode = 200;
            $message = 'Transaction successfully modified.';

        } catch(\Exception $e) {
            $debug[] = $e->getTraceAsString();
        }

        return [
            'status' => $status,
            'code' => $statusCode,
            'message' => $message,
            'data' => $responseData,
            'debug' => $debug,
        ];
    }

    /**
     * @param array $transactionData
     * @return int
     */
    protected function createTransaction(array $transactionData)
    {
        $connection = $this->get('doctrine.dbal.default_connection');
        $tableName = 'transactions';

        return $connection->insert($tableName, $transactionData);
    }

    private function generateUuid()
    {
        $connection = $this->get('doctrine.dbal.default_connection');
        $uuid = $connection->executeQuery('SELECT UUID()')
                        ->fetchColumn();

        return $uuid;
    }
}
