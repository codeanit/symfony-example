<?php

namespace ApiBundle\Controller;

use BackendBundle\Entity\Transactions;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;

/**
 * Class PayoutV2Controller
 * @package ApiBundle\Controller
 */
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
            'processing_status'=> Transactions::TRANSACTION_STATUS_PENDING,
            'created_at'=> date('Y-m-d H:i:s'),
            'uuid' => $uuid,
            'queue_operation' => Transactions::QUEUE_OPERATION_CREATE,
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
    public function postChangesAction(Request $request)
    {
        $status = '';
        $statusCode = '';
        $message = '';
        $data = [];

        $uuid = $request->get('transaction_uuid');
        $transactionData = $request->get('transaction');

        try {
            if (! $uuid) {
                throw new \Exception('Fatal Error :: UUID not found.');
            }
            $txn = $this->findTransactionByTransactionId($uuid);

            if (! $txn) {
                throw new \Exception("Fatal Error :: Transaction with id '{$uuid}' not found!!");
            }

            if ($this->isCancelRequestRegistered($txn['id'])) {
                throw new \Exception("Fatal Error :: Transaction with id '{$uuid}' already queued for Cancellation!!");
            }

            $parentId = $txn['id'];
            unset($txn['id']);
            unset($txn['uuid']);
            $transactionData = array_merge($txn, $transactionData, [
                'source_transaction_id'=> mt_rand(1, 9999999),
                'transaction_source'=> $request->get('source'), //isset($data['source'])?$data['source']:'',
                'transaction_service'=> $request->get('service'), //isset($data['service'])?$data['service']:'',
                'processing_status'=> Transactions::TRANSACTION_STATUS_PENDING,
                'created_at'=> date('Y-m-d H:i:s'),
                'queue_operation' => Transactions::QUEUE_OPERATION_CHANGE,
                'parent_id' => $parentId,
            ]);

            if (! $this->createTransaction($transactionData)) {
                throw new \Exception('Fatal Error :: Unable to create new Transaction.');
            }

            $status = 'Ok';
            $statusCode = 200;
            $message = 'Transaction Successfully Created.';
            $data['transaction_uuid'] = $uuid;

        } catch(\Exception $e) {
            $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR;
            $status = 'Failed';
            $message = $e->getMessage();
        }

        return [
            'status' => $status,
            'status_code' => $statusCode,
            'message' => $message,
            'data' => $data,
        ];
    }

    /**
     * @param Request $request
     * @return array
     */
    public function postCancelAction(Request $request)
    {
        $status = 'Failed';
        $statusCode = 500;
        $message = 'Unable to modify the Transaction.';
        $debug = [];
        $responseData = [];

        $uuid = $request->get('transaction_uuid');
        $transactionData = $request->get('transaction');

        try {
            if (! $uuid) {
                throw new \Exception('Fatal Error :: UUID not found.');
            }
            $txn = $this->findTransactionByTransactionId($uuid);

            if (! $txn) {
                throw new \Exception("Fatal Error :: Transaction with id '{$uuid}' not found!!");
            }

            if ($this->isCancelRequestRegistered($txn['id'])) {
                throw new \Exception("Fatal Error :: Transaction with id '{$uuid}' already queued for Cancellation!!");
            }

            $parentId = $txn['id'];
            unset($txn['id']);
            unset($txn['uuid']);
            $transactionData = array_merge($txn, $transactionData, [
                'source_transaction_id'=> mt_rand(1, 9999999),
                'transaction_source'=> $request->get('source'), //isset($data['source'])?$data['source']:'',
                'transaction_service'=> $request->get('service'), //isset($data['service'])?$data['service']:'',
                'processing_status'=> Transactions::TRANSACTION_STATUS_PENDING,
                'created_at'=> date('Y-m-d H:i:s'),
                'queue_operation' => Transactions::QUEUE_OPERATION_CANCEL,
                'parent_id' => $parentId,
            ]);

            if (! $this->createTransaction($transactionData)) {
                throw new \Exception('Fatal Error :: Unable to create new Transaction for Cancellation.');
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

    /**
     * @return bool|string
     * @throws \Doctrine\DBAL\DBALException
     */
    private function generateUuid()
    {
        $connection = $this->get('doctrine.dbal.default_connection');
        $uuid = $connection->executeQuery('SELECT UUID()')
                        ->fetchColumn();

        return $uuid;
    }

    /**
     * @param $transactionUuid
     * @return mixed
     */
    private function findTransactionByTransactionId($transactionUuid)
    {
        $qb = $this->get('doctrine.dbal.default_connection')
                    ->createQueryBuilder()
                    ->select('*')
                    ->from('transactions', 't')
                    ->where('t.uuid = :txnuuid')
                    ->andWhere('t.parent_id IS NULL')
                    ->setParameter('txnuuid', $transactionUuid)
        ;

        return $qb->execute()->fetch();
    }

    /**
     * @param $transactionId
     * @return mixed
     */
    private function isCancelRequestRegistered($transactionId)
    {
        $qb = $this->get('doctrine.dbal.default_connection')
            ->createQueryBuilder()
            ->select('count(id)')
            ->from('transactions', 't')
            ->where('t.parent_id = :txnId')
            ->andWhere('t.queue_operation = :statusCancelled')
            ->setParameter('statusCancelled', Transactions::QUEUE_OPERATION_CANCEL)
            ->setParameter('txnId', $transactionId)
        ;

        return $qb->execute()->fetchColumn();
    }
}
