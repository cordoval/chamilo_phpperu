<?php
/**
 * $Id: repo_viewer_component.class.php 191 2009-11-13 11:50:28Z chellee $
 * @package application.common.repo_viewer
 */
/**
==============================================================================
 *	This class represents a component of a EncyclopediaRepoViewer. Its output
 *	is included in the publisher's output.
==============================================================================
 */
abstract class RepoViewerComponent
{
    /**
     * The ObjectRepoViewer instance that created this object.
     */
    private $parent;

    /**
     * Constructor.
     * @param ObjectRepoViewer $parent The creator of this object.
     */
    function RepoViewerComponent($parent)
    {
        $this->parent = $parent;
    }

    /**
     * Returns the creator of this object.
     * @return ObjectRepoViewer The creator.
     */
    function get_parent()
    {
        return $this->parent;
    }

    function get_maximum_select()
    {
        return $this->get_parent()->get_maximum_select();
    }

    function redirect_complex($type)
    {
        return $this->get_parent()->redirect_complex($type);
    }

    /**
     * @see ObjectRepoViewer::get_user_id()
     */
    protected function get_user_id()
    {
        return $this->parent->get_user_id();
    }

    function get_user()
    {
        return $this->parent->get_user();
    }

    /**
     * @see ObjectRepoViewer::get_types()
     */
    protected function get_types()
    {
        return $this->parent->get_types();
    }

    /**
     * Returns the publisher component's output in HTML format.
     * @return string The output.
     */
    abstract function as_html();

    /**
     * @see ObjectRepoViewer::get_url()
     */
    function get_url($parameters = array(), $encode = false)
    {
        return $this->get_parent()->get_url($parameters, $encode);
    }

    /**
     * @see ObjectRepoViewer::get_parameter()
     */
    function get_parameter($name)
    {
        $this->get_parent()->get_parameter($name);
    }

    /**
     * @see ObjectRepoViewer::set_parameter()
     */
    function set_parameter($name, $value)
    {
        $this->get_parent()->set_parameter($name, $value);
    }

    function set_default_content_object($type, $content_object)
    {
        $this->get_parent()->set_default_content_object($type, $content_object);
    }

    /**
     * @see ObjectRepoViewer::get_default_object()
     */
    function get_default_content_object($type)
    {
        return $this->get_parent()->get_default_content_object($type);
    }

    function redirect($message = null, $error_message = false, $parameters = array(), $filter = array(), $encode_entities = false)
    {
        return $this->get_parent()->redirect($message, $error_message, $parameters, $filter, $encode_entities);
    }

    function get_parameters()
    {
        return $this->get_parent()->get_parameters();
    }

    function set_parameters($parameters)
    {
        $this->get_parent()->set_parameters($parameters);
    }

    function get_creation_defaults()
    {
        return $this->get_parent()->get_creation_defaults();
    }

    function get_excluded_objects()
    {
        return $this->get_parent()->get_excluded_objects();
    }

    static function factory($type, $repoviewer)
    {
        $path = dirname(__FILE__) . '/component/' . Utilities :: camelcase_to_underscores($type) . '.class.php';
        
        if (! file_exists($path) || ! is_file($path))
        {
            $message = Translation :: get('ComponentFailedToLoad') . ': ' . Translation :: get($type);
            Display :: error_message($message);
        }
        
        $class = 'RepoViewer' . $type . 'Component';
        require_once $path;
        return new $class($repoviewer);
    }
}
?>