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
use Symfony\Component\Finder\Finder;



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
                $bankBranch=$bankAccountNumber=$bankName='';
                $paymentType=1;
            }
            $dataToGenerate=array(
                       "Date of the order"=>$transaction->getRemittanceDate()->format('y/m/d'),
                       "MTCN number"=>$transaction->getTransactionCode(),
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
            $rootPath=dirname($this->container->getParameter('kernel.root_dir'));
            if (!is_dir($rootPath.'/web/generated_files/sanmartin/generated')) {               
                  mkdir($rootPath.'/web/generated_files/sanmartin/generated', 0777, true);
                }
            $path=dirname($this->container->getParameter('kernel.root_dir'))
                    .'/web/generated_files/sanmartin/generated/SM'
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
                $notiDump = [
                            'message' => 'transaction create success for sanmartin' ,
                            'status' => 'success' ,
                            'code' => '200',
                            'transaction_code' => $queue->getTransaction()->getTransactionCode(),
                            ];              
                $this->notifyTb($notiDump);
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
        try {
            $rootPath=dirname($this->container->getParameter('kernel.root_dir'));
              if (!is_dir($rootPath.'/web/generated_files/sanmartin/unparsed/')) 
              {               
                mkdir($rootPath.'/web/generated_files/sanmartin/unparsed/', 0777, true);
                mkdir($rootPath.'/web/generated_files/sanmartin/parsed/', 0777, true);
              }
            $unparsedPath= $rootPath.'/web/generated_files/sanmartin/unparsed/';
            $parsedPath= $rootPath.'/web/generated_files/sanmartin/parsed/';
            $contents=array();
            $getMTCN=array();
            $fileCount=0;        
            $finder = new Finder();
            $finder->files()->in($unparsedPath);
            foreach ($finder as $file) {
               $contents [] = $file->getRelativePathname();
               $data=file_get_contents($unparsedPath.$contents[$fileCount++]);            
               $txnData=explode("\n", str_replace("\r", '', trim($data)));             
               foreach ($txnData as $txnDatas) {
                  $MTCN= explode('|',$txnDatas);
                  $getMTCN[]=$MTCN[1];               
               } 
               if(copy($unparsedPath.$file->getRelativePathname(),$parsedPath.$file->getRelativePathname())){
                  unlink($unparsedPath.$file->getRelativePathname());
               }                             
            }            
           // print_r($getMTCN);die;
          } catch (\Exception $e) {         
              echo $e->getMessage();die;                     
          }               
        die;
        return;
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