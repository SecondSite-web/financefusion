# dash
A PHP powered custom web app cms system

## Dependencies
1.  NPM Package Manager - Used for managing and importing external packages and for running Gulp tasks for automation. NPM packages are not included in the production version. https://nodejs.org/en/download/
2.  Composer - PHP lib manager https://getcomposer.org/download/
## Folder Structure
1.  vendor - php libs
2.  node_modules - external packages, exclude in production
3.  tools - another Composer folder for dev only. Contains Php Cs Fixer https://github.com/FriendsOfPHP/PHP-CS-Fixer, PHPStan https://github.com/phpstan/phpstan, PHP Codesniffer https://github.com/squizlabs/PHP_CodeSniffer, Php Mess Detector https://phpmd.org/. All recommended plugins for dev in php. Recommended for use with Jetbrains PHPStorm and Visual Studio Code
## Setup Dev Environment
1.  Install/Update NPM and Composer
2.  Setup local server and SQL - eg, Xampp https://www.apachefriends.org/index.html, 
    https://xdebug.org/, *This guide will assist with X-Debug setup. https://gist.github.com/odan/1abe76d373a9cbb15bed
3.  Run 'npm install' and 'composer require' in the root project folder. 
4.  Navigate to the tools folder and run 'composer require' there too to install the dev tools. Dev tools not for use in production
## Database
Mysql PDO for greater compatibility
## Gulp Taskrunner
Gulp automates:
1.  'gulp jsCompile'  JS concatenation and minification and headers
2.  'gulp jsSingle'   Minifies single js files from ./src/js-single to ./js
3.  'gulp vendor'     Copies libs from './node_modules' to './vendor' eg. Bootstrap, Jquery, fontawesome
4.  'gulp emails'     Converts simple html templates in bootstrap format into email templates
5.  'gulp images'     Compresses and moves images from './src/img' to './img'
6.  'gulp zipFile'    Adds the project folder into a zip file for convenient exporting
7.  'gulp watch'      Watches all files in './src' and auto-compiles the files
8.  'gulp build'      Performs all compilation tasks
9.  'gulp dist'       Compiles the webroot files without the dev files in './dist'  
Get to know the 'gulpfile.js' - it's awesome