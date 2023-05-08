<?php
namespace Signify\Validators;

use SilverStripe\Forms\RequiredFields;

/**
 * Validator for the {@link Signify\S3Video} object
 *
 * Validates the BucketID independently of the required fields. If BucketID
 * were validated as a standard required field, the implicit behaviour of S3Videos
 * automatically linking when there is only one available Bucket would no longer work correctly.
 *
 */
class S3VideoValidator extends RequiredFields
{
    public function php($data)
    {
        $valid = parent::php($data);

        $fields = $this->form->Fields();
        if (empty($data['BucketID'])) {
            $message = "Bucket is required.";
            $this->result->addFieldError('BucketID', $message);
            $valid = false;
        }

        return $valid;
    }
}
