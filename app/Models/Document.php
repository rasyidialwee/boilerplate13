<?php

namespace App\Models;

use CleaniqueCoders\Traitify\Concerns\InteractsWithUuid;
use Database\Factories\DocumentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Document extends Model implements HasMedia
{
    /** @use HasFactory<DocumentFactory> */
    use HasFactory;

    use InteractsWithMedia;
    use InteractsWithUuid;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'user_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('default')->singleFile();
    }
}
