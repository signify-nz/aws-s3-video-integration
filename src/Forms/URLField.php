<?php
declare(strict_types=1);

namespace Signify\Forms;

use SilverStripe\Core\Validation\ValidationResult;
use SilverStripe\Forms\TextField;

/**
 * URL input field.
 */
class URLField extends TextField
{
    /**
     * {@inheritDoc}
     */
    public function __construct(
        $name,
        $title = null,
        $value = '',
        $maxLength = null,
        $form = null
    ) {
        $this->addExtraClass('text');
        $this->setAttribute('type', 'url');

        parent::__construct($name, $title, $value, $maxLength, $form);
    }

    /**
     * Validate this field
     *
     * @return bool
     */
    public function validate(): ValidationResult
    {
        $this->beforeExtending('updateValidate', function (ValidationResult $result) {
            // If no value is set, then URL cannot be invalid
            if (!$this->value) {
                return;
            }

            // Ensure the URL has protocol
            $protRe = '/https?:\/\/.*/m';
            if (!preg_match($protRe, $this->value)) {
                $this->value = 'https://' . $this->value;
            }

            $urlRe = '/^(?:http(s)?:\/\/)?[\w.-]+(?:\.[\w\.-]+)+[\w\-\._~:\/?#[\]@!\$&\'\(\)\*\+,;=.]+$/m';
            if (!preg_match($urlRe, $this->value)) {
                $result->addFieldError(
                    $this->getName(),
                    _t(
                        'App\\Field\\URLField.VALIDATEURL',
                        'The value for {name} must be a valid URL',
                        ['name' => $this->getName()]
                    )
                );
            }
        });

        return parent::validate();
    }
}
