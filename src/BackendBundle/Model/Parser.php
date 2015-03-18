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

class Parser
{
    protected function createReaderForFile($fileName,$readDataOnly = true)
    {
        // Most common case
        $reader = new \PHPExcel_Reader_Excel5();

        $reader->setReadDataOnly($readDataOnly);

        if ($reader->canRead($fileName)) return $reader;

        // Make sure have zip archive
        if (class_exists('ZipArchive')) 
        {
            $reader = new \PHPExcel_Reader_Excel2007();

            $reader->setReadDataOnly($readDataOnly);

            if ($reader->canRead($fileName)) return $reader;
        }

        // Note that csv does not actually check for a csv file
        $reader = new \PHPExcel_Reader_CSV();

        if ($reader->canRead($fileName)) return $reader;

        throw new Exception("No Reader found for $fileName");

    }
    public function load($fileName, $readDataOnly = true)
    {
        $reader = $this->createReaderForFile($fileName,$readDataOnly);        
        return $reader->load($fileName);
    }
}
