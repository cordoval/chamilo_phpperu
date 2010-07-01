<?php
/**
 * @package application.lib.weblcms.trackers
 */

class ForumTopicViewTracker extends SimpleTracker
{
    const CLASS_NAME = __CLASS__;
    
    const PROPERTY_USER_ID = 'user_id';
    const PROPERTY_DATE = 'date';
    const PROPERTY_PUBLICATION_ID = 'publication_id';
    const PROPERTY_FORUM_TOPIC_ID = 'forum_topic_id';

    function validate_parameters(array $parameters = array())
    {
        $this->set_user_id($parameters[self :: PROPERTY_USER_ID]);
        $this->set_publication_id($parameters[self :: PROPERTY_PUBLICATION_ID]);
        $this->set_forum_topic_id($parameters[self :: PROPERTY_FORUM_TOPIC_ID]);
        $this->set_date(time());
    }

    /**
     * Inherited
     */
    function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_USER_ID, self :: PROPERTY_PUBLICATION_ID, self :: PROPERTY_FORUM_TOPIC_ID, self :: PROPERTY_DATE));
    }

    function get_user_id()
    {
        return $this->get_default_property(self :: PROPERTY_USER_ID);
    }

    function set_user_id($user_id)
    {
        $this->set_default_property(self :: PROPERTY_USER_ID, $user_id);
    }

    function get_publication_id()
    {
        return $this->get_default_property(self :: PROPERTY_PUBLICATION_ID);
    }

    function set_publication_id($publication_id)
    {
        $this->set_default_property(self :: PROPERTY_PUBLICATION_ID, $publication_id);
    }

    function get_forum_topic_id()
    {
        return $this->get_default_property(self :: PROPERTY_FORUM_TOPIC_ID);
    }

    function set_forum_topic_id($forum_topic_id)
    {
        $this->set_default_property(self :: PROPERTY_FORUM_TOPIC_ID, $forum_topic_id);
    }

    function get_date()
    {
        return $this->get_default_property(self :: PROPERTY_DATE);
    }

    function set_date($date)
    {
        $this->set_default_property(self :: PROPERTY_DATE, $date);
    }

    static function get_table_name()
    {
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }
}
?>