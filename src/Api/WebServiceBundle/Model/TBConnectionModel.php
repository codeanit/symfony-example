<?php

/**
 * First Global Data.
 *
 * @category DEX_API
 *
 * @author   Anit Shrestha Manandhar <ashrestha@firstglobalmoney.com>
 * @license  http://firstglobalmoney.com/license description
 *
 * @version  v1.0.0
 *
 * @link     (remittanceController, http://firsglobaldata.com)
 */
namespace Api\WebServiceBundle\Model;

/**
 * Bridge to call TB DBAL.
 *
 * @category DEX_API
 *
 * @author   Anit Shrestha Manandhar <ashrestha@firstglobalmoney.com>
 * @license  http://firstglobalmoney.com/license Usage License
 *
 * @version  v1.0.0
 *
 * @link     (remittanceController, http://firsglobaldata.com)
 */
class TBConnectionModel
{
    /**
     * Push the received request to the DBAL.
     *
     * @param array $postedData
     *                          array('model' => 'Transaction',
     *                          'operation' => 'transactionTest',
     *                          'sessionID' => '$sessionID',
     *                          'username' => '$username',
     *                          'password' => '$password',
     *                          'refNo' => '$refno',
     *                          'signature'=> '$signature'
     *                          );
     *
     * @return String
     */
    public function curlTransborder(array $postedData)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, "http://fgmtest.firstglobalmoney.com/secure/dexdbal");
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $postedData);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $resultPOST = curl_exec($curl);

        return $resultPOST;
    }
}
