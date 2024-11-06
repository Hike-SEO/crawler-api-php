<?php

namespace App\Models;

use App\Enums\WaitUntil;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property WaitUntil $wait_until
 *
 * @method static \Database\Factories\WebsiteFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Website newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Website newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Website query()
 *
 * @mixin \Eloquent
 */
class Website extends Model
{
    use HasFactory;

    protected $fillable = [
        'url',
        'ignore_robots_txt',
        'wait_until',
        'skip_ignored_paths',
        'page_timeout',
        'max_concurrent_pages',
        'hike_user_agent',
    ];

    protected $casts = [
        'wait_until' => WaitUntil::class,
        'ignore_robots_txt' => 'boolean',
        'skip_ignored_paths' => 'boolean',
        'hike_user_agent' => 'boolean',
    ];
}
