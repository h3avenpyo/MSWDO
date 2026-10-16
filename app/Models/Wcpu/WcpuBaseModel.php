<?php

namespace App\Models\Wcpu;

use Illuminate\Database\Eloquent\Model;

abstract class WcpuBaseModel extends Model
{
    /**
     * The database connection name for WCPU models.
     *
     * @var string
     */
    protected $connection = 'wcpu';
}
