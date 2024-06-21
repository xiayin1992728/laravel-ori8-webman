<?php

namespace Yajra\Oci8;

use Illuminate\Database\Connection;
use Illuminate\Pagination\Cursor;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use support\Container;
use support\Db;
use Webman\Bootstrap;
use Workerman\Timer;
use Workerman\Worker;
use Yajra\Oci8\Connectors\OracleConnector as Connector;

class Oci8ServiceProviderWebMan implements Bootstrap
{
    public static function start(?Worker $worker)
    {
        Connection::resolverFor('oracle', function ($connection, $database, $prefix, $config) {
            if (isset($config['dynamic']) && ! empty($config['dynamic'])) {
                call_user_func_array($config['dynamic'], [&$config]);
            }

            $connector = new Connector();
            $connection = $connector->connect($config);
            $db = new Oci8Connection($connection, $database, $prefix, $config);

            if (! empty($config['skip_session_vars'])) {
                return $db;
            }

            // set oracle session variables
            $defaultSessionVars = [
                'NLS_TIME_FORMAT' => 'HH24:MI:SS',
                'NLS_DATE_FORMAT' => 'YYYY-MM-DD HH24:MI:SS',
                'NLS_TIMESTAMP_FORMAT' => 'YYYY-MM-DD HH24:MI:SS',
                'NLS_TIMESTAMP_TZ_FORMAT' => 'YYYY-MM-DD HH24:MI:SS TZH:TZM',
                'NLS_NUMERIC_CHARACTERS' => '.,',
            ];
            $sessionVars = config('database.connections.sessionVars', $defaultSessionVars);

            // Like Postgres, Oracle allows the concept of "schema"
            if (isset($config['schema'])) {
                $sessionVars['CURRENT_SCHEMA'] = $config['schema'];
            }

            if (isset($config['session'])) {
                $sessionVars = array_merge($sessionVars, $config['session']);
            }

            if (isset($config['edition'])) {
                $sessionVars = array_merge(
                    $sessionVars,
                    ['EDITION' => $config['edition']]
                );
            }

            $db->setSessionVars($sessionVars);

            return $db;
        });
        if ($worker) {
            Timer::add(55, function () {
                Db::connection("oracle")
                    ->select("select 1 from dual");
            });
        }
    }
}
