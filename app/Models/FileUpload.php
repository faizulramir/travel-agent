<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FileUpload extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'file_upload';

    public function user()
    {
        return $this->belongsTo(DashboardUser::class, 'user_id');
    }
}