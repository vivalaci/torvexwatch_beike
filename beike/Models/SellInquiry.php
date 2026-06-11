<?php

namespace Beike\Models;

use Illuminate\Database\Eloquent\Model;

class SellInquiry extends Model
{
    protected $fillable = ['email', 'brand', 'ip_address'];
}
