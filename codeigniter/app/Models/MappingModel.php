<?php

namespace App\Models;

use CodeIgniter\Model;

class MappingModel extends Model
{
    protected $table      = 'mapping';
    protected $primaryKey = 'id';

    protected $allowedFields = ['type', 'local_id', 'external_id'];
}
