<?php
/**
 * $Id: document_viewer.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.document.component
 */
require_once dirname(__FILE__) . '/../document_tool.class.php';
require_once dirname(__FILE__) . '/../../../browser/object_publication_table/object_publication_table.class.php';
require_once dirname(__FILE__) . '/../document_tool_component.class.php';
require_once dirname(__FILE__) . '/document_viewer/document_browser.class.php';
require_once dirname(__FILE__) . '/document_viewer/document_cell_renderer.class.php';
require_once Path :: get_repository_path() . 'lib/content_object/document/document.class.php';

class DocumentToolViewerComponent extends DocumentToolComponent
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
        $conditions[] = new EqualityCondition(ContentObjectPublication :: PROPERTY_TOOL, 'document');
        
        $subselect_condition = new EqualityCondition('type', 'introduction');
        $conditions[] = new SubselectCondition(ContentObjectPublication :: PROPERTY_CONTENT_OBJECT_ID, ContentObject :: PROPERTY_ID, RepositoryDataManager :: get_instance()->get_database()->escape_table_name(ContentObject :: get_table_name()), $subselect_condition);
        $condition = new AndCondition($conditions);
        
        $publications = WeblcmsDataManager :: get_instance()->retrieve_content_object_publications_new($condition);
        $this->introduction_text = $publications->next_result();
        
        $this->action_bar = $this->get_action_bar();
        $trail = new BreadcrumbTrail();
        
        if (Request :: get('pid') != null)
        {
            $trail->add(new Breadcrumb($this->get_url(array(Tool :: PARAM_ACTION => DocumentTool :: ACTION_VIEW_DOCUMENTS, Tool :: PARAM_PUBLICATION_ID => Request :: get('pid'))), WebLcmsDataManager :: get_instance()->retrieve_content_object_publication(Request :: get('pid'))->get_content_object()->get_title()));
            $browser = new DocumentBrowser($this, 'document');
            $html = $browser->as_html();
        }
        else
        {
            $table = new ObjectPublicationTable($this, $this->get_user(), array('document'), $this->get_condition(), new DocumentCellRenderer($this));
            $tree = new ContentObjectPublicationCategoryTree($this, Request :: get('pcattree'));
            $html = '<div style="width: 18%; overflow: auto; float:left;">';
            $html .= $tree->as_html();
            $html .= '</div>';
            $html .= '<div style="width: 80%; overflow: auto;">';
            $html .= $table->as_html();
            $html .= '</div>';
        }
        
        $trail->add_help('courses document tool');
        $this->display_header($trail, true);
        
        if (! Request :: get('pid'))
        {
            if (PlatformSetting :: get('enable_introduction', 'weblcms'))
            {
                echo $this->display_introduction_text($this->introduction_text);
            }
        }
        echo $this->action_bar->as_html();
        echo $html;
        $this->display_footer();
    }

    function add_actionbar_item($item)
    {
        $this->action_bar->add_tool_action($item);
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        
        $cat_id = Request :: get('pcattree');
        $category = WeblcmsDataManager :: get_instance()->retrieve_content_object_publication_category($cat_id);
        
        if (! Request :: get('pid'))
        {
            $action_bar->set_search_url($this->get_url());
            if ($this->is_allowed(ADD_RIGHT) || ($category && $category->get_name() == Translation :: get('Dropbox')))
            {
                $action_bar->add_common_action(new ToolbarItem(Translation :: get('Publish'), Theme :: get_common_image_path() . 'action_publish.png', $this->get_url(array(DocumentTool :: PARAM_ACTION => DocumentTool :: ACTION_PUBLISH)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
            }
        }
        
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('ShowAll'), Theme :: get_common_image_path() . 'action_browser.png', $this->get_url(array(Tool :: PARAM_ACTION => null)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        
        if (! Request :: get('pid') && $this->is_allowed(EDIT_RIGHT))
            $action_bar->add_common_action(new ToolbarItem(Translation :: get('ManageCategories'), Theme :: get_common_image_path() . 'action_category.png', $this->get_url(array(DocumentTool :: PARAM_ACTION => DocumentTool :: ACTION_MANAGE_CATEGORIES)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        
        if (! $this->introduction_text && PlatformSetting :: get('enable_introduction', 'weblcms') && $this->is_allowed(EDIT_RIGHT))
        {
            $action_bar->add_common_action(new ToolbarItem(Translation :: get('PublishIntroductionText'), Theme :: get_common_image_path() . 'action_introduce.png', $this->get_url(array(AnnouncementTool :: PARAM_ACTION => Tool :: ACTION_PUBLISH_INTRODUCTION)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        }
        
        if (! Request :: get('pid'))
        {
            $action_bar->add_tool_action(new ToolbarItem(Translation :: get('Download'), Theme :: get_common_image_path() . 'action_save.png', $this->get_url(array(DocumentTool :: PARAM_ACTION => DocumentTool :: ACTION_ZIP_AND_DOWNLOAD)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
            $action_bar->add_tool_action(new ToolbarItem(Translation :: get('Slideshow'), Theme :: get_common_image_path() . 'action_slideshow.png', $this->get_url(array(DocumentTool :: PARAM_ACTION => DocumentTool :: ACTION_SLIDESHOW)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        }
        
        if ($this->is_allowed(EDIT_RIGHT))
        {
            $action_bar->add_tool_action($this->get_access_details_toolbar_item($this));
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