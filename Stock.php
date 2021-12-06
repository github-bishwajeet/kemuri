<?php
require "db.php";

/*  
 * This file handles all operations related to `stocks` table.
 */
class Stock
{
    protected $conn;

    function __construct()
    {
        set_time_limit(0); // safe_mode is off
        ini_set('max_execution_time', 1000); //1000 seconds
        global $conn;
        $this->conn = $conn;
    }

    /**
     * @param array $file
     * @return object|throws
     */ 
    public function upload_csv($file)
    {
        try {
            $filename = "uploads/".time().mt_rand(1111, 9999).'.csv';
            move_uploaded_file($file["tmp_name"], $filename);
            $headings = ['date', 'stock_name', 'price'];
            $file = fopen($filename, "r");
            $csv_headings = fgetcsv($file, 10000000, ",");
            $lowercase_headings = is_array($csv_headings) ? array_map('strtolower', $csv_headings) : $csv_headings;
            if (!empty(array_diff($headings, $lowercase_headings))) {
                abort('Invalid CSV heading names, please download our sample template and use it to upload the records!');
            }
            $date_index = array_search('date', $lowercase_headings);
            $stock_name_index = array_search('stock_name', $lowercase_headings);
            $price_index = array_search('price', $lowercase_headings);
            // prepare and bind
            $stmt = $this->conn->prepare("INSERT INTO stocks (`stock_name`, `date`, `price`) VALUES ( ?, ?, ?)");
            $stmt->bind_param("ssi", $stock_name, $date, $price);
            $skip_row = [];
            while (($stock = fgetcsv($file, 10000000, ",")) !== false) {
                $stock_name = htmlspecialchars($stock[$stock_name_index]);
                $price = htmlspecialchars($stock[$price_index]);
                $date  = $this->parse_date(htmlspecialchars($stock[$date_index]));
                if($date == '1970-01-01' || empty($date)){
                    $stock['msg'] = 'Invalid date format';
                    $skip_row[] = $stock;
                    continue;
                }

                if(!is_numeric($price)){
                    $stock['msg'] = 'Price must be a numeric value';
                    $skip_row[] = $stock;
                    continue;
                }

                if(empty($stock_name)){
                    $stock['msg'] = 'Stock name is required';
                    $skip_row[] = $stock;
                    continue;
                }

                $stmt->execute();
                $mysqli_error = mysqli_error($this->conn);
                if(!empty($mysqli_error)){
                    $stock['msg'] = $mysqli_error;
                    $skip_row[] = $stock;
                }
            }
            $stmt->close();
            fclose($file);
            unlink($filename);

            // get unique stock names
            $response['message']  = 'File uploaded successfully.';
            $response['skip_row'] = $skip_row;
            echo json_encode($response);
        } catch (Exception $e) {
            abort($e->getMessage(), 500);
        }
    }


    /**
     * Parse date to different formats to get the correct result
     * @param string $date
     */ 
    private function parse_date($date)
    {
        $date_formats = ['m/d/Y', 'd/m/Y', 'Y/m/d', 'Y/d/m', 'd-m-Y', 'm-d-Y', 'Y-m-d', 'Y-d-m'];
        foreach ($date_formats as $date_format) {
            $parse = [];
            $parse = date_parse_from_format($date_format, $date);
            if (empty($parse['warnings'])) {
                $date = $parse['year'].'-'.$parse['month'].'-'.$parse['day'];
                return date('Y-m-d', strtotime($date));
            }
        }
    }

    /** 
    * Get all stocks
    * @return Object 
    */ 
    public function get_stocks(){
        $sql    = "SELECT DISTINCT `stock_name` FROM stocks limit 200";
        $stmt   = $this->conn->query($sql);
        $stocks = $stmt->fetch_all();
        $stmt->close();
        $this->conn->close();
        echo json_encode(array_column($stocks, 0));
    }


    /**
     * Calculate profit/loss based on company & date range 
     * @param array $input
     * @return object
     */ 
    public function calculate_profit_loss($input)
    {
        $error_msg = '';
        // validate the input fields
        if(empty($input['company'])){
            $error_msg  = 'Company field is required';
        }

        if(empty($input['dateFrom'])){
            $error_msg .= !empty($error_msg) ? $error_msg.', ' : '';
            $error_msg .= 'Date :: From field is required';
        }

        if(empty($input['dateTo'])){
            $error_msg .= !empty($error_msg) ? $error_msg.', ' : '';
            $error_msg .= 'Date :: To field is required';
        }

        if(!empty($error_msg)){
            return abort($error_msg);
        }

        $date_from = htmlspecialchars($input['dateFrom']);
        $date_to   = htmlspecialchars($input['dateTo']);
        $company   = htmlspecialchars($input['company']);
        $sql       = "SELECT DISTINCT `price`,`stock_name`,`date` FROM stocks WHERE `stock_name` = '$company' AND `date` BETWEEN '$date_from' AND '$date_to' ORDER BY `date`";
        $stmt      = $this->conn->prepare($sql);
        $stmt->execute();
        $result    = $stmt->get_result();
        $stocks    = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        if(empty($stocks)){
            echo json_encode(['error' => 'No records found for <b>'.$company.'</b> from <b>'.$date_from.'</b> to <b>'.$date_to.'</b>']); return;
        }
        $price     = array_column($stocks, 'price'); 
        $max_price = max($price);
        $max_price_index = array_keys($price, $max_price);
        $max_price_index = !empty($max_price_index) ? $max_price_index[0] : '';
        $records_before_max_price = array_slice($price, 0, $max_price_index);
        $sell_on   = $stocks[$max_price_index]['date'];
        $sell_price= $stocks[$max_price_index]['price'];

        if(!empty($records_before_max_price)){
            $min_price = min($records_before_max_price);
            $min_price_index = array_keys($records_before_max_price, $min_price)[0];
            $purchase_on     = $stocks[$min_price_index]['date'];
            $purchase_price  = $stocks[$min_price_index]['price'];
        }
        else{
            $purchase_on     = '';
            $purchase_price  = 0;
        }

        $response = [
            'purchase_on'   => $purchase_on,
            'purchase_price'=> $purchase_price,
            'sell_on'       => $sell_on,
            'sell_price'    => $sell_price,
        ];
        echo json_encode($response);
    }
} 