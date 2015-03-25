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

namespace ApiBundle\Model;

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

class DB
{  

    protected $container;    

    function __construct(ContainerInterface $container) {
        
        $this->container = $container;
    }

    public function operateTransaction(array $data,$jsonData,$operation)
    {        
        if(isset($data['transaction_status']))
        {
            $status=$data['transaction_status'];
        } else{            
            $status='hold'; 
        }

        $logData = array(
                        'transaction_key'=> mt_rand(1, 9999999),
                        'transaction_source'=>isset($data['source'])?$data['source']:'',
                        'transaction_service'=>isset($data['service'])?$data['service']:'',
                        'transaction_status'=>$status,
                        'status'=>'approved',
                        'transaction_code'=>isset($data['transaction']->transaction_code)?$data['transaction']->transaction_code:'',
                        'transaction_type'=>isset($data['transaction']->transaction_type)?$data['transaction']->transaction_type:'',
                        'transaction_payment_type'=>isset($data['transaction']->payment_type)?$data['transaction']->payment_type:'',
                        'transaction_payment_type_code'=>isset($data['transaction']->payment_type_code)?$data['transaction']->payment_type_code:'',
                        'receiver_id_number'=>isset($data['transaction']->receiver_id_number)?$data['transaction']->receiver_id_number:'',
                        'receiver_id_type'=>isset($data['transaction']->receiver_id_type)?$data['transaction']->receiver_id_type:'',
                        'receiver_id_issued_country'=>isset($data['transaction']->receiver_id_issued_country)?$data['transaction']->receiver_id_issued_country:'',
                        'receiver_first_name'=>isset($data['transaction']->receiver_first_name)?$data['transaction']->receiver_first_name:'',
                        'receiver_middle_name'=>isset($data['transaction']->receiver_mother_name)?$data['transaction']->receiver_mother_name:'',
                        'receiver_last_name'=>isset($data['transaction']->receiver_last_name)?$data['transaction']->receiver_last_name:'',
                        'receiver_email'=>isset($data['transaction']->receiver_email)?$data['transaction']->receiver_email:'',
                        'receiver_account_type'=>isset($data['transaction']->receiver_account_type)?$data['transaction']->receiver_account_type:'',
                        'receiver_currency'=>isset($data['transaction']->receiver_currency)?$data['transaction']->receiver_currency:'',
                        'receiver_city'=>'',
                        'receiver_country'=>isset($data['transaction']->receiver_country)?$data['transaction']->receiver_country:'',
                        'receiver_state'=>isset($data['transaction']->receiver_state)?$data['transaction']->receiver_state:'',
                        'receiver_phone_mobile'=>isset($data['transaction']->receiver_phone_mobile)?$data['transaction']->receiver_phone_mobile:'',
                        'receiver_phone_landline'=>isset($data['transaction']->receiver_phone_landline)?$data['transaction']->receiver_phone_landline:'',
                        'receiver_postal_code'=>isset($data['transaction']->receiver_postal_code)?$data['transaction']->receiver_postal_code:'',
                        'receiver_account_number'=>isset($data['transaction']->receiver_account_number)?$data['transaction']->receiver_account_number:'',
                        'receiver_bank_routing_no'=>isset($data['transaction']->receiver_bank_routing_no)?$data['transaction']->receiver_bank_routing_no:'',
                        'receiver_bank_branch'=>isset($data['transaction']->receiver_bank_branch)?$data['transaction']->receiver_bank_branch:'',
                        'receiver_bank_name'=>isset($data['transaction']->receiver_bank_name)?$data['transaction']->receiver_bank_name:'',
                        'sender_id_number'=>isset($data['transaction']->sender_id_number)?$data['transaction']->sender_id_number:'',
                        'sender_id_type'=>isset($data['transaction']->sender_id_type)?$data['transaction']->sender_id_type:'',
                        'sender_id_issued_country'=>isset($data['transaction']->sender_id_issued_country)?$data['transaction']->sender_id_issued_country:'',
                        'sender_first_name'=>isset($data['transaction']->sender_first_name)?$data['transaction']->sender_first_name:'',
                        'sender_middle_name'=>isset($data['transaction']->sender_mother_name)?$data['transaction']->sender_mother_name:'',
                        'sender_last_name'=>isset($data['transaction']->sender_last_name)?$data['transaction']->sender_last_name:'',
                        'sender_email'=>isset($data['transaction']->sender_email)?$data['transaction']->sender_email:'',
                        'sender_account_type'=>isset($data['transaction']->sender_account_type)?$data['transaction']->sender_account_type:'',
                        'sender_currency'=>isset($data['transaction']->sender_currency)?$data['transaction']->sender_currency:'',
                        'sender_city'=>'',
                        'sender_country'=>isset($data['transaction']->sender_country)?$data['transaction']->sender_country:'',
                        'sender_state'=>isset($data['transaction']->sender_state)?$data['transaction']->sender_state:'',
                        'sender_phone_mobile'=>isset($data['transaction']->sender_phone_mobile)?$data['transaction']->sender_phone_mobile:'',
                        'sender_phone_landline'=>isset($data['transaction']->sender_phone_landline)?$data['transaction']->sender_phone_landline:'',
                        'sender_postal_code'=>isset($data['transaction']->sender_postal_code)?$data['transaction']->sender_postal_code:'',
                        'sender_account_number'=>isset($data['transaction']->sender_account_number)?$data['transaction']->sender_account_number:'',
                        'sender_bank_routing_number'=>isset($data['transaction']->sender_bank_routing_no)?$data['transaction']->sender_bank_routing_no:'',
                        'sender_bank_branch'=>isset($data['transaction']->sender_bank_branch)?$data['transaction']->sender_bank_branch:'',
                        'sender_bank_name'=>isset($data['transaction']->sender_bank_name)?$data['transaction']->sender_bank_name:'',
                        'additional_informations'=>''
                        );        
        $queueData = array(
                        'transaction_source' => isset($data['source'])?$data['source']:'',
                        'transaction_service' => isset($data['service'])?$data['service']:'',
                        'operation' => $operation, 
                        'parameter' => $jsonData,
                        'is_executed' => 0,
                        'creation_datetime' => date('Y-m-d H:i:s')
                        );       
        $conn = $this->container->get('database_connection');
        $check = 0;
        $check_queue = 0;


        try {              
                if(isset($data['confirmation_number']))
                {
                    $num = $data['confirmation_number'];
                }
                if(isset($data['transaction']->transaction_code))
                {
                    $num = $data['transaction']->transaction_code;                    
                }
                $qb = $conn->createQueryBuilder()
                               ->select('count(t.id)')
                               ->from('transactions', 't')
                               ->where('t.transaction_code = :transaction_code')
                               ->setParameter('transaction_code', $num);
                $count=$qb->execute()->fetchColumn();
                               
                if($operation=='modify') {
                    if($count>0) {
                        $check_queue = $conn->insert('operations_queue', $queueData); 
                        $check=3;                    
                        $check_queue=3;                        
                    }else {
                        $check=4;                    
                        $check_queue=4;
                    }                             

                }elseif ($operation== 'cancel') {
                    $check_queue = $conn->insert('operations_queue', $queueData); 
                    $check=4;                    
                    $check_queue=4;
                } else{

                    if ($count <= 0) {                        
                        $check= $conn->insert('transactions', $logData);            
                        $check_queue = $conn->insert('operations_queue', $queueData);            
                        $check_queue = $conn->insert('TB', $logData);
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

    public function getTransactions()
    {
        $conn = $this->container->get('database_connection');
        $data=$conn->fetchAll('SELECT * FROM transactions');
        return $data;
    }

    public function getServices()
    {
        $conn = $this->container->get('database_connection');
        $data=$conn->fetchAll('SELECT * FROM services');
        return $data;
    }

    public function getfServices()
    {
        $conn = $this->container->get('database_connection');
        $data=$conn->fetchAll('SELECT * FROM ftp_services');
        return $data;
    }

    public function getServiceCredentials($id='')
    {   
        $conn = $this->container->get('database_connection');        
        $data = $conn->fetchArray('SELECT * FROM services WHERE service_name = ?', array(strtolower($id)));
        return $data;
    }   

    


    public function saveCredentials($serviceName,$fields)
    {
        $conn = $this->container->get('database_connection');
        $cred=array();
        $result=0;
        $service=strtolower($serviceName);

        
        foreach ($fields as $key => $field) {            
            $cred[strtolower($key)]=$field;            
        }        
        try {
             $qb = $conn->createQueryBuilder()
                               ->select('count(t.id)')
                               ->from('services', 't')
                               ->where('t.service_name = :service_name')
                               ->setParameter('service_name', $service);
                $count=$qb->execute()->fetchColumn();      
            } catch ( \Exception $e) {
                  $e->getMessage();
            }  
           
        $enc=base64_encode(json_encode($cred));    
        if($count > 0 ) {
         $result =$conn->update('services',array('service_name'=>$service,'credentials'=>$enc), array('service_name' => $service));
         
        }else{
         $result = $conn->insert('services',array('service_name'=>$service,'status'=>'1','credentials'=>$enc));
        }
        
        return $result;
    }

    public function changeStatus($id,$status)
    {
        $cStatus=($status==1)?'0':'1';
        $conn = $this->container->get('database_connection');
        $result =$conn->update('services',array('status'=>$cStatus), array('id' => $id));
    }

    public function getFields($id){
        $conn = $this->container->get('database_connection');
        $fields=array();
        try {
            $data = $conn->fetchArray('SELECT id,credentials,is_ftp_service FROM services WHERE id = ?', array($id));            
            $decodedData=json_decode(base64_decode($data[1]));
            foreach ($decodedData as $key => $value) {
               array_push($fields,$key);
            }
        } catch (\Exception $e) {
            
        }      
        return array($fields,$data[0],$data[2]);
    }

    public function getFieldsById($id){
        $conn = $this->container->get('database_connection');
        $fields=array();
        try {
            $data = $conn->fetchArray('SELECT credentials FROM services WHERE id = ?', array(strtolower($id)));                     
            $decodedData=json_decode(base64_decode($data[0]));
            foreach ($decodedData as $key => $value) {
               array_push($fields,$value);
            }
        } catch (\Exception $e) {
            
        }
        return array($decodedData);
    }

    public function editService($serviceName,$fields,$id,$ftp)
    {        

        $conn = $this->container->get('database_connection');
        $cred=array();
        $result=0;
        $newFieldsKey=array();
        $service=strtolower($serviceName);
        $old=$this->getFieldsById($id);

        //get new fields key 
        foreach ($fields as $key => $value) {
            if (is_int($key)) {
                array_push($newFieldsKey,$key);
            }
        }

        foreach ($old[0] as $key => $value) {
            if(array_key_exists($key,$fields)){
                if($fields[$key] !=''){
                   $cred[$fields[$key]]=$value;
                }            
            }
        }
        foreach ($newFieldsKey as $key => $value)
                 {                    
                    $cred[$fields[$value]]='';                    
                 } 
          
        try {
             $qb = $conn->createQueryBuilder()
                               ->select('count(t.id)')
                               ->from('services', 't')
                               ->where('t.service_name = :service_name')
                               ->setParameter('service_name', $service);
                $count=$qb->execute()->fetchColumn();      
            } catch ( \Exception $e) {
                  $e->getMessage();
            }  
        $enc=base64_encode(json_encode($cred));      
       // if($count < 0 ) {
         $result =$conn->update('services',array('service_name'=>$service,'credentials'=>$enc,'is_ftp_service'=>$ftp), array('id' => $id));
        //} 
        return $result;
    }
    public function saveService($serviceName,$fields,$ftp)
    {        
        $conn = $this->container->get('database_connection');
        $cred=array();
        $result=0;
        $service=strtolower($serviceName);

        
        foreach ($fields as $key => $field) {
            $cred[$field]='';
            // $cred[$key]=$value;

        }        
        try {
             $qb = $conn->createQueryBuilder()
                               ->select('count(t.id)')
                               ->from('services', 't')
                               ->where('t.service_name = :service_name')
                               ->setParameter('service_name', $service);
                $count=$qb->execute()->fetchColumn();      
            } catch ( \Exception $e) {
                  $e->getMessage();
            }  
        $enc=base64_encode(json_encode($cred));    
        if($count > 0 ) {
         $result =$conn->update('services',array('service_name'=>$service,'credentials'=>$enc,'is_ftp_service'=>$ftp), array('service_name' => $service));
         
        }else{
         $result = $conn->insert('services',array('service_name'=>$service,'status'=>'1','credentials'=>$enc,'is_ftp_service'=>$ftp));
        }
        
        return $result;
    }

    public function checkDuplicateServiceName($name,$id){
        
        $conn = $this->container->get('database_connection');        
        $data = $conn->fetchArray('SELECT * FROM services WHERE service_name = ? and id != ?', array(strtolower($name),$id));        
        return $data;
                 
    }

    public function getUser(array $data){
        $conn = $this->container->get('database_connection');        
        $result = $conn->fetchArray('SELECT * FROM users WHERE username = ? and password = ?', $data);
        return count($result);
    }

    public function saveUploadData($name,$id,$sName){
        $conn = $this->container->get('database_connection');        
        $result = $conn->insert('ftp_services',array('service_id'=>$id,'file'=>$name,'action'=>'OUT','service_name'=>$sName));      
        return;
    }

    public function getServiceId($name){
        $conn = $this->container->get('database_connection');        
        $result = $conn->fetchArray('SELECT id FROM services WHERE service_name = ?', array(strtolower($name)));
        return $result;
    }

    public function getLogList($service_name,$service_id)
    {
        $conn = $this->container->get('database_connection');        
        $data = $conn->fetchAll('SELECT * FROM log WHERE Service = ? ORDER BY id DESC', array(ucfirst($service_id)));      
        return $data;
    }

    public function getCred($service_name)
    {
        $conn = $this->container->get('database_connection');        
        $data = $conn->fetchAll('SELECT * FROM services WHERE service_name = ? ', array(strtolower($service_name)));      
        return $data;
    }

}