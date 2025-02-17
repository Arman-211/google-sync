<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @method static allowed()
 */
class Record extends Model
{
    public function scopeAllowed($query)
    {
        return $query->where('status', 'Allowed');
    }

}
