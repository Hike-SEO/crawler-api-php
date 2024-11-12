<?php

namespace App\Models;

use App\Data\CrawledPage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $full_crawl_id
 * @property string $url
 * @property \Spatie\LaravelData\Contracts\BaseData|null $data
 * @property string|null $error
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $crawled_at
 *
 * @method static \Database\Factories\PageCrawlFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|PageCrawl newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PageCrawl newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PageCrawl query()
 * @method static \Illuminate\Database\Eloquent\Builder|PageCrawl whereCrawledAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PageCrawl whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PageCrawl whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PageCrawl whereError($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PageCrawl whereFullCrawlId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PageCrawl whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PageCrawl whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PageCrawl whereUrl($value)
 *
 * @mixin \Eloquent
 */
class PageCrawl extends Model
{
    use HasFactory;

    protected $fillable = [
        'url',
        'full_crawl_id',
        'data',
        'error',
        'crawled_at',
    ];

    protected $casts = [
        'data' => CrawledPage::class,
        'crawled_at' => 'datetime',
    ];
}
