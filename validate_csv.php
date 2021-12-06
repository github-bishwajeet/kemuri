<?php
require_once "Stock.php";
require_once "helpers.php";

/*
 * This file validates the CSV upload
 */
if($_SERVER['REQUEST_METHOD'] !== 'POST'){
    exit('Get request method is not allowed, accepts POST.');
}

if(isset($_FILES['csv'])){
    $file = $_FILES['csv'];
    // check file format
    if($file['type'] !== 'text/csv'){
      abort('Invalid file format, supports CSV file only.');
    }
    // check file size
    if($file['size'] > 5 * 1024 * 1024){
        abort('File size is too big, supports file size upto 5MB.');
    }
    $csv = new Stock();
    $csv->upload_csv($file);
}else{
    return abort('CSV file is required');
}