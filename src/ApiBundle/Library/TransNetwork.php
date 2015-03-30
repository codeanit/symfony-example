<?php
/**
 * First Global Data
 *
 * @category DEX_API
 * @package  Api\PayoutBundle\Library
 * @author   Manish Chalise <mchalise@gmail.com>
 * @license  http://firstglobalmoney.com/license description
 * @version  v1.0.0
 * @link     (remittanceController, http://firsglobaldata.com)
 */
namespace Api\PayoutBundle\Library;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

/**
 * TransNetwork TPS XML Webservice Interface for Money Transmitters
 *
 * @category DEX_API
 * @package  Api\PayoutBundle\Library
 * @author   Manish Chalise <mchalise@gmail.com>
 * @license  http://firstglobalmoney.com/license Usage License
 * @version  v1.0.0
 * @link     (remittanceController, http://firsglobaldata.com)
 */

class TransNetwork
{

    protected $container;

    protected $url;

    protected $operationMap = array(
                               'create' => 'createTransfer',
                               'modify' => '',
                               'update' => '',
                               'cancel' => '',
                              );

    /**
     * Constructor creates Service Container interface and assign WSDL address
     * 
     * @param ContainerInterface $container Service Container DI
     */

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->url       = 'https://devsit.transnetwork.com/TNCWS33/TransmitterInterface.asmx?wsdl';

    }


    /**
     * Used to map the method according to operation 
     * 
     * @param string $operation [Operation name]
     * @param array  $args      [Parameters]
     * 
     * @return void            
     */
    public function process($operation, $args)
    {
        return call_user_func_array(array($this, $this->operationMap[$operation]), [$args]);

    }

    /**
     * The CreateTransfer WebMethod allows a transmitter to create a new funds 
     * transfer order for payment by a Transnetwork associated payer
     * 
     * @param  array $txn [requied fields for create transaction]
     * 
     * @return void
     */
    public function createTransfer($txn = null)
    {
        $log = new \Symfony\Bridge\Monolog\Logger('TransNetwork');
        $log->pushHandler(new StreamHandler(__DIR__ . '/Logs/TransNetwork/createTransfer.log.txt', Logger::INFO));
        $data=$txn;
        $param=array(
            'Username'=>'samsos',
            'Password'=>'TNC1234!',
            'TransferDetails'=>array('AccountInst'=>'',
                                     'AccountNo'=>'',
                                     'AccountType'=>'',
                                     'AgentCountry'=>'',
                                     'AgentID'=>'',
                                     'AgentPostalCode'=>'',
                                     'AgentState'=>'',
                                     'BenAddress'=>'',
                                     'BenCOB'=>'',
                                     'BenCity'=>'',
                                     'BenCountry'=>'',
                                     'BenDOB'=>'',
                                     'BenFirst'=>'',
                                     'BenIDCountry'=>'',
                                     'BenIDNumber'=>'',
                                     'BenIDType'=>'',
                                     'BenMLast'=>'',
                                     'BenNationality'=>'',
                                     'BenOccupation'=>'',
                                     'BenPLast'=>'',
                                     'BenPostalCode'=>'',
                                     'BenSSN'=>'',
                                     'BenState'=>'',
                                     'BenTel'=>'',
                                     'ClaimCode'=>'',
                                     'ClientAddress'=>'',
                                     'ClientCOB'=>'',
                                     'ClientCity'=>'',
                                     'ClientCountry'=>'',
                                     'ClientDOB'=>'',
                                     'ClientFirst'=>'',
                                     'ClientGender'=>'',
                                     'ClientIDCountry'=>'',
                                     'ClientIDExpDate'=>'',
                                     'ClientIDNumber'=>'',
                                     'ClientIDState'=>'',
                                     'ClientIDType'=>'',
                                     'ClientMLast'=>'',
                                     'ClientMiddleName'=>'',
                                     'ClientNationality'=>'',
                                     'ClientOccupation'=>'',
                                     'ClientPLast'=>'',
                                     'ClientPostalCode'=>'',
                                     'ClientSOB'=>'',
                                     'ClientSSN'=>'',
                                     'ClientState'=>'',
                                     'ClientTel'=>'',
                                     'ConfCode'=>'',
                                     'CustomField1'=>'',
                                     'CustomField2'=>'',
                                     'InternalRefNumber'=>'',
                                     'Note'=>'',
                                     'OriginationAmount'=>'',
                                     'OriginationCountry'=>'',
                                     'OriginationCurrency'=>'',
                                     'OriginationState'=>'',
                                     'PayerLocationID'=>'',
                                     'PayerName'=>'',
                                     'PaymentAmount'=>'',
                                     'PaymentCountry'=>'',
                                     'PaymentCurrency'=>'',
                                     'PaymentType'=>'',
                                     'TransactionDate'=>'',)
            );
        $soap_client = new \SoapClient(
            $this->url,
            array(
                "trace" => 1,
                'exceptions' => 1,
                'cache_wsdl' => WSDL_CACHE_NONE,)
        );
        $response = $soap_client->CreateTransfer($param);
        var_dump($response);
        die;
    }
}
