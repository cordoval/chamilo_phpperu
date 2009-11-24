<?php
/**
 * $Id: content_object_publication.class.php 218 2009-11-13 14:21:26Z kariboe $
 * @package application.lib.weblcms
 */
/**
 * This class represents a learning object publication.
 *
 * When publishing a learning object from the repository in the weblcms
 * application, a new object of this type is created.
 */
class ContentObjectPublication extends DataClass
{
    const CLASS_NAME = __CLASS__;

    /**#@+
     * Constant defining a property of the publication
     */
    const PROPERTY_CONTENT_OBJECT_ID = 'content_object_id';
    const PROPERTY_COURSE_ID = 'course_id';
    const PROPERTY_TOOL = 'tool';
    const PROPERTY_PARENT_ID = 'parent_id';
    const PROPERTY_CATEGORY_ID = 'category_id';
    const PROPERTY_FROM_DATE = 'from_date';
    const PROPERTY_TO_DATE = 'to_date';
    const PROPERTY_HIDDEN = 'hidden';
    const PROPERTY_PUBLISHER_ID = 'publisher_id';
    const PROPERTY_PUBLICATION_DATE = 'published';
    const PROPERTY_MODIFIED_DATE = 'modified';
    const PROPERTY_DISPLAY_ORDER_INDEX = 'display_order';
    const PROPERTY_EMAIL_SENT = 'email_sent';
    const PROPERTY_SHOW_ON_HOMEPAGE = 'show_on_homepage';

    private $target_course_groups;
    private $target_users;

    private $content_object;
    private $publisher;

    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_CONTENT_OBJECT_ID, self :: PROPERTY_COURSE_ID, self :: PROPERTY_TOOL, self :: PROPERTY_PARENT_ID, self :: PROPERTY_CATEGORY_ID, self :: PROPERTY_FROM_DATE, self :: PROPERTY_TO_DATE, self :: PROPERTY_HIDDEN, self :: PROPERTY_PUBLISHER_ID, self :: PROPERTY_PUBLICATION_DATE, self :: PROPERTY_MODIFIED_DATE, self :: PROPERTY_DISPLAY_ORDER_INDEX, self :: PROPERTY_EMAIL_SENT, self :: PROPERTY_SHOW_ON_HOMEPAGE));
    }

    /**
     * inherited
     */
    function get_data_manager()
    {
        return WeblcmsDataManager :: get_instance();
    }

    /**
     * Gets the learning object.
     * @return ContentObject
     */
    function get_content_object_id()
    {
        return $this->get_default_property(self :: PROPERTY_CONTENT_OBJECT_ID);
    }

    /**
     * Gets the course code of the course in which this publication was made.
     * @return string The course code
     */
    function get_course_id()
    {
        return $this->get_default_property(self :: PROPERTY_COURSE_ID);
    }

    /**
     * Gets the tool in which this publication was made.
     * @return string
     */
    function get_tool()
    {
        return $this->get_default_property(self :: PROPERTY_TOOL);
    }

    /**
     * Gets the parent_id of the learning object publication
     * @return int
     */
    function get_parent_id()
    {
        return $this->get_default_property(self :: PROPERTY_PARENT_ID);
    }

    /**
     * Gets the id of the learning object publication category in which this
     * publication was made
     * @return int
     */
    function get_category_id()
    {
        return $this->get_default_property(self :: PROPERTY_CATEGORY_ID);
    }

    /**
     * Gets the list of target users of this publication
     * @return array An array of user ids.
     * @see is_for_everybody()
     */
    function get_target_users()
    {
        if (! isset($this->target_users))
        {
            $wdm = WeblcmsDataManager :: get_instance();
            $this->target_users = $wdm->retrieve_content_object_publication_target_users($this);
        }

        return $this->target_users;
    }

    /**
     * Gets the list of target course_groups of this publication
     * @return array An array of course_group ids.
     * @see is_for_everybody()
     */
    function get_target_course_groups()
    {
        if (! isset($this->target_course_groups))
        {
            $wdm = WeblcmsDataManager :: get_instance();
            $this->target_course_groups = $wdm->retrieve_content_object_publication_target_course_groups($this);
        }

        return $this->target_course_groups;
    }

    /**
     * Gets the date on which this publication becomes available
     * @return int
     * @see is_forever()
     */
    function get_from_date()
    {
        return $this->get_default_property(self :: PROPERTY_FROM_DATE);
    }

    /**
     * Gets the date on which this publication becomes unavailable
     * @return int
     * @see is_forever()
     */
    function get_to_date()
    {
        return $this->get_default_property(self :: PROPERTY_TO_DATE);
    }

    /**
     * Gets the user id of the user who made this publication
     * @return int
     */
    function get_publisher_id()
    {
        return $this->get_default_property(self :: PROPERTY_PUBLISHER_ID);
    }

    function get_content_object()
    {
        if (! isset($this->content_object))
        {
            $rdm = RepositoryDataManager :: get_instance();
            $this->content_object = $rdm->retrieve_content_object($this->get_content_object_id());
        }

        return $this->content_object;
    }

    function set_content_object($content_object)
    {
        $this->content_object = $content_object;
    }

    function get_publication_publisher()
    {
        if (! isset($this->publisher))
        {
            $udm = UserDataManager :: get_instance();
            $this->publisher = $udm->retrieve_user($this->get_publisher_id());
        }

        return $this->publisher;
    }

    /**
     * Gets the date on which this publication was made
     * @return int
     */
    function get_publication_date()
    {
        return $this->get_default_property(self :: PROPERTY_PUBLICATION_DATE);
    }

    /**
     * Gets the date on which this publication was made
     * @return int
     */
    function get_modified_date()
    {
        return $this->get_default_property(self :: PROPERTY_MODIFIED_DATE);
    }

    /**
     * Determines whether this publication was sent by email to the users and
     * course_groups for which this publication was made
     * @return boolean True if an email was sent
     */
    function is_email_sent()
    {
        return $this->get_default_property(self :: PROPERTY_EMAIL_SENT);
    }

    /**
     * Determines whether this publication is hidden or not
     * @return boolean True if the publication is hidden.
     */
    function is_hidden()
    {
        return $this->get_default_property(self :: PROPERTY_HIDDEN);
    }

    /**
     * Determines whether this publication is available forever
     * @return boolean True if the publication is available forever
     * @see get_from_date()
     * @see get_to_date()
     */
    function is_forever()
    {
        return $this->get_from_date() == 0 && $this->get_to_date() == 0;
    }

    function is_for_everybody()
    {
        return (count($this->get_target_users()) == 0 && count($this->get_target_course_groups()) == 0);
    }

    function is_visible_for_target_users()
    {
        return (! $this->is_hidden()) && ($this->is_forever() || ($this->get_from_date() <= time() && time() <= $this->get_to_date()));
    }

    function get_display_order_index()
    {
        return $this->get_default_property(self :: PROPERTY_DISPLAY_ORDER_INDEX);
    }

    function set_content_object_id($content_object_id)
    {
        $this->set_default_property(self :: PROPERTY_CONTENT_OBJECT_ID, $content_object_id);
    }

    function set_course_id($course)
    {
        $this->set_default_property(self :: PROPERTY_COURSE_ID, $course);
    }

    function set_tool($tool)
    {
        $this->set_default_property(self :: PROPERTY_TOOL, $tool);
    }

    function set_parent_id($parent_id)
    {
        $this->set_default_property(self :: PROPERTY_PARENT_ID, $parent_id);
    }

    function set_category_id($category)
    {
        $this->set_default_property(self :: PROPERTY_CATEGORY_ID, $category);
    }

    function set_target_users($target_users)
    {
        $this->target_users = $target_users;
    }

    function set_target_course_groups($target_course_groups)
    {
        $this->target_course_groups = $target_course_groups;
    }

    function set_from_date($from_date)
    {
        $this->set_default_property(self :: PROPERTY_FROM_DATE, $from_date);
    }

    function set_to_date($to_date)
    {
        $this->set_default_property(self :: PROPERTY_TO_DATE, $to_date);
    }

    function set_publisher_id($publisher)
    {
        $this->set_default_property(self :: PROPERTY_PUBLISHER_ID, $publisher);
    }

    function set_publication_date($publication_date)
    {
        $this->set_default_property(self :: PROPERTY_PUBLICATION_DATE, $publication_date);
    }

    function set_modified_date($modified_date)
    {
        $this->set_default_property(self :: PROPERTY_MODIFIED_DATE, $modified_date);
    }

    function set_hidden($hidden)
    {
        $this->set_default_property(self :: PROPERTY_HIDDEN, $hidden);
    }

    function set_display_order_index($display_order)
    {
        $this->set_default_property(self :: PROPERTY_DISPLAY_ORDER_INDEX, $display_order);
    }

    function set_email_sent($email_sent)
    {
        $this->set_default_property(self :: PROPERTY_EMAIL_SENT, $email_sent);
    }

    /**#@-*/
    /**
     * Toggles the visibility of this publication.
     */
    function toggle_visibility()
    {
        $this->set_hidden(! $this->is_hidden());
    }

    function get_show_on_homepage()
    {
        return $this->get_default_property(self :: PROPERTY_SHOW_ON_HOMEPAGE);
    }

    function set_show_on_homepage($show_on_homepage)
    {
        $this->set_default_property(self :: PROPERTY_SHOW_ON_HOMEPAGE, $show_on_homepage);
    }

    /**
     * Creates this publication in persistent storage
     * @see WeblcmsDataManager::create_content_object_publication()
     */
    function create()
    {
        $dm = WeblcmsDataManager :: get_instance();

        if (is_null($this->get_category_id()))
        {
            $this->set_category_id(0);
        }

        return $dm->create_content_object_publication($this);
    }

    /**
     * Moves the publication up or down in the list.
     * @param $places The number of places to move the publication down. A
     *                negative number moves it up.
     * @return int The number of places that the publication was moved
     *             down.
     */
    function move($places)
    {
        return WeblcmsDataManager :: get_instance()->move_content_object_publication($this, $places);
    }

    function retrieve_feedback()
    {
        return WeblcmsDataManager :: get_instance()->retrieve_content_object_publication_feedback($this->get_id());
    }

    static function get_table_name()
    {
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }
}
?>