<?php
/**
 * $Id: document_viewer.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.document.component
 */

class DocumentToolViewerComponent extends DocumentTool
{
    private $action_bar;
    private $introduction_text;

    function run()
    {
    	 
       $viewer = ToolComponent :: factory(ToolComponent :: ACTION_VIEW, $this);
         $viewer->set_action_bar($this->get_action_bar());
        $renderer = new ContentObjectPublicationDetailsRenderer($viewer);
        $html = $renderer->as_html();

    	$viewer->display_header();
        echo $viewer->get_action_bar()->as_html();
        echo '<div id="action_bar_browser">';
        echo $html;
        echo '</div>';
        $viewer->display_footer();
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

        if (! Request :: get(Tool :: PARAM_PUBLICATION_ID))
        {
            $action_bar->set_search_url($this->get_url());
            
            $properties=$this->get_tool()->get_properties();
    		
            if ($this->is_allowed(ADD_RIGHT) || $properties->name=="document")//|| ($category && $category->get_name() == Translation :: get('Dropbox')))
            {
                $action_bar->add_common_action(new ToolbarItem(Translation :: get('Publish'), Theme :: get_common_image_path() . 'action_publish.png', $this->get_url(array(DocumentTool :: PARAM_ACTION => DocumentTool :: ACTION_PUBLISH)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
            }
        }

        $action_bar->add_common_action(new ToolbarItem(Translation :: get('ShowAll'), Theme :: get_common_image_path() . 'action_browser.png', $this->get_url(array(Tool :: PARAM_ACTION => null)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));

        if (! Request :: get(Tool :: PARAM_PUBLICATION_ID) && $this->is_allowed(EDIT_RIGHT))
            $action_bar->add_common_action(new ToolbarItem(Translation :: get('ManageCategories'), Theme :: get_common_image_path() . 'action_category.png', $this->get_url(array(DocumentTool :: PARAM_ACTION => DocumentTool :: ACTION_MANAGE_CATEGORIES)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));

        if (! $this->introduction_text && $this->get_course()->get_intro_text() && $this->is_allowed(EDIT_RIGHT))
        {
            $action_bar->add_common_action(new ToolbarItem(Translation :: get('PublishIntroductionText'), Theme :: get_common_image_path() . 'action_introduce.png', $this->get_url(array(DocumentTool :: PARAM_ACTION => Tool :: ACTION_PUBLISH_INTRODUCTION)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        }

        if (! Request :: get(Tool :: PARAM_PUBLICATION_ID))
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
            $conditions[] = new PatternMatchCondition(ContentObject :: PROPERTY_TITLE, '*' . $query . '*', ContentObject :: get_table_name());
            $conditions[] = new PatternMatchCondition(ContentObject :: PROPERTY_DESCRIPTION, '*' . $query . '*', ContentObject :: get_table_name());
            return new OrCondition($conditions);
        }

        return null;
    }
}
?>