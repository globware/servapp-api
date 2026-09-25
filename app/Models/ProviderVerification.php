<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProviderVerification extends Model
{
    protected $guarded = [];

    protected $casts = [
        'decided_at' => 'datetime',
    ];

    public function provider()
    {
        return $this->belongsTo(User::class, 'provider_id');
    }

    public function idFile()
    {
        return $this->belongsTo(File::class, 'id_file_id');
    }

    public function businessFile()
    {
        return $this->belongsTo(File::class, 'business_file_id');
    }
}
