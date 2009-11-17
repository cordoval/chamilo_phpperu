<?php
/**
 * $Id: geolocation_browser.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.geolocation.component
 */
require_once dirname(__FILE__) . '/../geolocation_tool.class.php';
require_once dirname(__FILE__) . '/../geolocation_tool_component.class.php';
require_once dirname(__FILE__) . '/geolocation_browser/geolocation_browser.class.php';
require_once dirname(__FILE__) . '/geolocation_browser/geolocation_cell_renderer.class.php';
require_once Path :: get_repository_path() . 'lib/content_object/physical_location/physical_location.class.php';
require_once dirname(__FILE__) . '/../../../browser/object_publication_table/object_publication_table.class.php';

class GeolocationToolBrowserComponent extends GeolocationToolComponent
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
        $conditions[] = new EqualityCondition(ContentObjectPublication :: PROPERTY_TOOL, 'geolocation');
        
        $subselect_condition = new EqualityCondition('type', 'introduction');
        $conditions[] = new SubselectCondition(ContentObjectPublication :: PROPERTY_CONTENT_OBJECT_ID, ContentObject :: PROPERTY_ID, RepositoryDataManager :: get_instance()->get_database()->escape_table_name(ContentObject :: get_table_name()), $subselect_condition);
        $condition = new AndCondition($conditions);
        
        $publications = WeblcmsDataManager :: get_instance()->retrieve_content_object_publications_new($condition);
        $this->introduction_text = $publications->next_result();
        
        $this->action_bar = $this->get_action_bar();
        
        $trail = new BreadcrumbTrail();
        
        if (Request :: get('pid') != null)
        {
            $trail->add(new Breadcrumb($this->get_url(array(Tool :: PARAM_ACTION => GeolocationTool :: ACTION_BROWSE, Tool :: PARAM_PUBLICATION_ID => Request :: get('pid'))), WebLcmsDataManager :: get_instance()->retrieve_content_object_publication(Request :: get('pid'))->get_content_object()->get_title()));
            $browser = new GeolocationBrowser($this);
            $html = $browser->as_html();
        
        }
        else
        {
            $table = new ObjectPublicationTable($this, $this->get_user(), array('physical_location'), $this->get_condition(), new GeolocationCellRenderer($this));
            $html .= $table->as_html();
        }
        
        $trail->add_help('courses geolocation tool');
        $this->display_header($trail, true);
        
        if (! Request :: get('pid'))
        {
            if (PlatformSetting :: get('enable_introduction', 'weblcms'))
            {
                echo $this->display_introduction_text($this->introduction_text);
            }
        }
        
        echo $this->action_bar->as_html();
        echo '<div id="action_bar_browser">';
        echo $html;
        
        if (Request :: get('pid') == null)
        {
            $conditions = array();
            $conditions[] = new EqualityCondition(ContentObjectPublication :: PROPERTY_COURSE_ID, $this->get_course_id());
            $conditions[] = new EqualityCondition(ContentObjectPublication :: PROPERTY_TOOL, 'geolocation');
            $subselect_condition = new EqualityCondition('type', 'physical_location');
            $conditions[] = new SubselectCondition(ContentObjectPublication :: PROPERTY_CONTENT_OBJECT_ID, ContentObject :: PROPERTY_ID, RepositoryDataManager :: get_instance()->get_database()->escape_table_name(ContentObject :: get_table_name()), $subselect_condition);
            $condition = new AndCondition($conditions);
            
            $publications = WeblcmsDataManager :: get_instance()->retrieve_content_object_publications_new($condition);
            
            if ($publications->size())
            {
                $html = array();
                
                $html[] = '<br /><br /><h3>' . Translation :: get('LocationsSummary') . '</h3>';
                
                $html[] = '<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>';
                $html[] = ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PATH) . 'common/javascript/google_maps.js');
                $html[] = '<div id="map_canvas" style="border: 1px solid black; height:500px"></div>';
                $html[] = '<script type="text/javascript">';
                $html[] = 'initialize(8);';
                
                while ($publication = $publications->next_result())
                {
                    if ($publication->is_visible_for_target_users())
                        $html[] = 'codeAddress(\'' . $publication->get_content_object()->get_location() . '\', \'' . $publication->get_content_object()->get_title() . '\');';
                }
                $html[] = '</script>';
                
                echo implode("\n", $html);
            }
        }
        
        echo '</div>';
        
        $this->display_footer();
    }

    function add_actionbar_item($item)
    {
        $this->action_bar->add_tool_action($item);
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        
        if (! Request :: get('pid'))
        {
            $action_bar->set_search_url($this->get_url());
            if ($this->is_allowed(ADD_RIGHT))
            {
                $action_bar->add_common_action(new ToolbarItem(Translation :: get('Publish'), Theme :: get_common_image_path() . 'action_publish.png', $this->get_url(array(GeolocationTool :: PARAM_ACTION => GeolocationTool :: ACTION_PUBLISH)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
            }
        }
        
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('ShowAll'), Theme :: get_common_image_path() . 'action_browser.png', $this->get_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        
        if (! $this->introduction_text && PlatformSetting :: get('enable_introduction', 'weblcms') && $this->is_allowed(EDIT_RIGHT))
        {
            $action_bar->add_common_action(new ToolbarItem(Translation :: get('PublishIntroductionText'), Theme :: get_common_image_path() . 'action_introduce.png', $this->get_url(array(AnnouncementTool :: PARAM_ACTION => Tool :: ACTION_PUBLISH_INTRODUCTION)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
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