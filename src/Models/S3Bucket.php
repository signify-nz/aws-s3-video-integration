<?php
declare(strict_types=1);

namespace Signify\Models;

use Signify\Admins\VideoAdmin;
use Signify\Forms\URLField;
use SilverStripe\Forms\CompositeValidator;
use SilverStripe\Forms\FieldList;
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

    private static $db = [
        'Domain' => 'Varchar(255)',
        'Sort' => 'Int'
    ];

    private static $has_many = [
        'Videos' => S3Video::class
    ];

    private static $summary_fields = [
        'Domain' => 'URL'
    ];

    private static $default_sort = 'Sort ASC';

    public function getCMSFields(): FieldList
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

    public function getBucketLink(): ?string
    {
        return $this->Domain;
    }

    /**
     * @param Member $member
     * @return boolean
     */
    public function canView($member = null)
    {
        $extended = $this->extendedCan(__FUNCTION__, $member);
        if ($extended !== null) {
            return $extended;
        }

        // Access locale admin permission
        return VideoAdmin::singleton()->canView($member);
    }

    /**
     * @param Member $member
     * @return boolean
     */
    public function canEdit($member = null)
    {
        $extended = $this->extendedCan(__FUNCTION__, $member);
        if ($extended !== null) {
            return $extended;
        }

        // Access locale admin permission
        return VideoAdmin::singleton()->canView($member);
    }

    /**
     * @param Member $member
     * @return boolean
     */
    public function canDelete($member = null)
    {
        $extended = $this->extendedCan(__FUNCTION__, $member);
        if ($extended !== null) {
            return $extended;
        }

        // Access locale admin permission
        return VideoAdmin::singleton()->canView($member);
    }

    /**
     * @param Member $member
     * @param array $context Additional context-specific data which might
     * affect whether (or where) this object could be created.
     * @return boolean
     */
    public function canCreate($member = null, $context = [])
    {
        $extended = $this->extendedCan(__FUNCTION__, $member, $context);
        if ($extended !== null) {
            return $extended;
        }

        // Access locale admin permission
        return VideoAdmin::singleton()->canView($member);
    }
}
