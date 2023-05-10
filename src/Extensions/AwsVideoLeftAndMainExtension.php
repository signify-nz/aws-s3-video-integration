<?php

namespace Signify\Extensions;

use SilverStripe\Core\Extension;
use SilverStripe\View\Requirements;

class AwsVideoLeftAndMainExtension extends Extension
{
    public function init()
    {
        Requirements::javascript('signify-nz/aws-s3-video-integration:js/aws-video-embed.js');
    }
}
