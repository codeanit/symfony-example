<?php
/**
 * Created by PhpStorm.
 * User: anit
 * Date: 2/25/15
 * Time: 4:06 PM.
 */
namespace BackendBundle\Library\BDO;


/**
 * Class Bdo
 * @package LibraryBundle\BusinessLogics
 */
class Bdo
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
            . "src/BackendBundle/Library/BDO/RemittanceAPITool.jar:."
            . " src.BackendBundle.Library.BDO.EncryptPassword password=" . $password;
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
        $CLEAR_BRS_PASSWORD = "bdoRemit1!",
        $TRANSACTION_REFERENCE_NUMBER = "CAON000000300001",
        $KEYSTORE_FILE = "src/BackendBundle/Library/BDO/certificate-and-jks/220FGOFC1",
        $KEYSTORE_PASSWORD = "FGM#374040w",
        $KEY_NAME = "fgdc",
        $KEY_PASSWORD = "FGM#374040w",
        $LANDED_AMOUNT = "1000",
        $TRANSACTION_DATE= "2015-04-07",
        $ACCOUNT_NUMBER = "100661036243"
    )
    {
        $queryString = "java -cp src/BackendBundle/Library/BDO/RemittanceAPITool.jar:."
            ." src/BackendBundle/Library/BDO/SignedData"
            ." SignatureType=" . $SignatureType
            .",CLEAR_BRS_PASSWORD=" . $CLEAR_BRS_PASSWORD
            .",TRANSACTION_REFERENCE_NUMBER=" . $TRANSACTION_REFERENCE_NUMBER
            .",KEYSTORE_FILE=src/BackendBundle/Library/BDO/certificate-and-jks/220FGOFC1"
            .",KEYSTORE_PASSWORD=FGM#374040w"
            .",KEY_NAME=fgdc"
            .",KEY_PASSWORD=FGM#374040w"
            .",LANDED_AMOUNT=" . $LANDED_AMOUNT
            .",TRANSACTION_DATE=" . $TRANSACTION_DATE
            .",ACCOUNT_NUMBER=" . $ACCOUNT_NUMBER;


        return shell_exec($queryString);
    }

    public function create()
    {
        $this->getEncryptedPassword("bdoRemit1!");
        
        $this->getSignedData();
    }

}
