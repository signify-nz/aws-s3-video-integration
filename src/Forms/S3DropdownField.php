<?php
declare(strict_types=1);

namespace Signify\Forms;

use ArrayAccess;
use SilverStripe\Forms\DropdownField;
use Signify\Models\S3Video;

/**
 * Dropdown selection for {@link Signify\Models\S3Video} data objects with
 * a link in the description to the video admin section of
 * the CMS and an empty string option pre-set.
 */
class S3DropdownField extends DropdownField
{
    public function __construct(
        string $name,
        string|null $title = null,
        array|ArrayAccess $source = [],
        mixed $value = null,
    ) {
        if (empty($source)) {
            $source = S3Video::get()->map('ID', 'Name')->toArray();
        }

        $this->setDescription(
            '<em><a href="admin/videos/" target="__blank">'
            . 'Add or edit videos '
            . '<span class="font-icon-external-link"></a><em>'
        );
        $this->setEmptyString('--Select Video--');

        parent::__construct($name, $title, $source, $value);
    }
}
