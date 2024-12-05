<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $website_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $finished_at
 * @property string|null $file_path
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PageCrawl> $pageCrawls
 * @property-read int|null $page_crawls_count
 * @property-read \App\Models\Website $website
 *
 * @method static \Database\Factories\FullCrawlFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|FullCrawl newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FullCrawl newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FullCrawl query()
 * @method static \Illuminate\Database\Eloquent\Builder|FullCrawl whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FullCrawl whereFilePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FullCrawl whereFinishedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FullCrawl whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FullCrawl whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FullCrawl whereWebsiteId($value)
 *
 * @mixin \Eloquent
 */
class FullCrawl extends Model
{
    use HasFactory;

    protected $fillable = [
        'website_id',
        'finished_at',
        'file_path',
    ];

    protected $casts = [
        'finished_at' => 'datetime',
    ];

    public function website(): BelongsTo
    {
        return $this->belongsTo(Website::class);
    }

    public function pageCrawls(): HasMany
    {
        return $this->hasMany(PageCrawl::class);
    }
}
