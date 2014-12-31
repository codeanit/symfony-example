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

class TBConnectionModel 
{

    public $url;

    protected $container;

    function __construct($container) {
        $this->container = $container;
    }

    /**
     * Send Data to TB 
     * 
     * @param array 
     *        
     * @return  array
     */
    public function curlTransborder(array $postedData)
    { 
         $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, "http://firstglobalmoney.com.local/secure/dexdbal");
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $postedData);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $resultPOST = curl_exec($curl);       

        return (array) json_decode($resultPOST);    
    }

    public function addLog(array $data)
    {        
        $logData=array(
                                'transaction_key'=>$data['transaction']->transaction_key,
                                'transaction_source'=>$data['source'],
                                'transaction_service'=>$data['service'],
                                'transaction_status'=>'pending',
                                'transaction_code'=>$data['transaction']->transaction_code,
                                'transaction_type'=>$data['transaction']->transaction_type,
                                'payment_type'=>$data['transaction']->payment_type,
                                'receiver_id_number'=>$data['transaction']->receiver_id_number,
                                'receiver_id_type'=>$data['transaction']->receiver_id_type,
                                'receiver_id_issued_country'=>$data['transaction']->receiver_id_issued_country,
                                'receiver_first_name'=>$data['transaction']->receiver_first_name,
                                'receiver_middle_name'=>$data['transaction']->receiver_middle_name,
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
                                'sender_middle_name'=>$data['transaction']->sender_middle_name,
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
                                'additional_informations'=>' '
                                );


        $conn = $this->container->get('database_connection');
        
        try {
            $check= $conn->insert('cdex_log', $logData);            
        } catch (\Exception $e) {
            $check=0;
        }
        return $check;
    }     

}