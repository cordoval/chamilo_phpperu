<?php
/**
 * $Id: profiler_manager.class.php 212 2009-11-13 13:38:35Z chellee $
 * @package application.profiler.profiler_manager
 */
require_once dirname(__FILE__) . '/profiler_manager_component.class.php';
require_once dirname(__FILE__) . '/profiler_search_form.class.php';
require_once dirname(__FILE__) . '/../profiler_data_manager.class.php';
require_once dirname(__FILE__) . '/component/profile_publication_browser/profile_publication_browser_table.class.php';
//require_once dirname(__FILE__).'/../profile_publisher.class.php';
require_once dirname(__FILE__) . '/../profiler_menu.class.php';
require_once dirname(__FILE__) . '/../profiler_block.class.php';

/**
 * A profiler manager provides some functionalities to the admin to manage
 * his users. For each functionality a component is available.
 */
class ProfilerManager extends WebApplication
{
    const APPLICATION_NAME = 'profiler';
    
    const PARAM_DELETE_SELECTED = 'delete_selected';
    const PARAM_MARK_SELECTED_READ = 'mark_selected_read';
    const PARAM_MARK_SELECTED_UNREAD = 'mark_selected_unread';
    const PARAM_FIRSTLETTER = 'firstletter';
    const PARAM_PROFILE_ID = 'profile';
    
    const ACTION_DELETE_PUBLICATION = 'delete';
    const ACTION_EDIT_PUBLICATION = 'edit';
    const ACTION_VIEW_PUBLICATION = 'view';
    const ACTION_CREATE_PUBLICATION = 'create';
    const ACTION_BROWSE_PROFILES = 'browse';
    const ACTION_MANAGE_CATEGORIES = 'manage_categories';
    
    private $parameters;
    private $search_parameters;
    private $user;
    private $search_form;
    private $breadcrumbs;
    private $firstletter;

    /**
     * Constructor
     * @param User $user The current user
     */
    function ProfilerManager($user = null)
    {
        parent :: __construct($user);
        $this->parse_input_from_table();
        
        if (Request :: get(ProfilerManager :: PARAM_FIRSTLETTER))
        {
            $this->firstletter = Request :: get(ProfilerManager :: PARAM_FIRSTLETTER);
        }
    }

    /**
     * Run this profiler manager
     */
    function run()
    {
        /*
		 * Only setting breadcrumbs here. Some stuff still calls
		 * forceCurrentUrl(), but that should not affect the breadcrumbs.
		 */
        //$this->breadcrumbs = $this->get_category_menu()->get_breadcrumbs();
        $action = $this->get_action();
        $component = null;
        switch ($action)
        {
            case self :: ACTION_BROWSE_PROFILES :
                $component = ProfilerManagerComponent :: factory('Browser', $this);
                break;
            case self :: ACTION_VIEW_PUBLICATION :
                $component = ProfilerManagerComponent :: factory('Viewer', $this);
                break;
            case self :: ACTION_DELETE_PUBLICATION :
                $component = ProfilerManagerComponent :: factory('Deleter', $this);
                break;
            case self :: ACTION_EDIT_PUBLICATION :
                $component = ProfilerManagerComponent :: factory('Editor', $this);
                break;
            case self :: ACTION_CREATE_PUBLICATION :
                $component = ProfilerManagerComponent :: factory('Publisher', $this);
                break;
            case self :: ACTION_MANAGE_CATEGORIES :
                $component = ProfilerManagerComponent :: factory('CategoryManager', $this);
                break;
            default :
                $this->set_action(self :: ACTION_BROWSE_PROFILES);
                $component = ProfilerManagerComponent :: factory('Browser', $this);
        }
        $component->run();
    }

    /**
     * Renders the profiler block and returns it.
     */
    function render_block($block)
    {
        $block = ProfilerBlock :: factory($this, $block);
        return $block->run();
    }

    /*
	 * Displays the menu html
	 *
	function get_menu_html()
	{
		$extra_items = array ();
		$create = array ();
		$create['title'] = Translation :: get('Publish');
		$create['url'] = $this->get_profile_creation_url();
		$create['class'] = 'create';
		$extra_items[] = $create;

		if ($this->get_search_validate())
		{
			// $search_url = $this->get_url();
			$search_url = '#';
			$search = array ();
			$search['title'] = Translation :: get('SearchResults');
			$search['url'] = $search_url;
			$search['class'] = 'search_results';
			$extra_items[] = $search;
		}
		else
		{
			$search_url = null;
		}

		$temp_replacement = '__FIRSTLETTER__';
		$url_format = $this->get_url(array (Application :: PARAM_ACTION => ProfilerManager :: ACTION_BROWSE_PROFILES, ProfilerManager :: PARAM_FIRSTLETTER => $temp_replacement));
		$url_format = str_replace($temp_replacement, '%s', $url_format);
		$user_menu = new ProfilerMenu($this->firstletter, $url_format, $extra_items);

		if ($this->get_action() == self :: ACTION_CREATE_PUBLICATION)
		{
			$user_menu->forceCurrentUrl($create['url'], true);
		}
		elseif(!isset($this->firstletter))
		{
			$user_menu->forceCurrentUrl($this->get_profile_home_url(), true);
		}

		if (isset ($search_url))
		{
			$user_menu->forceCurrentUrl($search_url, true);
		}

		$html = array();
		$html[] = '<div style="float: left; width: 20%;">';
		$html[] = $user_menu->render_as_tree();
		$html[] = '</div>';

		return implode($html, "\n");
	}*/
    
    /**
     * Displays the search form
     */
    private function display_search_form()
    {
        echo $this->get_search_form()->display();
    }

    /**
     * Displays the footer.
     */
    function display_footer()
    {
        echo '</div>';
        echo '<div class="clear">&nbsp;</div>';
        echo '</div>';
        echo '<div class="clear">&nbsp;</div>';
        Display :: footer();
    }

    /**
     * Gets the parameter list
     * @param boolean $include_search Include the search parameters in the
     * returned list?
     * @return array The list of parameters.
     */
    function get_parameters($include_search = false)
    {
        if ($include_search && isset($this->search_parameters))
        {
            return array_merge($this->search_parameters, parent :: get_parameters());
        }
        
        return parent :: get_parameters();
    }

    /**
     * Sets the active URL in the navigation menu.
     * @param string $url The active URL.
     */
    function force_menu_url($url)
    {
        //$this->get_category_menu()->forceCurrentUrl($url);
    }

    /**
     * Returns whether a given object id is published in this application
     * @param int $object_id
     * @return boolean Is the object is published
     */
    function content_object_is_published($object_id)
    {
        return ProfilerDataManager :: get_instance()->content_object_is_published($object_id);
    }

    /**
     * Returns whether a given array of objects has been published
     * @param array $object_ids An array of object id's
     * @return boolean Was any learning object published
     */
    function any_content_object_is_published($object_ids)
    {
        return ProfilerDataManager :: get_instance()->any_content_object_is_published($object_ids);
    }

    /**
     * Gets the publication attributes of a given array of learning object id's
     * @param array $object_id The array of object ids
     * @param string $type Type of retrieval
     * @param int $offset
     * @param int $count
     * @param int $order_property
     * @return array An array of Learing Object Publication Attributes
     */
    function get_content_object_publication_attributes($object_id, $type = null, $offset = null, $count = null, $order_property = null)
    {
        return ProfilerDataManager :: get_instance()->get_content_object_publication_attributes($this->get_user(), $object_id, $type, $offset, $count, $order_property);
    }

    /**
     * Gets the publication attributes of a given learning object id
     * @param int $object_id The object id
     * @return ContentObjectPublicationAttribute
     */
    function get_content_object_publication_attribute($object_id)
    {
        return ProfilerDataManager :: get_instance()->get_content_object_publication_attribute($object_id);
    }

    /**
     * Counts the publication attributes
     * @param string $type Type of retrieval
     * @param Condition $conditions
     * @return int
     */
    function count_publication_attributes($type = null, $condition = null)
    {
        return ProfilerDataManager :: get_instance()->count_publication_attributes($this->get_user(), $type, $condition);
    }

    /**
     * Counts the publication attributes
     * @param string $type Type of retrieval
     * @param Condition $conditions
     * @return boolean
     */
    function delete_content_object_publications($object_id)
    {
        return ProfilerDataManager :: get_instance()->delete_profile_publications($object_id);
    }
    
	function delete_content_object_publication($publication_id)
    {
    	 return ProfilerDataManager :: get_instance()->delete_content_object_publication($publication_id);
    }

    /**
     * Update the publication id
     * @param ContentObjectPublicationAttribure $publication_attr
     * @return boolean
     */
    function update_content_object_publication_id($publication_attr)
    {
        return ProfilerDataManager :: get_instance()->update_profile_publication_id($publication_attr);
    }

    /**
     * Count the publications
     * @param Condition $condition
     * @return int
     */
    function count_profile_publications($condition = null)
    {
        $pmdm = ProfilerDataManager :: get_instance();
        return $pmdm->count_profile_publications($condition);
    }

    /**
     * Count the unread publications
     * @return int
     */
    function count_unread_profile_publications()
    {
        $pmdm = ProfilerDataManager :: get_instance();
        return $pmdm->count_unread_profile_publications($this->user);
    }

    /**
     * Retrieve a profile publication
     * @param int $id
     * @return PersonalMessagePublication
     */
    function retrieve_profile_publication($id)
    {
        $pmdm = ProfilerDataManager :: get_instance();
        return $pmdm->retrieve_profile_publication($id);
    }

    /**
     * Retrieve a series of profile publications
     * @param Condition $condition
     * @param array $order_by
     * @param int $offset
     * @param int $max_objects
     * @return PersonalMessagePublicationResultSet
     */
    function retrieve_profile_publications($condition = null, $order_by = array (), $offset = 0, $max_objects = -1)
    {
        $pmdm = ProfilerDataManager :: get_instance();
        return $pmdm->retrieve_profile_publications($condition, $order_by, $offset, $max_objects);
    }

    /**
     * Inherited
     */
    function get_content_object_publication_locations($content_object)
    {
        $allowed_types = array('profile');
        
        $type = $content_object->get_type();
        if (in_array($type, $allowed_types))
        {
            $locations = array(__CLASS__);
            return $locations;
        }
        
        return array();
    }

    function publish_content_object($content_object, $location)
    {
        $publication = new ProfilePublication();
        $publication->set_profile($content_object->get_id());
        $publication->set_publisher(Session :: get_user_id());
        $publication->set_published(time());
        $publication->set_category(0);
        $publication->create();
        return Translation :: get('PublicationCreated');
    }

    /**
     * Gets the url for deleting a profile publication
     * @param PersonalMessagePublication
     * @return string The url
     */
    function get_publication_deleting_url($profile)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_DELETE_PUBLICATION, self :: PARAM_PROFILE_ID => $profile->get_id()));
    }

    /**
     * Gets the url for editing a profile publication
     * @param PersonalMessagePublication
     * @return string The url
     */
    function get_publication_editing_url($profile)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_EDIT_PUBLICATION, self :: PARAM_PROFILE_ID => $profile->get_id()));
    }

    /**
     * Gets the url for viewing a profile publication
     * @param ProfilePublication
     * @return string The url
     */
    function get_publication_viewing_url($profile)
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_VIEW_PUBLICATION, self :: PARAM_PROFILE_ID => $profile->get_id()));
    }

    /**
     * Gets the url for replying to a profile publication
     * @param ProfilePublication
     * @return string The url
     */
    function get_publication_reply_url($profile)
    {
        return $this->get_url(array(Application :: PARAM_ACTION => ProfilerManager :: ACTION_CREATE_PUBLICATION, ProfilePublisher :: PARAM_ACTION => 'publicationcreator', ProfilePublisher :: PARAM_CONTENT_OBJECT_ID => $profile->get_profile(), self :: PARAM_PROFILE_ID => $profile->get_id(), ProfilePublisher :: PARAM_EDIT => 1));
    }

    /**
     * Gets the url for creating a profile publication
     * @param ProfilePublication
     * @return string The url
     */
    function get_profile_creation_url()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CREATE_PUBLICATION));
    }

    /**
     * Gets the HOME URL for a profile publication
     * @param ProfilePublication
     * @return string The url
     */
    function get_profile_home_url()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE_PROFILES));
    }

    function get_profiler_category_manager_url()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_MANAGE_CATEGORIES));
    }

    /**
     * Gets the search condition for a profile publication
     */
    function get_search_condition()
    {
        return $this->get_search_form()->get_condition();
    }

    /**
     * Gets the search form for a profile publication
     */
    private function get_search_form()
    {
        if (! isset($this->search_form))
        {
            $this->search_form = new ProfilerSearchForm($this, $this->get_url());
        }
        return $this->search_form;
    }

    /**
     * Gets the search form's validate
     */
    function get_search_validate()
    {
        return $this->get_search_form()->validate();
    }

    /**
     * Parse the input from the sortable tables and process input accordingly
     */
    private function parse_input_from_table()
    {
        if (isset($_POST['action']))
        {
            $selected_ids = $_POST[ProfilePublicationBrowserTable :: DEFAULT_NAME . ObjectTable :: CHECKBOX_NAME_SUFFIX];
            if (empty($selected_ids))
            {
                $selected_ids = array();
            }
            elseif (! is_array($selected_ids))
            {
                $selected_ids = array($selected_ids);
            }
            switch ($_POST['action'])
            {
                case self :: PARAM_MARK_SELECTED_READ :
                    $this->set_action(self :: ACTION_MARK_PUBLICATION);
                    Request :: set_get(self :: PARAM_PROFILE_ID, $selected_ids);
                    Request :: set_get(self :: PARAM_MARK_TYPE, self :: PARAM_MARK_SELECTED_READ);
                    break;
                case self :: PARAM_MARK_SELECTED_UNREAD :
                    $this->set_action(self :: ACTION_MARK_PUBLICATION);
                    Request :: set_get(self :: PARAM_PROFILE_ID, $selected_ids);
                    Request :: set_get(self :: PARAM_MARK_TYPE, self :: PARAM_MARK_SELECTED_UNREAD);
                    break;
                case self :: PARAM_DELETE_SELECTED :
                    $this->set_action(self :: ACTION_DELETE_PUBLICATION);
                    Request :: set_get(self :: PARAM_PROFILE_ID, $selected_ids);
                    break;
            }
        }
    }

    /**
     * Helper function for the Application class,
     * pending access to class constants via variables in PHP 5.3
     * e.g. $name = $class :: APPLICATION_NAME
     *
     * DO NOT USE IN THIS APPLICATION'S CONTEXT
     * Instead use:
     * - self :: APPLICATION_NAME in the context of this class
     * - YourApplicationManager :: APPLICATION_NAME in all other application classes
     */
    function get_application_name()
    {
        return self :: APPLICATION_NAME;
    }
}
?>