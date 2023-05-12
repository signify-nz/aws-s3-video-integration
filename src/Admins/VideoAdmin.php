<?php
declare(strict_types=1);

namespace Signify\Admins;

use SilverStripe\Admin\ModelAdmin;
use Signify\Models\S3Bucket;
use Signify\Models\S3Video;
use SilverStripe\Forms\GridField\GridFieldConfig;
use Symbiote\GridFieldExtensions\GridFieldOrderableRows;

class VideoAdmin extends ModelAdmin
{

    private static $managed_models = [
        S3Video::class => ['title' => 'Videos'],
        S3Bucket::class => ['title' => 'S3 Buckets'],
    ];

    private static $url_segment = 'videos';

    private static $menu_title = 'Videos';

    protected function getGridFieldConfig(): GridFieldConfig
    {
        $config = parent::getGridFieldConfig();

        if ($this->modelClass === S3Bucket::class) {
            $config->addComponent(new GridFieldOrderableRows('Sort'));
        }

        return $config;
    }
}
