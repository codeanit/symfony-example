<?php 

/**
 * First Global Data
 *
 * @category DEX_API
 * @package  Api\WebServiceBundle\Tests\Controller
 * @author   Manish Chalise
 * @license  http://firstglobalmoney.com/license description
 * @version  v1.0.0
 * @link     (remittanceController, http://firsglobaldata.com)
 */

namespace Api\PayoutBundle\Model;

use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Bridge to call TB DBAL
 *
 * @category DEX_API
 * @package  Api\WebServiceBundle\Tests\Controller
 * @author   Manish Chalise
 * @license  http://firstglobalmoney.com/license Usage License
 * @version  v1.0.0
 * @link     (remittanceController, http://firsglobaldata.com)
 */

class DatabaseOperationModel
{  

    protected $container;    

    function __construct(ContainerInterface $container) {
        $this->container = $container;
    }

    public function operateTransaction(array $data,$jsonData,$operation)
    {    
        $status='hold';      
        if($operation =='cancel'){
        $status='cancelled';
        }
        $logData = array(
                        'transaction_key'=>$data['transaction']->transaction_key,
                        'transaction_source'=>$data['source'],
                        'transaction_service'=>$data['service'],
                        'transaction_status'=>$status,
                        'transaction_code'=>$data['transaction']->transaction_code,
                        'transaction_type'=>$data['transaction']->transaction_type,
                        'transaction_payment_type'=>$data['transaction']->payment_type,
                        'transaction_payment_type_code'=>$data['transaction']->payment_type_code,
                        'receiver_id_number'=>$data['transaction']->receiver_id_number,
                        'receiver_id_type'=>$data['transaction']->receiver_id_type,
                        'receiver_id_issued_country'=>$data['transaction']->receiver_id_issued_country,
                        'receiver_first_name'=>$data['transaction']->receiver_first_name,
                        'receiver_middle_name'=>$data['transaction']->receiver_mother_name,
                        'receiver_last_name'=>$data['transaction']->receiver_last_name,
                        'receiver_email'=>$data['transaction']->receiver_email,
                        'receiver_account_type'=>$data['transaction']->receiver_account_type,
                        'receiver_currency'=>$data['transaction']->receiver_currency,
                        'receiver_city'=>'',
                        'receiver_country'=>$data['transaction']->receiver_country,
                        'receiver_state'=>$data['transaction']->receiver_state,
                        'receiver_phone_mobile'=>$data['transaction']->receiver_phone_mobile,
                        'receiver_phone_landline'=>$data['transaction']->receiver_phone_landline,
                        'receiver_postal_code'=>$data['transaction']->receiver_postal_code,
                        'receiver_account_number'=>$data['transaction']->receiver_account_number,
                        'receiver_bank_routing_no'=>$data['transaction']->receiver_bank_routing_no,
                        'receiver_bank_branch'=>$data['transaction']->receiver_bank_branch,
                        'receiver_bank_name'=>$data['transaction']->receiver_bank_name,
                        'sender_id_number'=>$data['transaction']->sender_id_number,
                        'sender_id_type'=>$data['transaction']->sender_id_type,
                        'sender_id_issued_country'=>$data['transaction']->sender_id_issued_country,
                        'sender_first_name'=>$data['transaction']->sender_first_name,
                        'sender_middle_name'=>$data['transaction']->sender_mother_name,
                        'sender_last_name'=>$data['transaction']->sender_last_name,
                        'sender_email'=>$data['transaction']->sender_email,
                        'sender_account_type'=>$data['transaction']->sender_account_type,
                        'sender_currency'=>$data['transaction']->sender_currency,
                        'sender_city'=>'',
                        'sender_country'=>$data['transaction']->sender_country,
                        'sender_state'=>$data['transaction']->sender_state,
                        'sender_phone_mobile'=>$data['transaction']->sender_phone_mobile,
                        'sender_phone_landline'=>$data['transaction']->sender_phone_landline,
                        'sender_postal_code'=>$data['transaction']->sender_postal_code,
                        'sender_account_number'=>$data['transaction']->sender_account_number,
                        'sender_bank_routing_number'=>$data['transaction']->sender_bank_routing_no,
                        'sender_bank_branch'=>$data['transaction']->sender_bank_branch,
                        'sender_bank_name'=>$data['transaction']->sender_bank_name,
                        'additional_informations'=>''
                        );        
        $queueData = array(
                        'transaction_source' => $data['source'],
                        'transaction_service' => $data['service'],
                        'operation' => $operation, 
                        'parameter' => $jsonData,
                        'is_executed' => 0,
                        'creation_datetime' => date('Y-m-d H:i:s')
                        );
        $conn = $this->container->get('database_connection');
        $check = 0;
        $check_queue = 0;

        try {
                if($operation=='modify') {
                    $check=$conn->update('transactions',$logData, array('transaction_key' => $data['transaction']->transaction_key));                    
                    $check=3;                    
                    $check_queue=3;

                } elseif($operation=='cancel'){
                    $check=$conn->update('transactions',$logData, array('transaction_key' => $data['transaction']->transaction_key));
                    $check=4;                    
                    $check_queue=4;
                }else{
                    $qb = $conn->createQueryBuilder()
                               ->select('count(t.id)')
                               ->from('transactions', 't')
                               ->where('t.transaction_key = :transaction_key')
                               ->setParameter('transaction_key', $data['transaction']->transaction_key);
                   
                    if ($qb->execute()->fetchColumn() <= 0) {
                        $check= $conn->insert('transactions', $logData);            
                        $check_queue = $conn->insert('operations_queue', $queueData);            
                        
                    }else{
                        $check=2;
                        $check_queue=2;
                    } 
                } 

            } catch (\Exception $e) {
            echo $e->getMessage();
            }
            if($data['source']!=''){
                $source=$data['source'];
            }else{
                $source='N/A';                
            }


            if(strtolower($data['source']) != 'tb')
            {                
                return array($check,$check_queue,$source,$data['transaction']->transaction_code);
            }            
            else{
                return array($check,$check_queue,$source);
            }
    }

}