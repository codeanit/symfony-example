<?php
/**
 * Created by PhpStorm.
 * User: rikesh
 * Date: 4/10/15
 * Time: 2:30 PM
 */

namespace BackendBundle\Tests\QueueWorkerTest\General;


use GuzzleHttp\Client;
use Symfony\Bundle\FrameworkBundle\Tests\Functional\WebTestCase;

class TransactionRequest extends WebTestCase
{
    const TRANSACTION_URL = '127.0.0.0/work/cdex-backed/app_dev/php/transactions/v2/creates';

    /**
     * @dataProvider createHttpClient
     */
    public function testTransactionCreate(Client $httpClient)
    {
        $request = $httpClient->createRequest('POST', self::TRANSACTION_URL, [
            'headers' => [
                'content-type' => 'application/json'
            ],
            'body' => [

            ]
        ]);

        $response = $request->send();



    }

    public function createHttpClient()
    {
        $client = new Client();

        return [
            [$client]
        ];
    }
} 