<?php

namespace Signify\Extensions;

use Signify\Models\S3Video;
use SilverStripe\Core\Extension;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\Form;

/**
* Extends the form used for remote files add in TinyMCE field
*
* Add option to select S3Video object instead of entering URL
*/
class RemoteFileFormExtension extends Extension
{
    public function updateForm(Form $form, $controller, $name, $context)
    {
        if ($context['type'] === 'create') {
            $form->Fields()->dataFieldByName('Url')->addExtraClass('js-url-embed-field');
            $form->Fields()->insertAfter(
                'Url',
                DropdownField::create(
                    'Url_Aws',
                    'Select AWS hosted video',
                    S3Video::get()->map('VideoLink', 'Name')
                )->setEmptyString('-- Select AWS Hosted Video --')
                ->addExtraClass('js-aws-video-field')
            );
        }
    }
}
