<?php

namespace App\Models;
use App\Traits\SaveToUpper;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use SaveToUpper;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'plan';
}