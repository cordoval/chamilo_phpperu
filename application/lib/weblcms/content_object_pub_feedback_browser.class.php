<?php
/**
 * $Id: content_object_pub_feedback_browser.class.php 218 2009-11-13 14:21:26Z kariboe $
 * @package application.lib.weblcms
 */
require_once dirname(__FILE__) . '/browser/content_object_publication_list_renderer.class.php';
require_once dirname(__FILE__) . '/browser/learningobjectpublicationcategorytree.class.php';

/**
==============================================================================
 *	This class allows the user to browse through learning object publications.
 *
 *	@author Tim De Pauw
==============================================================================
 */
abstract class ContentObjectPubFeedbackBrowser
{
    /**
     * The types of learning objects for which publications need to be
     * displayed.
     */
    private $types;
    
    /**
     * The ID of the category that is currently active.
     */
    private $category;
    
    /**
     * The list renderer used to display objects.
     */
    protected $listRenderer;
    
    /**
     * The tree view used to display categories.
     */
    private $categoryTree;
    
    /**
     * The tool that instantiated this browser.
     */
    private $parent;
    
    private $publication_id;

    /**
     * Constructor.
     * @param Tool $parent The tool that instantiated this browser.
     * @param mixed $types The types of learning objects for which
     *                     publications need to be displayed.
     */
    function ContentObjectPubFeedbackBrowser($parent, $types)
    {
        $this->parent = $parent;
        $this->types = is_array($types) ? $types : array($types);
    }

    /**
     * Returns the publication browser's content in HTML format.
     * @return string The HTML.
     */
    function as_html()
    {
        if (! isset($this->categoryTree))
        {
            return $this->listRenderer->as_html();
        }
        return '<div style="float: left; width: 18%; overflow: auto;">' . $this->categoryTree->as_html() . '</div>' . '<div style="float: right; width: 80%">' . $this->listRenderer->as_html() . '</div>' . '<div class="clear">&nbsp;</div>';
    }

    /**
     * Returns the learning object publication list renderer associated with
     * this object.
     * @return ContentObjectPublicationRenderer The renderer.
     */
    function get_publication_list_renderer()
    {
        return $this->listRenderer;
    }

    /**
     * Sets the renderer for the publication list.
     * @param ContentObjectPublicationRenderer $renderer The renderer.
     */
    function set_publication_list_renderer($renderer)
    {
        $this->listRenderer = $renderer;
    }

    /**
     * Gets the publication category tree.
     * @return ContentObjectPublicationCategoryTree The category tree.
     */
    function get_publication_category_tree()
    {
        return $this->categoryTree;
    }

    function get_publication_id()
    {
        return $this->publication_id;
    }

    function set_publication_id($publication_id)
    {
        $this->publication_id = $publication_id;
    }

    /**
     * Sets the publication category tree.
     * @param ContentObjectPublicationCategoryTree $tree The category tree.
     */
    function set_publication_category_tree($tree)
    {
        $this->categoryTree = $tree;
    }

    /**
     * Returns the repository tool that this browser is associated with.
     * @return Tool The tool.
     */
    function get_parent()
    {
        return $this->parent;
    }

    /**
     * Returns the ID of the current category.
     * @return int The category ID.
     */
    function get_category()
    {
        return $this->category;
    }

    function set_category($category)
    {
        $this->category = $category;
    }

    /**
     * @see Tool :: get_user_id()
     */
    function get_user_id()
    {
        return $this->parent->get_user_id();
    }

    function get_user_info($user_id)
    {
        return $this->parent->get_user_info($user_id);
    }

    /**
     * @see Tool :: get_course_groups()
     */
    function get_course_groups()
    {
        return $this->parent->get_course_groups();
    }

    /**
     * @see Tool :: get_course_id()
     */
    function get_course_id()
    {
        return $this->parent->get_course_id();
    }

    /**
     * @see Tool :: get_categories()
     */
    function get_categories($list = false)
    {
        return $this->parent->get_categories($list);
    }

    /**
     * @see Tool :: get_url()
     */
    function get_url($parameters = array (), $filter = array(), $encode_entities = false)
    {
        return $this->parent->get_url($parameters, $filter, $encode_entities);
    }

    /**
     * @see Tool :: get_parameters()
     */
    function get_parameters()
    {
        return $this->parent->get_parameters();
    }

    /**
     * @see Tool :: get_parameter()
     */
    function get_parameter($name)
    {
        return $this->parent->get_parameter($name);
    }

    /**
     * @see Tool :: is_allowed()
     */
    function is_allowed($right)
    {
        return $this->parent->is_allowed($right);
    }

    /**
     * @see WeblcmsManager :: get_last_visit_date()
     */
    function get_last_visit_date()
    {
        return $this->parent->get_last_visit_date();
    }

    /**
     * Returns the learning object publications to display.
     * @param int $from The index of the first publication to return.
     * @param int $count The maximum number of publications to return.
     * @param int $column The index of the column to sort the table on.
     * @param int $direction The sorting direction; either SORT_ASC or
     *                       SORT_DESC.
     * @return array The learning object publications.
     */
    
    /*abstract function get_publications($from, $count, $column, $direction);

    abstract function get_publication_count();*/
    
    function get_path($path_type)
    {
        return $this->get_parent()->get_parent()->get_path($path_type);
    }
}
?>
