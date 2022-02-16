<?php

namespace App\Models;
use App\Traits\SaveToUpper;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use SaveToUpper;
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'orders';

    public function upload()
    {
        return $this->belongsTo(FileUpload::class, 'file_id');
    }
}