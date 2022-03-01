<?php

namespace App\Models;
use App\Traits\SaveToUpper;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use SaveToUpper;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $no_upper = ['pay_file'];

    protected $table = 'payment';
}