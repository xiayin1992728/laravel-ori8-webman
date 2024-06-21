1. 在 webman 中的 database.php 中加入下面配置
```angular2html
'oracle' => [
'driver'         => 'oracle',
'tns'            => env('DB_TNS', ''),
'host'           => env('DB_HOST', ''),
'port'           => env('DB_PORT', '1521'),
'database'       => env('DB_DATABASE', ''),
'service_name'   => env('DB_SERVICE_NAME', ''),
'username'       => env('DB_USERNAME', ''),
'password'       => env('DB_PASSWORD', ''),
'charset'        => env('DB_CHARSET', 'AL32UTF8'),
'prefix'         => env('DB_PREFIX', ''),
'prefix_schema'  => env('DB_SCHEMA_PREFIX', ''),
'edition'        => env('DB_EDITION', 'ora$base'),
'server_version' => env('DB_SERVER_VERSION', '11g'),
'load_balance'   => env('DB_LOAD_BALANCE', 'yes'),
'dynamic'        => [],
],
```
2. 在 config/bootstrap.php 中加入下面代码
```angular2html
Yajra\Oci8\Oci8ServiceProviderWebMan::class
```
