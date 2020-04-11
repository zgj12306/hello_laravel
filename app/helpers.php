<?php

function get_db_config()
{
    if (getenv('IS_IN_HEROKU')) {
        $url = parse_url(getenv("DATABASE_URL"));
        return $db_config = [
            'connection' => 'pgsql',
            'host' => $url['host'],
            'database' => substr($url['path'], 1),
            'username' => $url['user'],
            'password' => $url['pass'],
        ];
    } else {
        return [
            'connection' => env('DB_CONNECTION', 'mysql'),
            'host' => env('DB_HOST', 'localhost'),
            'database' => env('DB_DATABASE', 'database'),
            'username' => env('DB_USERNAME', 'name'),
            'password' => env('DB_PASSWORD', 'password'),
        ];
    }
}
