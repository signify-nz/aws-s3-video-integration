<?php
declare(strict_types=1);

namespace Signify\Models;

use Signify\Validators\S3VideoValidator;
use SilverStripe\Forms\CompositeField;
use SilverStripe\Forms\CompositeValidator;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\LiteralField;
use SilverStripe\ORM\DataObject;
use SilverStripe\Versioned\Versioned;

/**
 * An AWS S3 Video
 */
class S3Video extends DataObject
{
    private static $table_name = 'S3Video';

    private static $singular_name = 'Video';

    private static $plural_name = 'Videos';

    private static $description = 'Video hosted on AWS S3';

    private static $extensions = [
        Versioned::class,
    ];

    private static $db = [
        'Name' => 'Varchar(255)',
        'ObjectKey' => 'Varchar(255)',
    ];

    private static $has_one = [
        'Bucket' => S3Bucket::class,
    ];

    public function getCMSFields(): FieldList
    {
        $fields = parent::getCMSFields();

        $objectKey = $fields->dataFieldByName('ObjectKey')
            ->setDescription(
                'The AWS S3 key name for the file e.g. "folder/example-key.mp4"<br>'
                . 'This can be found in the Amazon S3 console within the "Object overview" area'
                . ' visible when looking at an individual object (click on a filename to view).'
                . ' The "Key" value should be copied in full.'
            );
        $bucket = DropdownField::create(
            'BucketID',
            'Bucket',
            S3Bucket::get()->map('ID', 'BucketLink')
        )->setDescription(
            'If no options are appearing here then an S3 bucket needs to be created first.'
        );

        $fields->removeByName([
            'ObjectKey',
            'BucketID',
        ]);

        if (S3Bucket::get()->count() === 1) {
            $bucket->addExtraClass('hidden');
        }

        $awsFields = CompositeField::create(
            $objectKey,
            $bucket
        )->setTitle('Details for AWS hosted video');

        $fields->insertAfter('Name', $awsFields);

        $awsPreview = LiteralField::create(
            'AWSPreview',
            '<div class="form-group field"><h3 class="form__field-label">Video preview</h3>'
            . '<div class="form__field-holder"><p class="stacked"><em>'
            . 'If the preview does not show the correct video then the Object Key or Bucket details are '
            . 'incorrect, or the settings on the S3 Bucket in AWS may not allow access from this domain.'
            . '</br>Preview will update when saved.</em></p>'
            . '<video src="' . $this->getVideoLink() . '" width="320px" height="auto" controls></video>'
            . '</div></div>'
        )->setTitle('Preview');

        $fields->addFieldToTab('Root.Main', $awsPreview);
        return $fields;
    }

    public function getCMSCompositeValidator(): CompositeValidator
    {
        $validator = parent::getCMSCompositeValidator();

        $validator->addValidator(
            S3VideoValidator::create(
                'Name',
                'ObjectKey',
            )
        );

        return $validator;
    }

    public function getVideoLink(): string
    {
        if ($this->Bucket()->getBucketLink() && $this->ObjectKey) {
            $parts = [
                $this->Bucket()->getBucketLink(),
                $this->ObjectKey
            ];
            return join('/', $parts);
        }
    }
}
