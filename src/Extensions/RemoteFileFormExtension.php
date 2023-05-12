<?php
declare(strict_types=1);

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
        $fields = $form->Fields();
        if ($context['type'] === 'create') {
            $fields
                ->dataFieldByName('Url')
                ->addExtraClass('js-url-embed-field');
            $fields->insertAfter(
                'Url',
                DropdownField::create(
                    'UrlAws',
                    'Select AWS hosted video',
                    S3Video::get()->map('VideoLink', 'Name')
                )->setEmptyString('-- Select AWS Hosted Video --')
                ->addExtraClass('js-aws-video-field')
            );
        }
    }
}
