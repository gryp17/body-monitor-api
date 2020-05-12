# body-monitor-api
An API for the [body monitor](https://github.com/gryp17/body-monitor) web application

## Installation

1. Import the database schema

  > [/db/innodb_schema.sql](https://github.com/gryp17/body-monitor-api/blob/master/db/innodb_schema.sql)
  
## Configuration

1. API

  The configuration file is located in

  > [/api/config/Config.php](https://github.com/gryp17/body-monitor-api/blob/master/api/config/Config.php)


  It contains the default database credentials

2. .htaccess

  Change the RewriteBase rule based on your domain path.
  
  The .htaccess file is located in the root directory of the project
  
  > [/.htaccess](https://github.com/gryp17/body-monitor-api/blob/master/.htaccess)
  
  Examples:

  ```apache
  #http://body-monitor.com
  RewriteBase /
  ```
  
  ```apache
  #localhost/body-monitor
  RewriteBase /body-monitor
  ```
