<?php

/**
 * @package admin.lib
 * $Id: system_announcement_publication.class.php 168 2009-11-12 11:53:23Z vanpouckesven $
 * @author Hans De Bisschop
 */

class SystemAnnouncementPublication extends DataClass
{
    const CLASS_NAME = __CLASS__;
    
    const PROPERTY_CONTENT_OBJECT_ID = 'content_object_id';
    const PROPERTY_FROM_DATE = 'from_date';
    const PROPERTY_TO_DATE = 'to_date';
    const PROPERTY_HIDDEN = 'hidden';
    const PROPERTY_PUBLISHER = 'publisher_id';
    const PROPERTY_PUBLISHED = 'published';
    const PROPERTY_MODIFIED = 'modified';
    const PROPERTY_EMAIL_SENT = 'email_sent';
    
    private $target_groups;
    private $target_users;

    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_CONTENT_OBJECT_ID, self :: PROPERTY_FROM_DATE, self :: PROPERTY_TO_DATE, self :: PROPERTY_HIDDEN, self :: PROPERTY_PUBLISHER, self :: PROPERTY_PUBLISHED, self :: PROPERTY_MODIFIED, self :: PROPERTY_EMAIL_SENT));
    }

    /**
     * inherited
     */
    function get_data_manager()
    {
        return AdminDataManager :: get_instance();
    }

    function get_content_object_id()
    {
        return $this->get_default_property(self :: PROPERTY_CONTENT_OBJECT_ID);
    }

    function get_from_date()
    {
        return $this->get_default_property(self :: PROPERTY_FROM_DATE);
    }

    function get_to_date()
    {
        return $this->get_default_property(self :: PROPERTY_TO_DATE);
    }

    function get_hidden()
    {
        return $this->get_default_property(self :: PROPERTY_HIDDEN);
    }

    function get_publisher()
    {
        return $this->get_default_property(self :: PROPERTY_PUBLISHER);
    }

    function get_published()
    {
        return $this->get_default_property(self :: PROPERTY_PUBLISHED);
    }

    function get_modified()
    {
        return $this->get_default_property(self :: PROPERTY_MODIFIED);
    }

    function get_email_sent()
    {
        return $this->get_default_property(self :: PROPERTY_EMAIL_SENT);
    }

    function set_content_object_id($id)
    {
        $this->set_default_property(self :: PROPERTY_CONTENT_OBJECT_ID, $id);
    }

    function set_from_date($from_date)
    {
        $this->set_default_property(self :: PROPERTY_FROM_DATE, $from_date);
    }

    function set_to_date($to_date)
    {
        $this->set_default_property(self :: PROPERTY_TO_DATE, $to_date);
    }

    function set_hidden($hidden)
    {
        $this->set_default_property(self :: PROPERTY_HIDDEN, $hidden);
    }

    function set_publisher($publisher)
    {
        $this->set_default_property(self :: PROPERTY_PUBLISHER, $publisher);
    }

    function set_published($published)
    {
        $this->set_default_property(self :: PROPERTY_PUBLISHED, $published);
    }

    function set_modified($modified)
    {
        $this->set_default_property(self :: PROPERTY_MODIFIED, $modified);
    }

    function set_email_sent($email_sent)
    {
        $this->set_default_property(self :: PROPERTY_EMAIL_SENT, $email_sent);
    }

    function get_publication_object()
    {
        $rdm = RepositoryDataManager :: get_instance();
        return $rdm->retrieve_content_object($this->get_content_object_id());
    }

    function get_publication_publisher()
    {
        $udm = UserDataManager :: get_instance();
        return $udm->retrieve_user($this->get_publisher());
    }

    function was_email_sent()
    {
        return $this->get_email_sent();
    }

    function create()
    {
        $now = time();
        $this->set_published($now);
        
        return parent :: create();
    }

    static function get_table_name()
    {
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }

    function is_hidden()
    {
        return $this->get_hidden();
    }

    function is_forever()
    {
        return $this->get_from_date() == 0 && $this->get_to_date() == 0;
    }

    /**
     * Toggles the visibility of this publication.
     */
    function toggle_visibility()
    {
        $this->set_hidden(! $this->is_hidden());
    }

    function is_visible_for_target_users()
    {
        return (! $this->is_hidden()) && ($this->is_forever() || ($this->get_from_date() <= time() && time() <= $this->get_to_date()));
    }

    function get_target_users()
    {
        if (! isset($this->target_users))
        {
            $adm = AdminDataManager :: get_instance();
            $this->target_users = $adm->retrieve_system_announcement_publication_target_users($this);
        }
        
        return $this->target_users;
    }

    function get_target_groups()
    {
        if (! isset($this->target_groups))
        {
            $adm = AdminDataManager :: get_instance();
            $this->target_groups = $adm->retrieve_system_announcement_publication_target_groups($this);
        }
        
        return $this->target_groups;
    }

    function set_target_users($target_users)
    {
        $this->target_users = $target_users;
    }

    function set_target_groups($target_groups)
    {
        $this->target_groups = $target_groups;
    }
}
?>