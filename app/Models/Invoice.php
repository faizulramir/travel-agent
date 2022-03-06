<?php

namespace App\Models;
// use App\Traits\SaveToUpper;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    // use SaveToUpper;
    protected $table = 'invoice';
    
}