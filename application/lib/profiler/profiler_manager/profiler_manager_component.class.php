<?php
/**
 * $Id: profiler_manager_component.class.php 212 2009-11-13 13:38:35Z chellee $
 * @package application.profiler.profiler_manager
 */

abstract class ProfilerManagerComponent extends WebApplicationComponent
{

    /**
     * Constructor
     * @param ProfileManager $pm The profile manager which
     * provides this component
     */
    protected function ProfilerManagerComponent($pm)
    {
        parent :: __construct($pm);
    }

    /**
     * @see ProfileManager :: count_profile_publications
     */
    function count_profile_publications($condition = null)
    {
        return $this->get_parent()->count_profile_publications($condition);
    }

    /**
     * @see ProfileManager :: retrieve_profile_publication()
     */
    function retrieve_profile_publication($id)
    {
        return $this->get_parent()->retrieve_profile_publication($id);
    }

    /**
     * @see ProfileManager :: retrieve_profile_publications()
     */
    function retrieve_profile_publications($condition = null, $order_by = array (), $offset = 0, $max_objects = -1)
    {
        return $this->get_parent()->retrieve_profile_publications($condition, $order_by, $offset, $max_objects);
    }

    /**
     * @see ProfileManager :: get_search_condition()
     */
    function get_search_condition()
    {
        return $this->get_parent()->get_search_condition();
    }

    /**
     * @see ProfileManager :: get_publication_deleting_url()
     */
    function get_publication_deleting_url($profile)
    {
        return $this->get_parent()->get_publication_deleting_url($profile);
    }

    /**
     * @see ProfileManager :: get_publication_editing_url()
     */
    function get_publication_editing_url($profile)
    {
        return $this->get_parent()->get_publication_editing_url($profile);
    }

    /**
     * @see ProfileManager :: get_publication_viewing_url()
     */
    function get_publication_viewing_url($profile)
    {
        return $this->get_parent()->get_publication_viewing_url($profile);
    }

    /**
     * @see ProfileManager :: get_profile_creation_url()
     */
    function get_profile_creation_url()
    {
        return $this->get_parent()->get_profile_creation_url();
    }

    /**
     * @see ProfileManager :: get_publication_reply_url()
     */
    function get_publication_reply_url($profile)
    {
        return $this->get_parent()->get_publication_reply_url($profile);
    }

    function get_profiler_category_manager_url()
    {
        return $this->get_parent()->get_profiler_category_manager_url();
    }
}
?>