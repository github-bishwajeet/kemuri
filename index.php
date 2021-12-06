<?php
require "helpers.php";
// require "db.php";
// $stmt = $conn->prepare("INSERT INTO stocks (`stock_name`, `date`, `price`) VALUES ( ?, ?, ?)");
// $stmt->bind_param("ssi", $stock_name, $date, $price);
// for($i = 0; $i <= 100000; $i++){
//    $stock_name = ['APPL', 'GOOGL', 'TSLA', 'MCRSFT', 'HCL', 'HP'][mt_rand(0,5)];
//    $date = mt_rand(1990,2021).'-'.mt_rand(1,12).'-'.mt_rand(1,31);
//    $price = mt_rand(10,1000);
//    $stmt->execute();
// }
// $stmt->close();
// $conn->close();

$routes = [
    '/upload'  => [
       'file'  =>  'validate_csv.php',
    ],
    '/stocks'  => [
      'file'   =>  'Stock.php',
      'method' =>  'get_stocks'
    ],
    '/stock_report' => [
      'file'   =>   'Stock.php',
      'method' =>   'calculate_profit_loss',
      'param'  =>   $_SERVER['REQUEST_METHOD'] == 'POST' ? $_POST : []
    ]
];

$request_url = $_SERVER['REQUEST_URI'];

if(!isset($routes[$request_url])) {
  http_response_code(404);
  exit('<center style="margin-top:18%"><h1>404</h1><h2>Requested page not found</h2></center>');
}

require $routes[$request_url]['file'];

if(isset($routes[$request_url]) && isset($routes[$request_url]['method'])) {
  $method  = $routes[$request_url]['method'];
  $class   = substr($routes[$request_url]['file'], 0, -4);
  $new_obj = new $class;
  if(isset($routes[$request_url]['param'])) {
    $new_obj->$method($_POST);
  }else {
    $new_obj->$method();
  }
}