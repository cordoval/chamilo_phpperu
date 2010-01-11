<?php
/**
 * $Id: portfolio_publication.class.php 206 2009-11-13 13:08:01Z chellee $
 * @package application.portfolio.portfolio_manager.component
 */

require_once dirname(__FILE__) . '/portfolio_publication_user.class.php';
require_once dirname(__FILE__) . '/portfolio_publication_group.class.php';

/**
 * This class describes a PortfolioPublication data object
 *
 * @author Sven Vanpoucke
 */
class PortfolioPublication extends DataClass
{
    const CLASS_NAME = __CLASS__;

    /**
     * PortfolioPublication properties
     */
    const PROPERTY_CONTENT_OBJECT = 'content_object_id';
    const PROPERTY_FROM_DATE = 'from_date';
    const PROPERTY_TO_DATE = 'to_date';
    const PROPERTY_HIDDEN = 'hidden';
    const PROPERTY_PUBLISHER = 'publisher_id';
    const PROPERTY_PUBLISHED = 'published';

    private $target_groups;
    private $target_users;

    /**
     * Get the default properties
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_CONTENT_OBJECT, self :: PROPERTY_FROM_DATE, self :: PROPERTY_TO_DATE, self :: PROPERTY_HIDDEN, self :: PROPERTY_PUBLISHER, self :: PROPERTY_PUBLISHED));
    }

    /**
     * inherited
     */
    function get_data_manager()
    {
        return PortfolioDataManager :: get_instance();
    }

    /**
     * Returns the content_object of this PortfolioPublication.
     * @return the content_object.
     */
    function get_content_object()
    {
        return $this->get_default_property(self :: PROPERTY_CONTENT_OBJECT);
    }

    /**
     * Sets the content_object of this PortfolioPublication.
     * @param content_object
     */
    function set_content_object($content_object)
    {
        $this->set_default_property(self :: PROPERTY_CONTENT_OBJECT, $content_object);
    }

    /**
     * Returns the from_date of this PortfolioPublication.
     * @return the from_date.
     */
    function get_from_date()
    {
        return $this->get_default_property(self :: PROPERTY_FROM_DATE);
    }

    /**
     * Sets the from_date of this PortfolioPublication.
     * @param from_date
     */
    function set_from_date($from_date)
    {
        $this->set_default_property(self :: PROPERTY_FROM_DATE, $from_date);
    }

    /**
     * Returns the to_date of this PortfolioPublication.
     * @return the to_date.
     */
    function get_to_date()
    {
        return $this->get_default_property(self :: PROPERTY_TO_DATE);
    }

    /**
     * Sets the to_date of this PortfolioPublication.
     * @param to_date
     */
    function set_to_date($to_date)
    {
        $this->set_default_property(self :: PROPERTY_TO_DATE, $to_date);
    }

    /**
     * Returns the hidden of this PortfolioPublication.
     * @return the hidden.
     */
    function get_hidden()
    {
        return $this->get_default_property(self :: PROPERTY_HIDDEN);
    }

    /**
     * Sets the hidden of this PortfolioPublication.
     * @param hidden
     */
    function set_hidden($hidden)
    {
        $this->set_default_property(self :: PROPERTY_HIDDEN, $hidden);
    }

    /**
     * Returns the publisher of this PortfolioPublication.
     * @return the publisher.
     */
    function get_publisher()
    {
        return $this->get_default_property(self :: PROPERTY_PUBLISHER);
    }

    /**
     * Sets the publisher of this PortfolioPublication.
     * @param publisher
     */
    function set_publisher($publisher)
    {
        $this->set_default_property(self :: PROPERTY_PUBLISHER, $publisher);
    }

    /**
     * Returns the published of this PortfolioPublication.
     * @return the published.
     */
    function get_published()
    {
        return $this->get_default_property(self :: PROPERTY_PUBLISHED);
    }

    /**
     * Sets the published of this PortfolioPublication.
     * @param published
     */
    function set_published($published)
    {
        $this->set_default_property(self :: PROPERTY_PUBLISHED, $published);
    }

    function set_target_groups($target_groups)
    {
        $this->target_groups = $target_groups;
    }

    function set_target_users($target_users)
    {
        $this->target_users = $target_users;
    }

    function get_target_groups()
    {
        if (! $this->target_groups)
        {
            $condition = new EqualityCondition(PortfolioPublicationGroup :: PROPERTY_PORTFOLIO_PUBLICATION, $this->get_id());
            $groups = PortfolioDataManager :: get_instance()->retrieve_portfolio_publication_groups($condition);

            while ($group = $groups->next_result())
            {
                $this->target_groups[] = $group->get_group_id();
            }
        }

        return $this->target_groups;
    }

    function get_target_users()
    {
        if (! $this->target_users)
        {
            $condition = new EqualityCondition(PortfolioPublicationUser :: PROPERTY_PORTFOLIO_PUBLICATION, $this->get_id());
            $users = PortfolioDataManager :: get_instance()->retrieve_portfolio_publication_users($condition);

            while ($user = $users->next_result())
            {
                $this->target_users[] = $user->get_user();
            }
        }

        return $this->target_users;
    }

    function is_visible_for_target_user($user_id)
    {
        $user = UserDataManager :: get_instance()->retrieve_user($user_id);

        if ($user->is_platform_admin() || $user_id == $this->get_publisher())
            return true;

        if ($this->get_target_groups() || $this->get_target_users())
        {
            $allowed = false;

            if (in_array($user_id, $this->get_target_users()))
            {
                $allowed = true;
            }

            if (! $allowed)
            {
                $user_groups = $user->get_groups();

                while ($user_group = $user_groups->next_result())
                {
                    if (in_array($user_group->get_id(), $this->get_target_groups()))
                    {
                        $allowed = true;
                        break;
                    }
                }
            }

            if (! $allowed)
                return false;
        }

        if ($this->get_hidden())
            return false;

        $time = time();

        if ($time < $this->get_from_date() || $time > $this->get_to_date() && !$this->is_forever())
            return false;

        return true;
    }
    
    function is_forever()
    {
    	return ($this->get_from_date() == 0 && $this->get_to_date() == 0);
    }

    function create()
    {
        $dm = PortfolioDataManager :: get_instance();
        return $dm->create_portfolio_publication($this);
    }

    static function get_table_name()
    {
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }
}

?>