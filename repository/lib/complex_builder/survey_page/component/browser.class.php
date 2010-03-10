<?php
/**
 * $Id: browser.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_builder.learning_path.component
 */
require_once dirname(__FILE__) . '/../survey_page_builder_component.class.php';
require_once dirname(__FILE__) . '/browser/survey_page_browser_table_cell_renderer.class.php';

class SurveyPageBuilderBrowserComponent extends SurveyPageBuilderComponent
{

    function run()
    {
        $menu_trail = $this->get_clo_breadcrumbs();
        $trail = new BreadcrumbTrail(false);
        $trail->merge($menu_trail);
        $trail->add_help('repository survey_page builder');
        
        if ($this->get_cloi())
        {
            $lo = RepositoryDataManager :: get_instance()->retrieve_content_object($this->get_cloi()->get_ref());
        }
        else
        {
            $lo = $this->get_root_lo();
        }
        
        $this->display_header($trail);
        $action_bar = $this->get_action_bar($lo);
        
        if ($action_bar)
        {
            echo '<br />';
            echo $action_bar->as_html();
        }
        
        //echo $this->get_object_info();
        

        //        if ($lo->get_version() == 'chamilo')
        //        {	
        echo '<br />';
        //$types = array('learning_path', 'announcement', 'assessment', 'blog_item', 'calendar_event', 'description', 'document', 'forum', 'glossary', 'hotpotatoes', 'link', 'note', 'wiki');
        $types = $lo->get_allowed_types();
        echo $this->get_creation_links($lo, $types);
        echo '<div class="clear">&nbsp;</div><br />';
        //        }
        

        echo '<div style="width: 18%; overflow: auto; float: left;">';
        echo $this->get_clo_menu();
        echo '</div><div style="width: 80%; float: right;">';
        echo $this->get_clo_table_html(false, null, new SurveyPageBrowserTableCellRenderer($this->get_parent(), $this->get_clo_table_condition()));
        echo '</div>';
        echo '<div class="clear">&nbsp;</div>';
        
        $this->display_footer();
    }

//    function get_object_info()
//    {
//        $html = array();
//        
//        $content_object = $this->get_root_lo();
//        $display = ContentObjectDisplay :: factory($content_object);
//        $content_object_display = $display->get_full_html();
//        $check_empty = trim(strip_tags($content_object_display));
//        
//        if (! empty($check_empty) && $check_empty != $content_object->get_title())
//        {
//            $html[] = '<div class="complex_browser_display">';
//            $html[] = $content_object_display;
//            $html[] = '<div class="clear">&nbsp;</div>';
//            $html[] = '</div>';
//        }
//        
//        return implode("\n", $html);
//    }

    function get_action_bar()
    {
        $pub = Request :: get('publish');
        
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        
        /*$type = 'learning_path';
		$url = $this->get_url(array(ComplexBuilder :: PARAM_BUILDER_ACTION => LearningPathBuilder :: ACTION_CREATE_CLOI, ComplexBuilder :: PARAM_TYPE => $type, ComplexBuilder :: PARAM_ROOT_LO => $this->get_root_lo()->get_id(), ComplexBuilder :: PARAM_CLOI_ID => ($this->get_cloi()?$this->get_cloi()->get_id():null), 'publish' => Request :: get('publish')));
		$action_bar->add_common_action(new ToolbarItem(Translation :: get(Utilities :: underscores_to_camelcase($type . 'TypeName')), Theme :: get_common_image_path().'content_object/' . $type . '.png', $url));

		$types = array('announcement', 'assessment', 'blog_item', 'calendar_event', 'description', 'document', 'forum', 'glossary', 'link', 'note', 'wiki');
		foreach($types as $type)
		{
			$url = $this->get_url(array(ComplexBuilder :: PARAM_BUILDER_ACTION => LearningPathBuilder :: ACTION_CREATE_LP_ITEM, ComplexBuilder :: PARAM_TYPE => $type, ComplexBuilder :: PARAM_ROOT_LO => $this->get_root_lo()->get_id(), ComplexBuilder :: PARAM_CLOI_ID => ($this->get_cloi()?$this->get_cloi()->get_id():null), 'publish' => Request :: get('publish')));
			$action_bar->add_tool_action(new ToolbarItem(Translation :: get(Utilities :: underscores_to_camelcase($type . 'TypeName')), Theme :: get_common_image_path().'content_object/' . $type . '.png', $url));
		}*/
        
        if ($pub && $pub != '')
        {
            $action_bar->add_common_action(new ToolbarItem(Translation :: get('Publish'), Theme :: get_common_image_path() . 'action_publish.png', $_SESSION['redirect_url']));
            return $action_bar;
        }
    }

    function get_creation_links($lo, $types = array())
    {
        $html[] = '<div class="select_complex_element">';
        $html[] = '<span class="title">' . Theme :: get_common_image('place_content_objects') . Translation :: get('SurveyPageAddContentObject') . '</span>';
        $html[] = '<div id="content_object_selection">';
        
        if (count($types) == 0)
        {
            $types = $lo->get_allowed_types();
        }
               
        foreach ($types as $type)
        {
            if ($type == 'survey_page_page')
            {
                $url = $this->get_url(array(ComplexBuilder :: PARAM_BUILDER_ACTION => ComplexBuilder :: ACTION_CREATE_CLOI, ComplexBuilder :: PARAM_TYPE => $type, ComplexBuilder :: PARAM_ROOT_LO => $this->get_root_lo()->get_id(), ComplexBuilder :: PARAM_CLOI_ID => ($this->get_cloi() ? $this->get_cloi()->get_id() : null), 'publish' => Request :: get('publish')));
            }
            else
            {
                $url = $this->get_url(array(ComplexBuilder :: PARAM_BUILDER_ACTION => SurveyPageBuilder :: ACTION_CREATE_SURVEY_PAGE_ITEM, ComplexBuilder :: PARAM_TYPE => $type, ComplexBuilder :: PARAM_ROOT_LO => $this->get_root_lo()->get_id(), ComplexBuilder :: PARAM_CLOI_ID => ($this->get_cloi() ? $this->get_cloi()->get_id() : null), 'publish' => Request :: get('publish')));
            }
            
            $html[] = '<a href="' . $url . '"><div class="create_block" style="background-image: url(' . Theme :: get_common_image_path() . 'content_object/big/' . $type . '.png);">';
            $html[] = Translation :: get(ContentObject :: type_to_class($type) . 'TypeName');
            $html[] = '<div class="clear">&nbsp;</div>';
            $html[] = '</div></a>';
        }
        
        $html[] = '<div class="clear">&nbsp;</div>';
        $html[] = ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_LIB_PATH) . 'javascript/repository.js');
        $html[] = '</div>';
        $html[] = '<div class="clear">&nbsp;</div>';
        $html[] = '</div>';
        
        return implode("\n", $html);
    }
}

?>
