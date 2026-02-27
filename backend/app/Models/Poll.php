<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Poll extends Model
{
    use HasFactory;

    protected $fillable = ['question', 'user_id', 'is_active', 'slug'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($poll) {
            if (!$poll->slug) {
                $poll->slug = str_pad(random_int(0, 99999999), 8, '0', STR_PAD_LEFT);
                while (static::where('slug', $poll->slug)->exists()) {
                    $poll->slug = str_pad(random_int(0, 99999999), 8, '0', STR_PAD_LEFT);
                }
            }
        });
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function options()
    {
        return $this->hasMany(Option::class);
    }

    public function votes()
    {
        return $this->hasMany(Vote::class);
    }
}
