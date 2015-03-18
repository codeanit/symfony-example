<?php
/**
 * Created by PhpStorm.
 * User: anit
 * Date: 2/25/15
 * Time: 4:06 PM.
 */
namespace LibraryBundle\BusinessLogic\BDO;

use LibraryBundle\BusinessLogic\ThirdParty;

/**
 * Class BDO
 * @package LibraryBundle\BusinessLogics
 */
class BdoBL extends ThirdParty
{
    /**
     * Returns encrypted password
     *
     * @param string $password BDO Password
     *
     * @return string
     */
    public function getEncryptedPassword($password)
    {
        $queryString = "java -cp "
            . "/home/anit/bdo-implemented/RemittanceAPITool.jar:."
            . " EncryptPassword Password=" . $password;
        $ePass = "";

        try {
            $ePass = shell_exec($queryString);




        } catch(\Exception $e)
        {
            $ePass = "ERROR";
        }

        return $ePass;
    }

    /**
     * Return TXN Signed Data
     *
     * @param string $SignatureType
     * @param string $CLEAR_BRS_PASSWORD
     * @param string $TRANSACTION_REFERENCE_NUMBER
     * @param string $KEYSTORE_FILE
     * @param string $KEYSTORE_PASSWORD
     * @param string $KEY_NAME
     * @param string $KEY_PASSWORD
     * @param string $LANDED_AMOUNT
     * @param string $TRANSACTION_DATE
     * @param string $ACCOUNT_NUMBER
     *
     * @return string
     */
    public function getSignedData(
        $SignatureType = "TXN",
        $CLEAR_BRS_PASSWORD = "test123",
        $TRANSACTION_REFERENCE_NUMBER = "txn12345",
        $KEYSTORE_FILE = "/home/anit/Dropbox/firstglobaldata/220FGOFC1",
        $KEYSTORE_PASSWORD = "FGM#374040w",
        $KEY_NAME = "fgdc",
        $KEY_PASSWORD = "FGM#374040w",
        $LANDED_AMOUNT = "1000",
        $TRANSACTION_DATE= "2014-11-24",
        $ACCOUNT_NUMBER = "123456789"
    )
    {
        $queryString = "java -cp /home/anit/bdo-implemented/RemittanceAPITool.jar:."
            ." com.tb.security.SignedData"
            ." SignatureType=TXN"
            .",CLEAR_BRS_PASSWORD=test123"
            .",TRANSACTION_REFERENCE_NUMBER=txn12345"
            .",KEYSTORE_FILE=/home/anit/Dropbox/firstglobaldata/220FGOFC1"
            .",KEYSTORE_PASSWORD=FGM#374040w"
            .",KEY_NAME=fgdc"
            .",KEY_PASSWORD=FGM#374040w"
            .",LANDED_AMOUNT=1000"
            .",TRANSACTION_DATE=2014-11-24"
            .",ACCOUNT_NUMBER=123456789";

        return shell_exec($queryString);
    }
}
