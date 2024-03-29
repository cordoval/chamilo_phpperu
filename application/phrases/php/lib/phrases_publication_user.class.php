<?php
namespace application\phrases;

use common\libraries\DataClass;

/**
 * @author Hans De Bisschop
 * @package application.phrases
 */

class PhrasesPublicationUser extends DataClass
{
    const CLASS_NAME = __CLASS__;
    const TABLE_NAME = 'publication_user';

    /**
     * PhrasesPublicationUser properties
     */
    const PROPERTY_PUBLICATION = 'publication_id';
    const PROPERTY_USER = 'user_id';

    /**
     * Get the default properties
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return array(self :: PROPERTY_PUBLICATION, self :: PROPERTY_USER);
    }

    function get_data_manager()
    {
        return PhrasesDataManager :: get_instance();
    }

    /**
     * Returns the phrases_publication of this PhrasesPublicationUser.
     * @return the phrases_publication.
     */
    function get_publication()
    {
        return $this->get_default_property(self :: PROPERTY_PUBLICATION);
    }

    /**
     * Sets the phrases_publication of this PhrasesPublicationUser.
     * @param phrases_publication
     */
    function set_publication($phrases_publication)
    {
        $this->set_default_property(self :: PROPERTY_PUBLICATION, $phrases_publication);
    }

    /**
     * Returns the user of this PhrasesPublicationUser.
     * @return the user.
     */
    function get_user()
    {
        return $this->get_default_property(self :: PROPERTY_USER);
    }

    /**
     * Sets the user of this PhrasesPublicationUser.
     * @param user
     */
    function set_user($user)
    {
        $this->set_default_property(self :: PROPERTY_USER, $user);
    }

    function create()
    {
        return $this->get_data_manager()->create_phrases_publication_user($this);
    }

    static function get_table_name()
    {
        return self :: TABLE_NAME;
    }
}
?>