# kemuri
A web tool to analyse stock max profit / minimise loss

# Installation

Step 1:
run `composer install` inside project root directory


Step 2 : 
set project base_url in `test/ImportCsvTest.php`      `eg: $base_url = 'http://kemuri.test'`

Step 3 :
set database config in `db.php`


Step 4:
Run PHP test cases in project root folder.          run `vendor/bin/phpunit tests`


Step 5:
set Vue.config.publicPath in app/src/main.js `eg: Vue.config.publicPath = 'http://kemuri.test'`


Step 6:
run `install npm` inside app directory


Step 7:
run `npm run serve` inside app directory


Ready to explore the Tool
