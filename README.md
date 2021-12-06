# kemuri
A web tool to analyse stock max profit / minimise loss

# Installation

Step 1:
install composer // run `composer install`


Step 2 : 
set project base_url in test/ImportCsvTest.php      #eg: $base_url = 'http://kemuri.test'


Step 3:
Run PHP test cases in project root folder.          # eg: using composer, run `vendor/bin/phpunit tests`


Step 4:
set Vue.config.publicPath in app/src/main.js `eg: Vue.config.publicPath = 'http://kemuri.test'`


Step 5:
run `install npm` inside app directory


Step 6:
run `npm run serve`inside app directory
