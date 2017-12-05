<?php

namespace App\Models;

use Framework\DB\Model;

class Test extends Model
{
    protected $connection = 'con1';

    protected $table = 'users';
}
