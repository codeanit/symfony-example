<?php
/**
 * Created by PhpStorm.
 * User: anit
 * Date: 4/16/15
 * Time: 3:58 PM
 */

namespace BackendBundle\Library\Queue\Worker;

use BackendBundle\Entity\OperationsQueue;
use BackendBundle\Entity\Transactions;
use BackendBundle\Library\Queue\AbstractQueueWorker as BaseWorker;
use JMS\Serializer\Serializer;

class SanMartinWorker extends BaseWorker {


    /**
     * @param OperationsQueue $queue
     * @param array $args
     * @return mixed
     */
    public function createTransaction(OperationsQueue $queue, $args = [])
    {

//        Data to be mapped
//                    "Date of the order"=>"12/12/12",
//                    "Number of Shipping"=>"2",
//                    "Sender Name"=>"Manish",
//                    "Sender Name Paternal"=>"Chalise",
//                    "Sender Name Mother"=>"",
//                    "Beneficiary Name"=>"Anit",
//                    "Recipient Name Paterno"=>"Shrestha",
//                    "Recipient Name Mother"=>"manandhar",
//                    "Currency Shipping"=>"USD",
//                    "Currency of Payment"=>"USD",
//                    "Exchange rate"=>"1.5",
//                    "Key Branch payment"=>"",
//                    "Shipping Amount"=>"1000",
//                    "Reference"=>"",
//                    "Country code source"=>"USA",
//                    "Tel Sender"=>"123123",
//                    "Recipient Street"=>"ktm-12 avinue road",
//                    "Recipient City"=>"kathmandu",
//                    "beneficiary State"=>"bagmati",
//                    "Recipient Zip Code"=>"97701",
//                    "Recipient Phone"=>"4894894",
//                    "Payment Type"=>"02",
//                    "Account No. Feed"=>"12313",
//                    "Bank"=>"global bank",
//                    "Bank Branch"=>"chabahil",
//                    "Message or Comment"=>"hello world",
//                    "City / Town Sender"=>"Dolkha",
//                    "Sender State"=>"Baglung"


        $output='';
        if($p==null){
            $path= $this->container->get('request')->server->get('DOCUMENT_ROOT').'/generated_files/'.'Sanmartin.txt';
        }
        else{
            $path= $p.'Sanmartin.txt';
        }

        foreach ($data as $key => $value) {
            $output .= $value.'|';
        }
        file_put_contents($path,$output.PHP_EOL,FILE_APPEND | LOCK_EX);
    }

    /**
     * @param OperationsQueue $queue
     * @param array $args
     * @return mixed
     */
    public function changeTransaction(OperationsQueue $queue, $args = [])
    {
        // TODO: Implement changeTransaction() method.
    }

    /**
     * @param OperationsQueue $queue
     * @param array $args
     * @return mixed
     */
    public function cancelTransaction(OperationsQueue $queue, $args = [])
    {
        // TODO: Implement cancelTransaction() method.
    }

    /**
     * @param array $arg
     * @return mixed
     */
    public function confirmTransaction()
    {
        // TODO: Implement confirmTransaction() method.
    }

    /**
     * @return string
     */
    protected function getWorkerServiceName()
    {
        // TODO: Implement getWorkerServiceName() method.
    }

    /**
     * @param Transactions $transaction
     * @param array $args
     * @return mixed
     */
    public function enqueueTransaction(Transactions $transaction, $args = [])
    {
        // TODO: Implement enqueueTransaction() method.
    }
}