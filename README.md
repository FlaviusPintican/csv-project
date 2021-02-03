## Application structure

#### app/Exceptions -> handle exceptions of the application
#### app/Http -> perform the request + validate the request via UploadFileRequest
#### app/Providers
#### app/Services generate csv statistics based on uploaded file
#### config -> config files of the app
#### public -> index.php front controller of the app
#### routes -> public and api routes(need authentication)
##### POST /csv/statistics is necessary to use for generate csv statistics

#### tests -> perform functional tests for app

#### This is a MVC project structure

#### Run commands
`` composer install`` \
`` php artisan key:generate`` \
`` php artisan serve -> run a local environment to develop application`` 

#### Goals of the project
##### Create an API to generate statistics for a give CSV file and validate the imput data(e.g. if file exists, if it's a csv file, and if file contains more than 10 records)
##### The output of this file is to display the duplicated ages and the percentage of rows that had the same age
