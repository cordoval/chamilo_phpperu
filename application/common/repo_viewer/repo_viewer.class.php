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
    const PARAM_CONTENT_OBJECT_TYPE = 'type';

    const PARAM_PUBLISH_SELECTED = 'repoviewer_selected';

    const ACTION_CREATOR = 'creator';
    const ACTION_BROWSER = 'browser';
    const ACTION_PUBLISHER = 'publisher';
    const ACTION_VIEWER = 'viewer';

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
    function RepoViewer($parent, $types, $maximum_select = self :: SELECT_MULTIPLE, $excluded_objects = array())
    {
        parent :: __construct($parent);
        //        $this->handle_table_action();
        $this->maximum_select = $maximum_select;
        $this->parent = $parent;
        $this->default_content_objects = array();
        $this->parameters = array();
        $this->types = (is_array($types) ? $types : array($types));
        $this->set_repo_viewer_actions(array(self :: ACTION_CREATOR, self :: ACTION_BROWSER));
        $this->excluded_objects = $excluded_objects;
        $this->set_parameter(RepoViewer :: PARAM_ACTION, (Request :: get(RepoViewer :: PARAM_ACTION) ? Request :: get(RepoViewer :: PARAM_ACTION) : self :: ACTION_CREATOR));
        //        $this->parse_input_from_table();
    }

    function run()
    {
        $action = $this->get_parameter(self :: PARAM_ACTION);

        switch ($action)
        {
            case self :: ACTION_CREATOR :
                $component = $this->create_component('Creator');
                break;
            case self :: ACTION_BROWSER :
                $component = $this->create_component('Browser');
                break;
            case self :: ACTION_PUBLISHER :
                $component = $this->create_component('Publisher');
                break;
            case self :: ACTION_VIEWER :
                $component = $this->create_component('Viewer');
                break;
            default :
                $component = $this->create_component('Creator');
                $this->set_parameter(self :: PARAM_ACTION, self :: ACTION_CREATOR);
                break;
        }

        $component->run();
    }

    function create_component($type, $application)
    {
        $component = parent :: create_component($type, $application);

        if (is_subclass_of($component, __CLASS__))
        {
            $component->set_types($this->get_types());
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

        echo implode("\n", $html);
    }

    function display_footer()
    {
        echo '</div></div>';
        parent :: display_footer();

        return implode("\n", $html);
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
        return $this->types;
    }

    /**
     * Set the type(s) of content object this RepoViewer uses.
     * @param $types
     */
    function set_types($types)
    {
        if (! is_array($types))
        {
            $types = array($types);
        }

        $this->types = $types;
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

    function get_selected_objects()
    {
        return Request :: get(self :: PARAM_ID);
    }

    function is_ready_to_be_published()
    {
        $action = $this->get_parameter(RepoViewer :: PARAM_ACTION);
        return self :: any_object_selected() && ($action == self :: ACTION_PUBLISHER);
    }

    function get_application_component_path()
    {
        return Path :: get_application_library_path() . 'repo_viewer/component/';
    }
}
?>