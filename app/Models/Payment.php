<?php

namespace App\Models;

use App\Events\PaymentUpdated;
use App\Events\PaymentSuccessUpdated;
use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\Notifiable;

class Payment extends Model
{
    use HasFactory;
    use HasUuid;
    use Notifiable;

    protected $fillable = ['uuid', 'user_id', 'payment_date', 'expires_at', 'status', 'clp_usd'];

    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer'

    ];

    /*protected $dispatchesEvents = [
        'updated' => PaymentSuccessUpdated::class,
    ];*/

    protected $dispatchesEvents = [
        'updated' => PaymentUpdated::class,
    ];

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['client'] ?? null, function ($query, $client) {

            $client = Client::where('uuid', $client)->first();
            $query->where('client_id', $client->id);

        })/*->when($filters['trashed'] ?? null, function ($query, $trashed) {
            if ($trashed === 'with') {
                $query->withTrashed();
            } elseif ($trashed === 'only') {
                $query->onlyTrashed();
            }
        })*/;
    }
}
