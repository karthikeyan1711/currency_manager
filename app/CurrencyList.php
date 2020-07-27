<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CurrencyList extends Model
{
    protected $fillable = ['id','currency_name','status'];
}
