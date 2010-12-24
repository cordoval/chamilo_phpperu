<?php 
namespace application\survey;

use common\libraries\Utilities;
use common\libraries\Path;
use rights\RightsUtilities;
use common\libraries\DataClass;
use repository\RepositoryDataManager;


class SurveyPublication extends DataClass
{
    const CLASS_NAME = __CLASS__;
    const TABLE_NAME = 'publication';
    
    /**
     * SurveyPublication properties
     */
    const PROPERTY_CONTENT_OBJECT_ID = 'content_object_id';
    const PROPERTY_FROM_DATE = 'from_date';
    const PROPERTY_TO_DATE = 'to_date';
    const PROPERTY_PUBLISHER = 'publisher_id';
    const PROPERTY_PUBLISHED = 'published';
    const PROPERTY_TYPE = 'type';
    
    const TYPE_OFFICIAL = 1;
    const TYPE_NAME_OFFICIAL = 'official';
    const TYPE_VOLUNTEER = 2;
    const TYPE_NAME_VOLUNTEER = 'volunteer';

    public function create()
    {
        $succes = parent :: create();
        $parent_location = SurveyRights :: get_surveys_subtree_root_id();
        $location = SurveyRights :: create_location_in_surveys_subtree($this->get_content_object_id(), $this->get_id(), $parent_location, SurveyRights :: TYPE_PUBLICATION, true);
        
        $rights = SurveyRights :: get_available_rights_for_publications();
        foreach ($rights as $right)
        {
            if ($right != SurveyRights :: RIGHT_PARTICIPATE)
            {
                RightsUtilities :: set_user_right_location_value($right, $this->get_publisher(), $location->get_id(), 1);
            }
        }
        return $succes;
    }

    public function update()
    {
        
        $succes = parent :: update();
        return $succes;
    }

    public function delete()
    {
        
        $location = SurveyRights :: get_location_by_identifier_from_surveys_subtree($this->get_id(), SurveyRights :: TYPE_PUBLICATION);
        if ($location)
        {
            if (! $location->remove())
            {
                return false;
            }
        }
        
        $succes = parent :: delete();
        return $succes;
    }

    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_CONTENT_OBJECT_ID, self :: PROPERTY_FROM_DATE, self :: PROPERTY_TO_DATE, self :: PROPERTY_PUBLISHER, self :: PROPERTY_PUBLISHED, self :: PROPERTY_TYPE));
    }

    function get_data_manager()
    {
        return SurveyDataManager :: get_instance();
    }

    /**
     * Returns the content_object_id of this SurveyPublication.
     * @return the content_object_id.
     */
    function get_content_object_id()
    {
        return $this->get_default_property(self :: PROPERTY_CONTENT_OBJECT_ID);
    }

    /**
     * Sets the content_object_id of this SurveyPublication.
     * @param content_object_id
     */
    function set_content_object_id($content_object_id)
    {
        $this->set_default_property(self :: PROPERTY_CONTENT_OBJECT_ID, $content_object_id);
    }

    /**
     * Returns the from_date of this SurveyPublication.
     * @return the from_date.
     */
    function get_from_date()
    {
        return $this->get_default_property(self :: PROPERTY_FROM_DATE);
    }

    /**
     * Sets the from_date of this SurveyPublication.
     * @param from_date
     */
    function set_from_date($from_date)
    {
        $this->set_default_property(self :: PROPERTY_FROM_DATE, $from_date);
    }

    /**
     * Returns the to_date of this SurveyPublication.
     * @return the to_date.
     */
    function get_to_date()
    {
        return $this->get_default_property(self :: PROPERTY_TO_DATE);
    }

    /**
     * Sets the to_date of this SurveyPublication.
     * @param to_date
     */
    function set_to_date($to_date)
    {
        $this->set_default_property(self :: PROPERTY_TO_DATE, $to_date);
    }

    function get_type()
    {
        return $this->get_default_property(self :: PROPERTY_TYPE);
    }

    function set_type($type)
    {
        $this->set_default_property(self :: PROPERTY_TYPE, $type);
    }

    static public function get_types()
    {
        return array(self :: TYPE_OFFICIAL => self :: TYPE_NAME_OFFICIAL, self :: TYPE_VOLUNTEER => self :: TYPE_NAME_VOLUNTEER);
    }

    /**
     * Returns the publisher of this SurveyPublication.
     * @return the publisher.
     */
    function get_publisher()
    {
        return $this->get_default_property(self :: PROPERTY_PUBLISHER);
    }

    /**
     * Sets the publisher of this SurveyPublication.
     * @param publisher
     */
    function set_publisher($publisher)
    {
        $this->set_default_property(self :: PROPERTY_PUBLISHER, $publisher);
    }

    /**
     * Returns the published of this SurveyPublication.
     * @return the published.
     */
    function get_published()
    {
        return $this->get_default_property(self :: PROPERTY_PUBLISHED);
    }

    /**
     * Sets the published of this SurveyPublication.
     * @param published
     */
    function set_published($published)
    {
        $this->set_default_property(self :: PROPERTY_PUBLISHED, $published);
    }
   
    function is_publication_period()
    {
        
        $from_date = $this->get_from_date();
        $to_date = $this->get_to_date();
        if ($from_date == 0 && $to_date == 0)
        {
            return true;
        }
        
        $time = time();
        
        if ($time < $from_date || $time > $to_date)
        {
            return false;
        }
        else
        {
            return true;
        }
    
    }

    static function get_table_name()
    {
        return self :: TABLE_NAME;
    }

    function get_publication_object()
    {
        $rdm = RepositoryDataManager :: get_instance();
        return $rdm->retrieve_content_object($this->get_content_object_id());
    }

}

?>