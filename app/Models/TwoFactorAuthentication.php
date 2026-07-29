<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TwoFactorAuthentication extends Model
{
    protected $table = 'two_factor_authentications';

    protected $fillable = [
        'user_id',
        'secret',
        'recovery_codes',
        'is_enabled',
        'enabled_at',
    ];

    protected $hidden = [
        'secret',
    ];

    protected $casts = [
        'recovery_codes' => 'array',
        'is_enabled' => 'boolean',
        'enabled_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
