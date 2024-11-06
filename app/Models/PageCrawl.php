<?php

namespace App\Models;

use App\Data\CrawledPage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PageCrawl extends Model
{
    use HasFactory;

    protected $fillable = [
        'url',
        'full_crawl_id',
        'data',
        'finished_at',
    ];

    protected $casts = [
        'data' => CrawledPage::class,
        'finished_at' => 'datetime',
    ];
}
