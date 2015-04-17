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
use Symfony\Component\DependencyInjection\ContainerInterface;


class SanMartinWorker extends BaseWorker {

    protected $container;

    /**
     * [__construct description]
     */
    function __construct(ContainerInterface $container) {
        $this->container = $container;
    }
    /**
     * @param OperationsQueue $queue
     * @param array $args
     * @return mixed
     */
    public function createTransaction(OperationsQueue $queue, $args = [])
    {
        try {
            $transaction = $queue->getTransaction();     
                if (strtolower($transaction->getTransactionType())=='bank') {
                    $paymentType=2;            
                    $bankBranch=$transaction->getBeneficiaryBankBranch();
                    $bankAccountNumber=$transaction->getBeneficiaryAccountNumber();
                    $bankName=$transaction->getBeneficiaryBankName();
                }else{
                    $bankBranch=$bankAccountNumber='';
                    $paymentType=1;
                }
                $dataToGenerate=array(
                           "Date of the order"=>$transaction->getRemittanceDate()->format('y/m/d'),
                           "Number of Shipping"=>$transaction->getTransactionCode(),
                           "Sender Name"=>$transaction->getRemitterfirstName(),
                           "Sender Name Paternal"=>$transaction->getRemitterLastName(),
                           "Sender Name Mother"=>$transaction->getRemitterMiddleName(),
                           "Beneficiary Name"=>$transaction->getBeneficiaryFirstName(),
                           "Recipient Name Paterno"=>$transaction->getBeneficiaryLastName(),
                           "Recipient Name Mother"=>$transaction->getBeneficiaryMiddleName(),
                           "Currency Shipping"=>$transaction->getRemittingCurrency(),
                           "Currency of Payment"=>$transaction->getPayoutCurrency(),
                           "Exchange rate"=>$transaction->getExchangeRate(),
                           "Key Branch payment"=>"",
                           "Shipping Amount"=>$transaction->getRemittingAmount(),
                           "Reference"=>"",
                           "Country code source"=>$transaction->getRemitterCountry(),
                           "Tel Sender"=>$transaction->getRemitterPhoneMobile(),
                           "Recipient Street"=>$transaction->getBeneficiaryAddress(),
                           "Recipient City"=>$transaction->getBeneficiaryCity(),
                           "beneficiary State"=>$transaction->getBeneficiaryState(),
                           "Recipient Zip Code"=>$transaction->getBeneficiaryPostalCode(),
                           "Recipient Phone"=>$transaction->getBeneficiaryPhoneMobile(),
                           "Payment Type"=>$paymentType,
                           "Account No. Feed"=>$bankAccountNumber,
                           "Bank"=>$bankName,
                           "Bank Branch"=>$bankBranch,
                           "Message or Comment"=>"message",
                           "City / Town Sender"=>$transaction->getRemitterCity(),
                           "Sender State"=>$transaction->getRemitterState()
                    );
                $output='';       
                $path=dirname($this->container->getParameter('kernel.root_dir'))
                        .'/web/generated_files/sanmartin/SM'
                        .date('ymd').$transaction->getTransactionCode()
                        .'.txt';
        
                foreach ($dataToGenerate as $value) {
                    $output .= $value.'|';
                }     
                file_put_contents($path,$output.PHP_EOL,FILE_APPEND | LOCK_EX);
                $check=file_exists($path);
                if($check==1){
                    $this->em->getRepository('BackendBundle:Log')
                         ->addLog(
                            $this->getWorkerSetting('service_id'),
                            'Create',
                            json_encode($dataToGenerate),
                            $output,
                            'SUCCESS'
                        );
                    $this->updateExecutedQueue($queue);

                }else{
                    $this->em->getRepository('BackendBundle:Log')
                         ->addLog(
                            $this->getWorkerSetting('service_id'),
                            'Create',
                            json_encode($dataToGenerate),
                            'File Generation Error',
                            "ERROR"
                        );
                }
        } catch (\Exception $e) {
            $this->logger->error('SANMARTIN_CREATE', [$e->getMessage()]);
        }
        return $check;
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
        return 'sanmartin';       
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