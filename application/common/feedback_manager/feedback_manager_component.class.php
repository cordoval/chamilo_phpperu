<?php
/**
 * $Id: feedback_manager_component.class.php 191 2009-11-13 11:50:28Z chellee $
 * @package application.common.feedback_manager
 */

/**
 * Description of feedback_manager_componentclass
 *
 * @author pieter
 */
class FeedbackManagerComponent
{
    /**
     * The ObjectPublisher instance that created this object.
     */
    private $parent;

    /**
     * Constructor.
     * @param ObjectPublisher $parent The creator of this object.
     */
    function FeedbackManagerComponent($parent)
    {
        $this->parent = $parent;
    }

    /**
     * Returns the creator of this object.
     * @return ObjectPublisher The creator.
     */
    protected function get_parent()
    {
        return $this->parent;
    }

    /**
     * @see ObjectPublisher::get_user_id()
     */
    function get_user_id()
    {
        return $this->parent->get_user_id();
    }

    function get_user()
    {
        return $this->parent->get_user();
    }

    function display_header($breadcrumbtrail)
    {
        return $this->parent->display_header($breadcrumbtrail);
    }

    function display_footer()
    {
        return $this->parent->display_footer();
    }

    /**
     * @see ObjectPublisher::get_url()
     */
    function get_url($parameters = array(), $encode = false)
    {
        
        return $this->parent->get_url($parameters, $encode);
    }

    /**
     * @see ObjectPublisher::get_parameters()
     */
    function get_parameters()
    {
        return $this->parent->get_parameters();
    }

    /**
     * @see ObjectPublisher::get_parameter()
     */
    function get_parameter($name)
    {
        $parameters = $this->get_parameters();
        return $parameters[$name];
    }

    /**
     * @see ObjectPublisher::set_parameter()
     */
    function set_parameter($name, $value)
    {
        $this->parent->set_parameter($name, $value);
    }

    function set_default_content_object($type, $content_object)
    {
        $this->parent->set_default_content_object($type, $content_object);
    }

    /**
     * @see ObjectPublisher::get_default_object()
     */
    function get_default_content_object($type)
    {
        return $this->parent->get_default_content_object($type);
    }

    function redirect($action = null, $message = null, $error_message = false, $extra_params = array())
    {
        return $this->parent->redirect($action, $message, $error_message, $extra_params);
    }

    function repository_redirect($action = null, $message = null, $cat_id = 0, $error_message = false, $extra_params = array())
    {
        return $this->parent->repository_redirect($action, $message, $cat_id, $error_message, $extra_params);
    }

    function get_extra_parameters()
    {
        return $this->parent->get_extra_parameters();
    }

    function set_extra_parameters($parameters)
    {
        $this->parent->set_extra_parameters($parameters);
    }

    function retrieve_feedback_publications($pid, $cid, $application)
    {
        return $this->parent->retrieve_feedback_publications($pid, $cid, $application);
    
    }

    function retrieve_feedback_publication($id)
    {
        return $this->parent->retrieve_feedback_publication($id);
    }

    function get_feedback()
    {
        return $this->get_parent()->get_feedback();
    }

    function add_actionbar_item($link)
    {
        $this->parent->add_to_action_bar($link);
    }

    static function factory($type, $parent)
    {
        
        $filename = dirname(__FILE__) . '/component/' . Utilities :: camelcase_to_underscores($type) . '.class.php';
        if (! file_exists($filename) || ! is_file($filename))
        {
            die('Failed to load "' . $type . '" component');
        }
        $class = 'FeedbackManager' . $type . 'Component';
        require_once $filename;
        
        return new $class($parent);
    }
}
?>