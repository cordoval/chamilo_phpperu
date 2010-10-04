<?php
/**
 * $Id: repo_viewer.class.php 191 2009-11-13 11:50:28Z chellee $
 * @package application.common.repo_viewer.component
 */
require_once dirname(__FILE__) . '/component/content_object_table/content_object_table.class.php';
//require_once dirname(__FILE__) . '/repo_viewer_component.class.php';


/**
==============================================================================
 * This class provides the means to repoviewer a learning object.
 *
 * @author Tim De Pauw
==============================================================================
 */

class RepoViewer extends SubManager
{
    const PARAM_ACTION = 'repoviewer_action';
    const PARAM_EDIT = 'edit';
    const PARAM_ID = 'repo_object';
    const PARAM_EDIT_ID = 'obj';
    const PARAM_QUERY = 'query';
    const PARAM_CONTENT_OBJECT_TYPE = 'content_object_type';

    const PARAM_PUBLISH_SELECTED = 'repoviewer_selected';

    const ACTION_CREATOR = 'creator';
    const ACTION_BROWSER = 'browser';
    const ACTION_PUBLISHER = 'publisher';
    const ACTION_VIEWER = 'viewer';

    const DEFAULT_ACTION = self :: ACTION_CREATOR;

    /**
     * The types of learning object that this repo_viewer is aware of and may
     * repoviewer.
     */
    private $types;

    private $parent;

    private $repo_viewer_actions;

    private $parameters;

    private $maximum_select;

    private $excluded_objects;

    /**
     * You have two choices for the select multiple
     * 0 / SELECT MULTIPLE - you can select as many lo as you want
     * A number > 0 - Max defined selected learning objects
     */
    const SELECT_MULTIPLE = 0;
    const SELECT_SINGLE = 1;

    /**
     * Constructor.
     * @param array $types The learning object types that may be repoviewered.
     */
    function RepoViewer($parent)
    {
        parent :: __construct($parent);
        $this->maximum_select = self :: SELECT_MULTIPLE;
        $this->parent = $parent;
        $this->default_content_objects = array();
        $this->parameters = array();
        $this->set_repo_viewer_actions(array(self :: ACTION_CREATOR, self :: ACTION_BROWSER));
        $this->excluded_objects = array();
        $this->set_parameter(RepoViewer :: PARAM_ACTION, (Request :: get(RepoViewer :: PARAM_ACTION) ? Request :: get(RepoViewer :: PARAM_ACTION) : self :: ACTION_CREATOR));
    }

    function create_component($type, $application)
    {
        $component = parent :: create_component($type, $application);

        if (is_subclass_of($component, __CLASS__))
        {
            $component->set_excluded_objects($this->get_excluded_objects());
            $component->set_maximum_select($this->get_maximum_select());
        }

        return $component;
    }

    function display_header()
    {
        parent :: display_header();

        $action = $this->get_parameter(RepoViewer :: PARAM_ACTION);
        $html = array();

        $repo_viewer_actions = $this->get_repo_viewer_actions();

        if ($action == self :: ACTION_VIEWER)
        {
            $repo_viewer_actions[] = self :: ACTION_VIEWER;
        }

        $tabs = new DynamicVisualTabsRenderer('repo_viewer');

        foreach ($repo_viewer_actions as $repo_viewer_action)
        {
            if ($action == $repo_viewer_action)
            {
                $selected = true;
            }
            elseif (($action == self :: ACTION_PUBLISHER || $action == 'multirepo_viewer') && $repo_viewer_action == self :: ACTION_CREATOR)
            {
                $selected = true;
            }
            else
            {
                $selected = false;
            }

            $parameters = $this->get_parameters();
            $parameters[self :: PARAM_ACTION] = $repo_viewer_action;

            if ($repo_viewer_action == self :: ACTION_VIEWER)
            {
                $parameters[self :: PARAM_ID] = Request :: get(self :: PARAM_ID);
            }

            $label = htmlentities(Translation :: get(Utilities :: underscores_to_camelcase($repo_viewer_action) . 'Title'));
            $link = $this->get_url($parameters);
            $tabs->add_tab(new DynamicVisualTab($repo_viewer_action, $label, Theme :: get_common_image_path() . 'place_repository_' . $repo_viewer_action . '.png', $link, $selected));
        }

        $html[] = $tabs->header();
        $html[] = DynamicVisualTabsRenderer :: body_header();

        echo implode("\n", $html);
    }

    function display_footer()
    {
        $html = array();
        $html[] = DynamicVisualTabsRenderer :: body_footer();
        $html[] = DynamicVisualTabsRenderer :: footer();
        echo implode("\n", $html);

        parent :: display_footer();
    }

    function as_html()
    {
        $action = $this->get_parameter(RepoViewer :: PARAM_ACTION);
        $html = array();

        $html[] = '<div class="tabbed-pane"><ul class="tabbed-pane-tabs">';
        $repo_viewer_actions = $this->get_repo_viewer_actions();

        if ($action == self :: ACTION_VIEWER)
        {
            $repo_viewer_actions[] = self :: ACTION_VIEWER;
        }

        foreach ($repo_viewer_actions as $repo_viewer_action)
        {
            $html[] = '<li><a';
            if ($action == $repo_viewer_action)
            {
                $html[] = ' class="current"';
            }
            elseif (($action == self :: ACTION_PUBLISHER || $action == 'multirepo_viewer') && $repo_viewer_action == self :: ACTION_CREATOR)
            {
                $html[] = ' class="current"';
            }

            $parameters = $this->get_parameters();
            $parameters[self :: PARAM_ACTION] = $repo_viewer_action;

            if ($repo_viewer_action == self :: ACTION_VIEWER)
            {
                $parameters[self :: PARAM_ID] = Request :: get(self :: PARAM_ID);
            }

            $html[] = ' href="' . $this->get_url($parameters, true) . '">' . htmlentities(Translation :: get(ucfirst($repo_viewer_action) . 'Title')) . '</a></li>';
        }
        $html[] = '</ul><div class="tabbed-pane-content">';

        $html[] = $this->get_repo_viewer_component($action)->as_html();
        $html[] = '</div></div>';

        return implode("\n", $html);
    }

    function set_maximum_select($maximum_select)
    {
        $this->maximum_select = $maximum_select;
    }

    function get_maximum_select()
    {
        return $this->maximum_select;
    }

    /**
     * Returns the types of content object that RepoViewer uses.
     * @return array The types.
     */
    function get_types()
    {
        return $this->get_parent()->get_allowed_content_object_types();
    }

    private $creation_defaults;

    function set_creation_defaults($defaults)
    {
        $this->creation_defaults = $defaults;
    }

    function get_creation_defaults()
    {
        return $this->creation_defaults;
    }

    function redirect($message = null, $error_message = false, $parameters = array(), $filter = array(), $encode_entities = false)
    {
        if (! $error_message)
        {
            $parameters[Application :: PARAM_MESSAGE] = $message;
        }
        else
        {
            $parameters[Application :: PARAM_ERROR_MESSAGE] = $message;
        }

        $parameters = array_merge($this->get_parent()->get_parameters(), $parameters);
        Redirect :: url($parameters, $filter, $encode_entities);
    }

    function get_repo_viewer_actions()
    {
        return $this->repo_viewer_actions;
    }

    function set_repo_viewer_actions($repo_viewer_actions)
    {
        $this->repo_viewer_actions = $repo_viewer_actions;
    }

    function get_excluded_objects()
    {
        return $this->excluded_objects;
    }

    function set_excluded_objects($excluded_objects)
    {
        $this->excluded_objects = $excluded_objects;
    }

    function any_object_selected()
    {
        $object = Request :: get(self :: PARAM_ID);
        return isset($object);
    }

    static function get_selected_objects()
    {
        return Request :: get(self :: PARAM_ID);
    }

    static function is_ready_to_be_published()
    {
        $action = Request :: get(RepoViewer :: PARAM_ACTION);
        $table_name = Request :: post('table_name');
        if($table_name)
        {
        	ContentObjectTable::handle_table_action();
	        $table_action = Request :: post($table_name . '_action_value');
	        if($table_action)
	        {
	            $action = $table_action;
	        }
        }

        return (self :: any_object_selected() && $action == RepoViewer::ACTION_PUBLISHER);
    }

    function get_application_component_path()
    {
        return Path :: get_common_extensions_path() . 'repo_viewer/component/';
    }

    /**
     * Helper function for the SubManager class,
     * pending access to class constants via variables in PHP 5.3
     * e.g. $name = $class :: DEFAULT_ACTION
     *
     * DO NOT USE IN THIS SUBMANAGER'S CONTEXT
     * Instead use:
     * - self :: DEFAULT_ACTION in the context of this class
     * - YourSubManager :: DEFAULT_ACTION in all other application classes
     */
    static function get_default_action()
    {
        return self :: DEFAULT_ACTION;
    }

    /**
     * Helper function for the SubManager class,
     * pending access to class constants via variables in PHP 5.3
     * e.g. $name = $class :: PARAM_ACTION
     *
     * DO NOT USE IN THIS SUBMANAGER'S CONTEXT
     * Instead use:
     * - self :: PARAM_ACTION in the context of this class
     * - YourSubManager :: PARAM_ACTION in all other application classes
     */
    static function get_action_parameter()
    {
        return self :: PARAM_ACTION;
    }

    /**
     * @param Application $application
     * @return RepoViewer
     */
    static function construct($application)
    {
        return parent :: construct(__CLASS__, $application);
    }

    /**
     * @param Application $application
     */
    static function launch($application)
    {
        self :: construct(__CLASS__, $application)->run();
    }
}
?>