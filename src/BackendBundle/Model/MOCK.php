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

class MOCK
{  

    protected $container;    

    function __construct(ContainerInterface $container) {
        $this->container = $container;
    }

   public function create(array $data)
   {
        $conn = $this->container->get('database_connection');
        $check=$conn->update('transactions',array('transaction_status'=>'complete'), array('transaction_code' => $data['transaction']->transaction_code));
       if($check==1){

            return array('code'=>'200',
                         'message'=>'Transaction Successfull',
                         'notify_source'=>$data['source'],
                         'confirmation_number'=>$data['transaction']->transaction_code,
                         'status'=>'complete'
                        );

        }else{
            return array('code'=>'400',
                         'message'=>'Cannot Modify Transaction',
                         'confirmation_number'=>$data['transaction']->transaction_code,
                         'notify_source'=>$data['source'],
                         'status' => 'failed'
                        );
        }    
   }

   public function modify(array $data)
   {
    if(isset($data['transaction']->transaction_status))
        {
            $status=$data['transaction']->transaction_status;
        } else{            
            $status='processing'; 
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
        
        $conn = $this->container->get('database_connection');
        $check = 0;
        $check_queue = 0;
        
        try {                     
             $check=$conn->update('TB',$logData, array('transaction_code' => $data['transaction']->transaction_code));
             // $checkT=$conn->update('transactions',$logData, array('transaction_key' => $data['transaction']->transaction_key));
            } catch (\Exception $e) {
             $e->getMessage();
            }
     
        if($check==1){
            return array('code'=>'200',
                         'message'=>'Successfully Updated Transaction',
                         'notify_source'=>$data['source'],
                         'confirmation_number'=>$data['transaction']->transaction_code,
                         'status'=>'complete',
                         'data' => $logData
                        );

        }else{
            return array('code'=>'400',
                         'message'=>'Cannot Modify Transaction',
                         'confirmation_number'=>$data['transaction']->transaction_code,
                         'notify_source'=>$data['source'], 
                         'status' => 'failed'                                      
                        );
        }    
   }
   
}