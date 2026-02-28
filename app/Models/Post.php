<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id', 'title', 'body', 'status', 'approved_by', 'rejected_reason'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function approvedBy() {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function logs() {
        return $this->hasMany(PostLog::class);
    }
}
