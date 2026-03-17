<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Category;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'description',
        'priority',
    ];

    protected $casts = [
        'priority' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function getPriorityLabelAttribute(): string
    {
        //bladeで使える。表示ように書くshow,index
        return match ($this->priority) {
            1 => '低',
            2 => '中',
            default => '高'
        };
    }
}
