<?php
/**
 * $Id: personal_messenger_manager_component.class.php 205 2009-11-13 12:57:33Z vanpouckesven $
 * @package application.personal_messenger.personal_messenger_manager
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */

abstract class PersonalMessengerManagerComponent extends WebApplicationComponent
{

    /**
     * Constructor
     * @param PersonalMessage $pm The pm which
     * provides this component
     */
    protected function PersonalMessengerManagerComponent($pm)
    {
        parent :: __construct($pm);
    }

    /**
     * @see PersonalMessengerManager :: get_folder()
     */
    function get_folder()
    {
        return $this->get_parent()->get_folder();
    }

    /**
     * @see PersonalMessengerManager :: count_personal_message_publications()
     */
    function count_personal_message_publications($condition = null)
    {
        return $this->get_parent()->count_personal_message_publications($condition);
    }

    /**
     * @see PersonalMessengerManager :: retrieve_personal_message_publication()
     */
    function retrieve_personal_message_publication($id)
    {
        return $this->get_parent()->retrieve_personal_message_publication($id);
    }

    /**
     * @see PersonalMessengerManager :: retrieve_personal_message_publications()
     */
    function retrieve_personal_message_publications($condition = null, $order_by = array (), $offset = 0, $max_objects = -1)
    {
        return $this->get_parent()->retrieve_personal_message_publications($condition, $order_by, $offset, $max_objects);
    }

    /**
     * @see PersonalMessengerManager :: get_publication_deleting_url()
     */
    function get_publication_deleting_url($personal_message)
    {
        return $this->get_parent()->get_publication_deleting_url($personal_message);
    }

    /**
     * @see PersonalMessengerManager :: get_publication_viewing_url()
     */
    function get_publication_viewing_url($personal_message)
    {
        return $this->get_parent()->get_publication_viewing_url($personal_message);
    }

    function get_publication_viewing_link($personal_message)
    {
        return $this->get_parent()->get_publication_viewing_link($personal_message);
    }

    /**
     * @see PersonalMessengerManager :: get_personal_message_creation_url()
     */
    function get_personal_message_creation_url()
    {
        return $this->get_parent()->get_personal_message_creation_url();
    }

    /**
     * @see PersonalMessengerManager :: get_publication_reply_url()
     */
    function get_publication_reply_url($personal_message)
    {
        return $this->get_parent()->get_publication_reply_url($personal_message);
    }
}
?>