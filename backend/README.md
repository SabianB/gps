# Empiric
Empriric is a php micro framework to develop api as fast as possible.

# Dependencies
PHP >= 7.4

Composer >= 1.10.10

Note: The framework might work or not in lower version of composer

# Prupose
The main prupose of this framework is to create a friendly eviroment to make api development as fast as possible without spending to much time of the project in generic api calls.

# Methodology
The mthodology that use this framework is REST API based but not REST API, it tries to combine the benefits that GraphQL give such as one unic endpoint instead the url descrtiped model used by REST API.

# Discord Server
https://discord.gg/RtKMPPN

# Getting Started
This mini guide is going to help you to get started using Empiric framework at the end of this point you will be able to consume resources via HTTP/HTTPS

1) Clone git repo in your computer using the following command
   ```console
   git clone https://github.com/InnovusLATAM/Empiric.git
   ```
2) Once you have the project in local the next step is to change some preferences in the `config.php` file located in the root of the project, this file have configurations like:
<ul>
    <li>Company name</li>
    <li>DataBase access preferences</li>
    <li>SMTP preferences</li>
    <li>Log path</li>
    <li>JWT secret key for auth process</li>
</ul>
and more
<br>
The first thing you have to do here is to change the log path in the `log_settings` array, the value of the path key must be an absolute route in the sysetm wich has to be accesible by apache or nginx user in case you use linux
<br>
Example:
<br>
Windows: C:\xampp\htdocs\empiric-framework\webapp.log
<br>
Linux: /var/www/html/webapp.log
<br>
<br>
3) When you have finished editting the `config.php` file you will must to install the dependencies specified in composer.json file, to perform this use the next command `composer install`
<br>
<br>
4) Now open your favorite browser and load the file .../api/api.php you must to see a json as an output if an error occur, this could be because the FrameWork can't access to the log file due to permissions
<br>
<br>
5) If everthing seems to be right at this point you should be able to consume the "utils" resouce from the server, you can use a rest client to test it.
<br>
The target file you must to use is .../api/api.php, for example http://localhost/empiric-framework/api/api.php send via POST the next JSON
<br>

~~~~json
{
	"endpoint":"utils",
	"action":"getDate"
}
~~~~
The response should look like
~~~~json
{
  "status": true,
  "utils": {
    "getDate": "13/09/2020 13:56:05"
  }
}
~~~~
<br>
if an error occurs the value status of the response json will be false and another value called message will appear trying to describe the error.
<br>
More coming soon...