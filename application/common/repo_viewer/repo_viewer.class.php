<?php
/**
 * $Id: repo_viewer.class.php 191 2009-11-13 11:50:28Z chellee $
 * @package application.common.repo_viewer.component
 */
require_once dirname(__FILE__) . '/component/content_object_table/content_object_table.class.php';
require_once dirname(__FILE__) . '/repo_viewer_component.class.php';

/**
==============================================================================
 *	This class provides the means to repoviewer a learning object.
 *
 *	@author Tim De Pauw
==============================================================================
 */

class RepoViewer
{
    const PARAM_ACTION = 'repoviewer_action';
    const PARAM_EDIT = 'edit';
    const PARAM_ID = 'object';
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

    /**
     * The default learning objects, which are used for form defaults.
     */
    private $default_content_objects;

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
    function RepoViewer($parent, $types, $maximum_select = self :: SELECT_MULTIPLE, $excluded_objects = array(), $parse_input = true)
    {
        $this->maximum_select = $maximum_select;
        $this->parent = $parent;
        $this->default_content_objects = array();
        $this->parameters = array();
        $this->types = (is_array($types) ? $types : array($types));
        $this->set_repo_viewer_actions(array(self :: ACTION_CREATOR, self :: ACTION_BROWSER));
        $this->excluded_objects = $excluded_objects;
        $this->set_parameter(RepoViewer :: PARAM_ACTION, (Request :: get(RepoViewer :: PARAM_ACTION) ? Request :: get(RepoViewer :: PARAM_ACTION) : self :: ACTION_CREATOR));
        if ($parse_input)
        {
            $this->parse_input_from_table();
        }
    }

    function as_html()
    {
        $action = $this->get_action();
        $html = array();

        $html[] = '<div class="tabbed-pane"><ul class="tabbed-pane-tabs">';
        $repo_viewer_actions = $this->get_repo_viewer_actions();
        
        if($action == self :: ACTION_VIEWER)
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
            
	        if($repo_viewer_action == self :: ACTION_VIEWER)
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

    function get_repo_viewer_component($action)
    {
        return RepoViewerComponent :: factory($action, $this);
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
     * Returns the tool which created this repo_viewer.
     * @return Tool The tool.
     */
    function get_parent()
    {
        return $this->parent;
    }

    /**
     * @see Tool::get_user_id()
     */
    function get_user_id()
    {
        return $this->get_parent()->get_user_id();
    }

    function get_user()
    {
        return $this->get_parent()->get_user();
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
        if (!is_array($types))
        {
            $types = array($types);
        }

        $this->types = $types;
    }

    /**
     * Returns the action that the user selected.
     * @return string The action.
     */
    function get_action()
    {
        return $this->get_parameter(RepoViewer :: PARAM_ACTION);
    }

    function set_action($action)
    {
        $this->set_parameter(RepoViewer :: PARAM_ACTION, $action);
    }

    function get_url($parameters = array(), $encode_entities = false, $filter = array())
    {
        $parameters = array_merge($this->parent->get_parameters(), $parameters);
        return Redirect :: get_url($parameters, $filter, $encode_entities);
        //return $this->parent->get_url($parameters, $encode);
    }

    function get_parameter($name)
    {
        return $this->parameters[$name];
    }

    function get_parameters()
    {
        return $this->parameters;
    }

    function set_parameters($parameters = array())
    {
        $this->parameters = $parameters;
    }

    function set_parameter($name, $value)
    {
        $this->parameters[$name] = $value;
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

    /**
     * Sets a default learning object. When the creator component of this
     * repo_viewer is displayed, the properties of the given learning object will
     * be used as the default form values.
     * @param string $type The learning object type.
     * @param ContentObject $content_object The learning object to use as the
     *                                        default for the given type.
     */
    function set_default_content_object($type, $content_object)
    {
        $this->default_content_objects[$type] = $content_object;
    }

    function get_default_content_object($type)
    {
        if (isset($this->default_content_objects[$type]))
        {
            return $this->default_content_objects[$type];
        }
        return new AbstractContentObject($type, $this->get_user_id());
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

    function parse_input_from_table()
    {
        if (isset($_POST['action']))
        {
            $selected_publication_ids = $_POST[ContentObjectTable :: DEFAULT_NAME . ObjectTable :: CHECKBOX_NAME_SUFFIX];

            if (! is_array($selected_publication_ids))
                $selected_publication_ids = array($selected_publication_ids);

            switch ($_POST['action'])
            {
                case self :: PARAM_PUBLISH_SELECTED :
                    if ($this->get_maximum_select() > 0)
                    {
                        if (count($selected_publication_ids) > $this->get_maximum_select())
                        {
                            Request :: set_get('message', sprintf(Translation :: get('MaximumSelectableContentObjectsReached'), count($selected_publication_ids), $this->get_maximum_select()));
                            $_POST['action'] = null;
                            Request :: set_get('action', null);
                            return;
                        }
                    }
                    $redirect_params = array_merge($this->get_parameters(), array(RepoViewer :: PARAM_ACTION => RepoViewer:: ACTION_PUBLISHER, RepoViewer :: PARAM_ID => $selected_publication_ids));

                    $this->redirect(null, false, $redirect_params);
                    break;
            }
        }
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
        $action = $this->get_action();

        return self :: any_object_selected() && ($action == self :: ACTION_PUBLISHER);
    }
}
?>