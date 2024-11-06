<?php

namespace App\Models;

use App\Enums\WaitUntil;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property WaitUntil $wait_until
 * @method static \Database\Factories\WebsiteFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|Website newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Website newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Website query()
 * @property int $id
 * @property string $url
 * @property bool $ignore_robots_txt
 * @property bool $skip_ignored_paths
 * @property int $page_timeout
 * @property int $max_concurrent_pages
 * @property bool $hike_user_agent
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Website whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Website whereHikeUserAgent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Website whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Website whereIgnoreRobotsTxt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Website whereMaxConcurrentPages($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Website wherePageTimeout($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Website whereSkipIgnoredPaths($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Website whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Website whereUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Website whereWaitUntil($value)
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
