<?php
/**
 * $Id: content_object_repo_viewer.class.php 218 2009-11-13 14:21:26Z kariboe $
 * @package application.lib.weblcms
 */

/**
==============================================================================
 *	This class provides the means to publish a learning object.
 *
 *	@author Tim De Pauw
==============================================================================
 */

class ContentObjectRepoViewer extends RepoViewer
{

    /**
     * The default learning objects, which are used for form defaults.
     */
    
    function ContentObjectRepoViewer($parent, $types, $mail_option = false, $maximum_select = RepoViewer :: SELECT_MULTIPLE, $action = TOOL :: ACTION_PUBLISH)
    {
        parent :: __construct($parent, $types, $mail_option, $maximum_select, array(), false);
        if (is_array($action))
        {
            foreach ($action as $type => $action)
            {
                $this->set_parameter($type, $action);
            }
        }
        else
            $this->set_parameter(Tool :: PARAM_ACTION, $action);
        if (Request :: get(Tool :: PARAM_PUBLICATION_ID) != null)
            $this->set_parameter(Tool :: PARAM_PUBLICATION_ID, Request :: get(Tool :: PARAM_PUBLICATION_ID));
        $this->set_repo_viewer_actions(array('creator', 'browser'));
        $this->parse_input_from_table();
    }

    function redirect_complex($type)
    {
        /*switch ($type)
		{
			case 'forum_topic':
				return false;
			default: return true;
		}*/        return false;
    }

    /**
     * @see Tool::get_course()
     */
    function get_course()
    {
        return $this->get_parent()->get_course();
    }

    /**
     * @see Tool::get_course_id()
     */
    function get_course_id()
    {
        return $this->get_parent()->get_course_id();
    }

    /**
     * @see Tool::get_course()
     */
    function get_user()
    {
        return $this->get_parent()->get_user();
    }

    /**
     * @see Tool::get_categories()
     */
    function get_categories()
    {
        return $this->get_parent()->get_categories();
    }

    /**
     * @see Tool::get_tool()
     */
    function get_tool()
    {
        return $this->get_parent();
    }

}
?>