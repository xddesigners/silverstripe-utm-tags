<?php

namespace XD\UTMTags\Extensions;

use SilverStripe\CMS\Controllers\ContentController;
use SilverStripe\Core\Extension;
use XD\UTMTags\UTMTags;

/**
 * @property ContentController $owner
 */
class ControllerExtension extends Extension
{
    public function onAfterInit()
    {   
        UTMTags::handleRequest($this->owner->getRequest());
    }
}
