<?php
namespace application\phrases;

use common\libraries\DataClass;

/**
 * @author Hans De Bisschop
 * @package application.phrases
 */

class PhrasesPublicationGroup extends DataClass
{
    const CLASS_NAME = __CLASS__;
    const TABLE_NAME = 'publication_group';

    /**
     * PhrasesPublicationGroup properties
     */
    const PROPERTY_PUBLICATION = 'publication_id';
    const PROPERTY_GROUP_ID = 'group_id';

    /**
     * Get the default properties
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return array(self :: PROPERTY_PUBLICATION, self :: PROPERTY_GROUP_ID);
    }

    function get_data_manager()
    {
        return PhrasesDataManager :: get_instance();
    }

    /**
     * Returns the phrases_publication of this PhrasesPublicationGroup.
     * @return the phrases_publication.
     */
    function get_publication()
    {
        return $this->get_default_property(self :: PROPERTY_PUBLICATION);
    }

    /**
     * Sets the phrases_publication of this PhrasesPublicationGroup.
     * @param phrases_publication
     */
    function set_publication($phrases_publication)
    {
        $this->set_default_property(self :: PROPERTY_PUBLICATION, $phrases_publication);
    }

    /**
     * Returns the group_id of this PhrasesPublicationGroup.
     * @return the group_id.
     */
    function get_group_id()
    {
        return $this->get_default_property(self :: PROPERTY_GROUP_ID);
    }

    /**
     * Sets the group_id of this PhrasesPublicationGroup.
     * @param group_id
     */
    function set_group_id($group_id)
    {
        $this->set_default_property(self :: PROPERTY_GROUP_ID, $group_id);
    }

    function create()
    {
        return $this->get_data_manager()->create_phrases_publication_group($this);
    }

    static function get_table_name()
    {
        return self :: TABLE_NAME;
    }
}
?>