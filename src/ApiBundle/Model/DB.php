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
        if(isset($data['processing_status']))
        {
            $status=$data['processing_status'];
        } else{            
            $status='hold'; 
        }
        $logData=array();
        $logData = array(
                        'source_transaction_id'=> mt_rand(1, 9999999),
                        'transaction_source'=>isset($data['source'])?$data['source']:'',
                        'transaction_service'=>isset($data['service'])?$data['service']:'',
                        'processing_status'=>$status,
                        'created_at'=>date('Y-m-d H:i:s'),        
                        );       
        foreach ($data['transaction'] as $key => $value) 
        {
            $logData[$key]=isset($value)?$value:'';                                               
        }        
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
                               
                if($operation=='update') {
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
                        // $check_queue = $conn->insert('TB', $logData);
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