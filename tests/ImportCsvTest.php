<?php

use phpDocumentor\Reflection\Types\Boolean;
use PHPUnit\Framework\TestCase;

class ImportCsvTest extends TestCase
{
    private $base_url = 'http://kemuri.test/';

   public function testDBConnection()
   {
        $servername = "localhost";
        $username   = "root";
        $password   = "";
        $dbname     = "kemuri";
        
        // Create connection
        $conn = new mysqli($servername, $username, $password, $dbname);
        
        $this->assertEquals(
            FALSE,
            $conn->connect_error
        );    
   }

    public function testCheckTable(){
        $servername = "localhost";
        $username   = "root";
        $password   = "";
        $dbname     = "kemuri";
        
        // Create connection
        $conn = new mysqli($servername, $username, $password, $dbname);
        
        $sql = "CREATE TABLE stocks(
        `id` INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        `date` DATE NOT NULL,
        `stock_name` VARCHAR(100) NOT NULL,
        `price` FLOAT NOT NULL)";
        $stmt = $conn->query($sql);

        $this->assertTrue(
            $stmt
        );
    }

    public function testValidateAndUploadCsvFile()
    {
        $file  = ['csv' => new CURLFile('uploads/test.csv', 'text/csv')];
        $array = [
            'body'   => $file,
            'url'    => $this->base_url.'upload',
            'method' => 'post'
        ];
        $result = $this->curlHttpRequest($array);
        $this->assertIsArray(
            $result
        );
        $this->assertArrayHasKey(
            'message',
            $result
        );
        $this->assertArrayHasKey(
            'skip_row',
            $result
        );
    }

    public function testGetAllStocks(){
        $array = [
            'url'    => $this->base_url.'stocks',
            'body'   => [],
            'method' => 'get'
        ];
        $get_all_stocks = $this->curlHttpRequest($array);
        $this->assertIsArray($get_all_stocks);
    }

    public function testStockFilter(){
        $company     = ['APPL', 'GOOGL', 'TSLA', 'MCRSFT', 'HCL', 'HP'][mt_rand(0,5)];
        $date_from   = mt_rand(1990,2015).'-'.mt_rand(1,12).'-'.mt_rand(1,31);
        $date_to     = mt_rand(2015,2021).'-'.mt_rand(1,12).'-'.mt_rand(1,31);
        $array       = [
            'url'    => $this->base_url.'stock_report',
            'method' => 'post',
            'body'   => [
                'dateFrom' => $date_from,
                'dateTo'   => $date_to,
                'company'  => $company
            ]
        ];
        $response  = $this->curlHttpRequest($array);
        $this->assertIsArray($response);
    }


    /**
     * Send Api Requests
     * @param array $array - consists `method`, `body`, `url`
     * 
     */ 
    public function curlHttpRequest($array){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $array['url']);
        $array['method'] == 'post' ? curl_setopt($ch, CURLOPT_POST, true) : '';
        curl_setopt($ch, CURLOPT_POSTFIELDS, $array['body']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = json_decode(curl_exec ($ch), true);
        curl_close ($ch);
        return $result;
    }
}
?>