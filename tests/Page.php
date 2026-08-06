<?php

namespace {

    use SilverStripe\CMS\Model\SiteTree;

    // Minimal Page class for standalone module testing.
    // silverstripe/cms (a dev dependency) ships RedirectorPage, which extends Page.
    // Page is normally provided by the host project's app/src/Page.php, which does
    // not exist when this module is tested in isolation, so we define it here.
    class Page extends SiteTree {}
}
