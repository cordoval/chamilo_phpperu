<?php
/**
 * $Id: wiki_browser.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.wiki.component
 */
/*
 * This is the first page you'll get when adding a wiki to a course.
 * It shows a list of every available wiki. You can edit, delete or hide a wiki.
 *
 * Author: Stefan Billiet
 * Author: Nick De Feyter
 */


require_once dirname(__FILE__) . '/../../../browser/object_publication_table/object_publication_table.class.php';
require_once dirname(__FILE__) . '/wiki_browser/wiki_cell_renderer.class.php';

class WikiToolBrowserComponent extends WikiToolComponent
{
    private $action_bar;
    private $introduction_text;

    function run()
    {
        if (! $this->is_allowed(VIEW_RIGHT))
        {
            Display :: not_allowed();
            return;
        }
        
        $conditions = array();
        $conditions[] = new EqualityCondition(ContentObjectPublication :: PROPERTY_COURSE_ID, $this->get_course_id());
        $conditions[] = new EqualityCondition(ContentObjectPublication :: PROPERTY_TOOL, 'wiki');
        
        $subselect_condition = new EqualityCondition('type', 'introduction');
        $conditions[] = new SubselectCondition(ContentObjectPublication :: PROPERTY_CONTENT_OBJECT_ID, ContentObject :: PROPERTY_ID, RepositoryDataManager :: get_instance()->get_database()->escape_table_name(ContentObject :: get_table_name()), $subselect_condition);
        $condition = new AndCondition($conditions);
        
        $publications = WeblcmsDataManager :: get_instance()->retrieve_content_object_publications_new($condition);
        $this->introduction_text = $publications->next_result();
        
        $this->action_bar = $this->get_toolbar();
        
        $trail = new BreadcrumbTrail();
        $trail->add_help('courses wiki tool');
        
        $this->display_header($trail, true);
        if (! Request :: get('pid'))
        {
            if (PlatformSetting :: get('enable_introduction', 'weblcms'))
            {
                echo $this->display_introduction_text($this->introduction_text);
            }
        }
        
        echo $this->action_bar->as_html();
        
        $table = new ObjectPublicationTable($this, $this->get_user(), array('wiki'), $this->get_condition(), new WikiCellRenderer($this));
        echo $table->as_html();
        
        $this->display_footer();
    }

    function get_toolbar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        $action_bar->set_search_url($this->get_url());
        
        if ($this->is_allowed(ADD_RIGHT))
        {
            
            $action_bar->add_common_action(new ToolbarItem(Translation :: get('PublishWiki'), Theme :: get_common_image_path() . 'action_create.png', $this->get_url(array(WikiTool :: PARAM_ACTION => WikiTool :: ACTION_PUBLISH)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        }
        
        /*$action_bar->add_common_action(
			new ToolbarItem(
				Translation :: get('Browse'), Theme :: get_common_image_path().'action_browser.png', $this->get_url(array(WikiTool :: PARAM_ACTION => WikiTool :: ACTION_BROWSE_WIKIS)), ToolbarItem :: DISPLAY_ICON_AND_LABEL
			)
		);*/
        
        if (! $this->introduction_text && PlatformSetting :: get('enable_introduction', 'weblcms') && $this->is_allowed(EDIT_RIGHT))
        {
            $action_bar->add_common_action(new ToolbarItem(Translation :: get('PublishIntroductionText'), Theme :: get_common_image_path() . 'action_introduce.png', $this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_PUBLISH_INTRODUCTION)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        }
        return $action_bar;
    }

    function get_condition()
    {
        $query = $this->action_bar->get_query();
        if (isset($query) && $query != '')
        {
            $conditions[] = new LikeCondition(ContentObject :: PROPERTY_TITLE, $query, ContentObject :: get_table_name());
            $conditions[] = new LikeCondition(ContentObject :: PROPERTY_DESCRIPTION, $query, ContentObject :: get_table_name());
            return new OrCondition($conditions);
        }
        
        return null;
    }
}
?>