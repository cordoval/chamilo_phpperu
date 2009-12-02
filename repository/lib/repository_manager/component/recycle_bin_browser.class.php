<?php
/**
 * $Id: recycle_bin_browser.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.repository_manager.component
 */
/**
 * Default repository manager component which allows the user to browse through
 * the different categories and learning objects in the repository.
 */
class RepositoryManagerRecycleBinBrowserComponent extends RepositoryManagerComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = new BreadcrumbTrail(false);
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('RecycleBin')));
        $trail->add_help('repository recyclebin');

        $this->display_header($trail, false, true);

        if (Request :: get(RepositoryManager :: PARAM_EMPTY_RECYCLE_BIN))
        {
            $this->empty_recycle_bin();
            $this->display_message(htmlentities(Translation :: get('RecycleBinEmptied')));
        }

        echo $this->get_action_bar()->as_html();
        $this->display_content_objects();
        $this->display_footer();
    }

    /**
     * Display the learning objects in the recycle bin.
     * @return int The number of learning objects currently in the recycle bin.
     */
    private function display_content_objects()
    {
        $parameters = $this->get_parameters(true);
        $table = new RecycleBinBrowserTable($this, $parameters, $this->get_condition());
        echo $table->as_html();
        return $table->get_object_count();
    }

    /**
     * Empty the recycle bin.
     * This function will permanently delete all objects from the recycle bin.
     * Only objects from current user will be deleted.
     */
    private function empty_recycle_bin()
    {
        $condition_recycled = new EqualityCondition(ContentObject :: PROPERTY_STATE, ContentObject :: STATE_RECYCLED);
        $condition_user     = new EqualityCondition(ContentObject :: PROPERTY_OWNER_ID, $this->get_user_id());
        $condition          = new AndCondition($condition_recycled, $condition_user);
        
        $trashed_objects = $this->retrieve_content_objects($condition, array(), 0, - 1);
        $count = 0;
        while ($object = $trashed_objects->next_result())
        {
            $object->delete();
            $count ++;
        }
        return $count;
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

        $action_bar->add_common_action(new ToolbarItem(Translation :: get('EmptyRecycleBin'), Theme :: get_common_image_path() . 'treemenu/trash.png', $this->get_url(array(RepositoryManager :: PARAM_EMPTY_RECYCLE_BIN => 1)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));

        return $action_bar;
    }

    function get_condition()
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(ContentObject :: PROPERTY_OWNER_ID, $this->get_user_id());
        $conditions[] = new EqualityCondition(ContentObject :: PROPERTY_STATE, ContentObject :: STATE_RECYCLED);
        return new AndCondition($conditions);
    }
}
?>