<?php
namespace LibraryBundle\Tests;

use LibraryBundle\BusinessLogic\BDO\BdoBL;

class BDOBLTest extends \PHPUnit_Framework_TestCase
{
    private $businessLogic;

    public function __construct()
    {
        $this->businessLogic = new BdoBL();
    }

    /**
     * [Test Case for PAID status in GetRemittance Method]
     */
    public function  testGetEncryptedPassword()
    {
        echo $this->businessLogic->getEncryptedPassword("test");

    }
}
?>