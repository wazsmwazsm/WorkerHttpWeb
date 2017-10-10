<?php

return [
    'db_con' => [
        'con1' => [
          'driver'   => 'mysql',
          'host'     => 'localhost',
          'port'     => '3306',
          'user'     => 'homestead',
          'password' => 'secret',
          'dbname'   => 'homestead',
          'charset'  => 'utf8',
        ],
        'con2' => [
          'driver'   => 'mysql',
          'host'     => 'localhost',
          'port'     => '3306',
          'user'     => 'homestead',
          'password' => 'secret',
          'dbname'   => 'ad_show_control_db',
          'charset'  => 'utf8',
        ],
    ],

    'redis' => [
      'cluster' => FALSE,
      'options' => NULL,
      'rd_con' => [
          'default' => [
              'host'     => '127.0.0.1',
              'password' => NULL,
              'port'     => 6379,
              'database' => 0,
          ],
      ]
    ]
];
