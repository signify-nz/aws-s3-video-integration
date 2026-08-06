<?php

namespace {

    use SilverStripe\CMS\Controllers\ContentController;

    // Minimal PageController class for standalone module testing.
    // silverstripe/cms (a dev dependency) ships RedirectorPageController, which
    // extends PageController. PageController is normally provided by the host
    // project's app/src/PageController.php, which does not exist when this module
    // is tested in isolation, so we define it here.
    class PageController extends ContentController {}
}
