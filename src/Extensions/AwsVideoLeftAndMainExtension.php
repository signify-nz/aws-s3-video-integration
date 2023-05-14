<?php
declare(strict_types=1);

namespace Signify\Extensions;

use SilverStripe\Core\Extension;
use SilverStripe\View\Requirements;

/**
 * Extension to enable javascript for aws wysiwyg integration.
 */
class AwsVideoLeftAndMainExtension extends Extension
{
    public function init()
    {
        Requirements::javascript('signify-nz/aws-s3-video-integration:js/aws-video-embed.js');
    }
}
