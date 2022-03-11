<?php

namespace App\Models;
use App\Traits\SaveToUpper;
use Illuminate\Database\Eloquent\Model;

class FileUpload extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    use SaveToUpper;
    protected $no_upper = ['json_inv', 'file_name'];
    protected $table = 'file_upload';

    public function user()
    {
        return $this->belongsTo(DashboardUser::class, 'user_id');
    }

    public function order()
    {
        return $this->hasMany(Order::class, 'file_id', 'id');
    }

    public function pay()
    {
        return $this->hasOne(Payment::class, 'file_id', 'id');
    }
}