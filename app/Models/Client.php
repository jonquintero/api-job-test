<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;

class Client extends Model
{
    use HasFactory;
    use HasUuid;
    use Notifiable;

    protected $fillable = ['uuid', 'email'];

    protected $casts = [
        'id' => 'integer',

    ];

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function payment(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}
