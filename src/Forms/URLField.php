<?php

namespace Signify\Forms;

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
     * @param Validator $validator
     * @return bool
     */
    public function validate($validator)
    {
        // If no value is set, then URL cannot be invalid
        if (!$this->value) {
            return true;
        }

        $res = parent::validate($validator);

        // Ensure the URL has protocol
        $protRe = '/https?:\/\/.*/m';
        if (!preg_match($protRe, $this->value)) {
            $this->value = 'https://' . $this->value;
        }

        $urlRe = '/^(?:http(s)?:\/\/)?[\w.-]+(?:\.[\w\.-]+)+[\w\-\._~:\/?#[\]@!\$&\'\(\)\*\+,;=.]+$/m';
        if (!preg_match($urlRe, $this->value)) {
            $validator->validationError(
                $this->name,
                _t(
                    'App\\Field\\URLField.VALIDATEURL',
                    'The value for {name} must be a valid URL',
                    ['name' => $this->getName()]
                ),
                'validation'
            );
            $res = false;
        }

        return !$res ? false : true;
    }
}
