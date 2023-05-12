<?php
declare(strict_types=1);

namespace Signify\Models;

use Signify\Forms\URLField;
use SilverStripe\Forms\CompositeValidator;
use SilverStripe\Forms\RequiredFields;
use SilverStripe\ORM\DataObject;

/**
 * An AWS S3 Bucket
 */
class S3Bucket extends DataObject
{

    private static $table_name = 'S3Bucket';

    private static $singular_name = 'AWS S3 Bucket';

    private static $plural_name = 'AWS S3 Buckets';

    private static $description = 'Connection details for an AWS S3 bucket';

    /**
     * @var array
     */
    private static $db = [
        'Domain' => 'Varchar(255)',
        'Sort' => 'Int'
    ];

    /**
     * @var array
     */
    private static $has_many = [
        'Videos' => S3Video::class
    ];

    private static $summary_fields = [
        'Domain' => 'URL'
    ];

    private static $default_sort = 'Sort ASC';

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();
        $fields->removeByName([
            'Sort'
        ]);

        $fields->replaceField(
            'Domain',
            URLField::create('Domain', 'URL for the AWS S3 bucket')
        );
        return $fields;
    }

    public function getCMSCompositeValidator(): CompositeValidator
    {
        $validator = parent::getCMSCompositeValidator();

        $validator->addValidator(
            RequiredFields::create(
                'Domain'
            )
        );

        return $validator;
    }

    public function getBucketLink()
    {
        return $this->Domain;
    }
}
