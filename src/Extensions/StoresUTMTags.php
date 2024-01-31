<?php

namespace XD\UTMTags\Extensions;

use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldConfig;
use SilverStripe\Forms\GridField\GridFieldDataColumns;
use SilverStripe\Forms\GridField\GridFieldToolbarHeader;
use SilverStripe\ORM\ArrayList;
use SilverStripe\ORM\DataExtension;
use SilverStripe\ORM\DataObject;
use SilverStripe\View\ArrayData;
use XD\UTMTags\UTMTags;

/**
 * @property DataObject|self $owner
 */
class StoresUTMTags extends DataExtension
{
    private static $db = [
        'UTMTags' => 'Text'
    ];

    public function updateCMSFields(FieldList $fields)
    {
        $fields->removeByName(['UTMTags']);
        if (!($utmList = $this->owner->getUTMTagsList()) || !$utmList->exists()) {
            return;
        }

        $fields->addFieldsToTab('Root.UTMTags', [
            GridField::create(
                'UTMTagsList', 
                _t(__CLASS__ . '.UTMTags', 'UTMTags'), 
                $utmList,
                $config = GridFieldConfig::create(),
            )
        ]);

        /** @var GridFieldDataColumns $dataColumns */
        $dataColumns = new GridFieldDataColumns();
        $dataColumns->setDisplayFields([
            'Key' => 'Titel',
            'Value' => 'Waarde'
        ]);

        $config->addComponent($dataColumns);
        $config->addComponent(new GridFieldToolbarHeader());
    }

    public function setUTMTagsFromSession()
    {
        $utmTags = UTMTags::getTagsFromSession();
        if (!empty($utmTags)) {
            $this->owner->UTMTags = json_encode($utmTags);
        }
    }

    public function getUTMTagsList(): ArrayList
    {
        $list = new ArrayList();
        if (!$tags = $this->owner->UTMTags) {
            return $list;
        }

        $tags = json_decode($tags, true);
        foreach ($tags as $key => $value) {
            $list->add(new ArrayData([
                'Key' => $key,
                'Value' => $value
            ]));
        }

        return $list;
    }
}
