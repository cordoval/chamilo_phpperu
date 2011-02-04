<?php

namespace application\handbook;

use common\libraries\DataClass;
use common\libraries\Utilities;

/**
 * This class describes a HandbookPreference data object
 * @author Nathalie Blocry
 */
class HandbookPreference extends DataClass
{
    const CLASS_NAME = __CLASS__;
    const TABLE_NAME = 'handbook_preference';

    /**
     * HandbookPreference properties
     */
    const PROPERTY_ID = 'id';
    const PROPERTY_HANDBOOK_PUBLICATION_ID = 'handbook_publication_id';
    const PROPERTY_IMPORTANCE = 'importance';
    const PROPERTY_METADATA_PROPERTY_TYPE_ID = 'metadata_property_type_id';

    static function get_default_property_names()
    {
        return array(self::PROPERTY_ID, self :: PROPERTY_HANDBOOK_PUBLICATION_ID, self :: PROPERTY_IMPORTANCE, self :: PROPERTY_METADATA_PROPERTY_TYPE_ID);
    }

    function get_data_manager()
    {
        return HandbookDataManager :: get_instance();
    }

    function get_id()
    {
        return $this->get_default_property(self :: PROPERTY_ID);
    }

    function set_id($id)
    {
        $this->set_default_property(self :: PROPERTY_ID, $id);
    }

    function get_handbook_publication_id()
    {
        return $this->get_default_property(self :: PROPERTY_HANDBOOK_PUBLICATION_ID);
    }

    function set_handbook_publication_id($publication_id)
    {
        $this->set_default_property(self :: PROPERTY_HANDBOOK_PUBLICATION_ID, $publication_id);
    }

    function get_importance()
    {
        return $this->get_default_property(self :: PROPERTY_IMPORTANCE);
    }

    function set_importance($importance)
    {


        $preferences = $this->retrieve_related_preferences();
        $count = count($preferences);
        $old_importance = $this->get_importance();
        if ($old_importance == null && $preferences[$importance] != null)
        {
            $old_importance = count($preferences + 1);
        }
        if ($preferences[$importance] != null)
        {
            $pref = $preferences[$importance];
            $pref->set_importances($old_importance);
            $pref->update();
        }
        $this->set_default_property(self :: PROPERTY_IMPORTANCE, $importance);
    }

    function get_metadata_property_type_id()
    {
        return $this->get_default_property(self :: PROPERTY_METADATA_PROPERTY_TYPE_ID);
    }

    function set_metadata_property_type_id($metadata_property_type_id)
    {
        $this->set_default_property(self :: PROPERTY_METADATA_PROPERTY_TYPE_ID, $metadata_property_type_id);
    }

    static function get_table_name()
    {
        return self :: TABLE_NAME;
    }

    function create()
    {
        //todo check importance of all related preferences
        $preferences = $this->retrieve_related_preferences();
        $count = count($preferences);
        $this->set_importance($count + 1);
        return parent::create();
    }

    function update_and_check()
    {
        //TODO check importance of all related preferences


        return parent::update();
    }

    function delete()
    {
        //TODO check importance of all related preferences
        $preferences = $this->retrieve_related_preferences();
        $count = count($preferences);
        if ($count > 1)
        {
            $i = $this->get_importance();
            for ($i; $i < $count; $i++)
            {
                $pref = $preferences[$i];
                $old_importance = $pref->get_importance();
                $pref->set_importances($old_importance - 1);
                $pref->update();
            }
        }
       return parent::delete();
    }

    function retrieve_related_preferences()
    {
        $preferences = array();
        $hdm = HandbookDataManager::get_instance();
        $preference_set = $hdm->retrieve_preferences_for_publication($this->get_handbook_publication_id());

        if ($preference_set != false)
        {
            while ($pref = $preference_set->next_result())
            {
                $importance = $pref->get_importance();
                $preferences[$importance] = $pref;
            }
        }
        return $preferences;
    }

}

?>