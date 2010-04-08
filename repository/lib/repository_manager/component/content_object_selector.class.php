<?php
/**
 * $Id: content_object_selector.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.repository_manager.component
 */
/**
 * Default repository manager component which allows the user to browse through
 * the different categories and learning objects in the repository.
 */
class RepositoryManagerContentObjectSelectorComponent extends RepositoryManagerComponent
{
    /**
     * Runs this component and displays its output.
     */
    
    private $root_id;
    private $cloi_id;

    function run()
    {
        $trail = new BreadcrumbTrail();
        $cloi_id = Request :: get(RepositoryManager :: PARAM_CLOI_ID);
        $root_id = Request :: get(RepositoryManager :: PARAM_CLOI_ROOT_ID);
        
        $trail = new BreadcrumbTrail();
        $trail->add_help('repository general');
        
        if (! Request :: get('publish'))
        {
            $trail->add(new Breadcrumb($this->get_link(array(Application :: PARAM_ACTION => RepositoryManager :: ACTION_BROWSE_CONTENT_OBJECTS)), Translation :: get('Repository')));
        }
        
        if (isset($cloi_id) && isset($root_id))
        {
            $this->cloi_id = $cloi_id;
            $this->root_id = $root_id;
        }
        else
        {
            $this->display_header($trail, false, true);
            $this->display_error_message(Translation :: get('NoCLOISelected'));
            $this->display_footer();
            exit();
        }
        $root = $this->retrieve_content_object($root_id);
        if (! Request :: get('publish'))
        {
            $trail->add(new Breadcrumb($this->get_link(array(Application :: PARAM_ACTION => RepositoryManager :: ACTION_VIEW_CONTENT_OBJECTS, RepositoryManager :: PARAM_CONTENT_OBJECT_ID => $root_id)), $root->get_title()));
            $trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => RepositoryManager :: ACTION_BROWSE_COMPLEX_CONTENT_OBJECTS, RepositoryManager :: PARAM_CLOI_ID => $cloi_id, RepositoryManager :: PARAM_CLOI_ROOT_ID => $root_id)), Translation :: get('ViewComplexContentObject')));
            $trail->add(new Breadcrumb($this->get_url(), Translation :: get('AddExistingContentObject')));
        }
        
        $output = $this->get_content_objects_html();
        $this->display_header($trail, false, true);
        echo $output;
        $this->display_footer();
    }

    /**
     * Gets the  table which shows the learning objects in the currently active
     * category
     */
    private function get_content_objects_html()
    {
        $condition = $this->get_condition();
        $parameters = $this->get_parameters(true);
        $types = Request :: get(RepositoryManager :: PARAM_CONTENT_OBJECT_TYPE);
        if (is_array($types) && count($types))
        {
            $parameters[RepositoryManager :: PARAM_CONTENT_OBJECT_TYPE] = $types;
        }
        $parameters = array_merge($parameters, array(RepositoryManager :: PARAM_CLOI_ID => $this->get_cloi_id(), RepositoryManager :: PARAM_CLOI_ROOT_ID => $this->get_root_id(), 'publish' => Request :: get('publish')));
        
        $table = new RepositoryBrowserTable($this, $parameters, $condition);
        return $table->as_html();
    }

    private function get_condition()
    {
        
        $conditions = array();
        $conditions[] = $this->get_search_condition();
        
        $clo = $this->retrieve_content_object($this->cloi_id);
        $types = $clo->get_allowed_types();
        $conditions1 = array();
        foreach ($types as $type)
        {
            $conditions1[] = new EqualityCondition(ContentObject :: PROPERTY_TYPE, $type);
        }
        $conditions[] = new OrCondition($conditions1);
        
        $conditions = array_merge($conditions, $this->retrieve_used_items($this->root_id));
        $conditions[] = new NotCondition(new EqualityCondition(ContentObject :: PROPERTY_ID, $this->root_id, ContentObject :: get_table_name()));
        
        return new AndCondition($conditions);
    }

    function get_root_id()
    {
        return $this->root_id;
    }

    function get_cloi_id()
    {
        return $this->cloi_id;
    }

    /**
     * This function is beeing used to determine all the complex learning objects that are used in a learning object
     * so we won't get stuck in an endless loop and returns a conditionslist to exclude the items
     */
    function retrieve_used_items($cloi_id)
    {
        $conditions = array();
        
        $clois = $this->retrieve_complex_content_object_items(new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $cloi_id, ComplexContentObjectItem :: get_table_name()));
        while ($cloi = $clois->next_result())
        {
            if ($cloi->is_complex())
            {
                $conditions[] = new NotCondition(new EqualityCondition(ContentObject :: PROPERTY_ID, $cloi->get_ref(), ContentObject :: get_table_name()));
                $conditions = array_merge($conditions, $this->retrieve_used_items($cloi->get_ref()));
            }
        }
        
        return $conditions;
    }
}
?>