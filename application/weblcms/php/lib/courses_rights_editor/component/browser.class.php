<?php

namespace application\weblcms;

use application\weblcms\tool\course_group\CourseGroupMenu;
use common\libraries\PatternMatchCondition;
use common\libraries\ResourceManager;
use common\libraries\Theme;
use common\libraries\DynamicContentTab;
use common\libraries\DynamicTabsRenderer;
use common\libraries\Utilities;
use common\libraries\AndCondition;
use common\libraries\EqualityCondition;
use common\libraries\Request;
use common\libraries\Path;
use common\libraries\Translation;
use common\extensions\rights_editor_manager\RightsEditorManagerBrowserComponent;
use rights\RightsUtilities;
use common\libraries\DynamicVisualTabsRenderer;
use common\libraries\DynamicVisualTab;
use common\libraries\InequalityCondition;

/**
 * $Id: browser.class.php 191 2009-11-13 11:50:28Z chellee $
 * @package application.common.rights_editor_manager.component
 */
require_once Path :: get_common_extensions_path() . 'rights_editor_manager/php/component/browser.class.php';
require_once dirname(__FILE__) . '/location_course_group_browser/location_course_group_browser_table.class.php';
require_once dirname(__FILE__) . '/../../../../tool/course_group/php/course_group_menu.class.php';

class CoursesRightsEditorManagerBrowserComponent extends RightsEditorManagerBrowserComponent
{

    function get_display_html()
    {
        $html = array();

        $html[] = $this->display_type_selector();
        $html[] = $this->action_bar->as_html() . '<br />';
        $html[] = $this->display_locations();

        if ($this->type == self :: TYPE_USER)
        {
            $html[] = $this->display_location_user_browser();
        }
        elseif ($this->type == self :: TYPE_GROUP)
        {
            //$html[] = $this->display_location_group_browser();
            $html[] = $this->display_location_course_group_browser();
        }
        else
        {
            $html[] = $this->display_location_template_browser();
        }

        $html[] = '<div class="clear"></div><br />';
        $html[] = RightsUtilities :: get_rights_legend();

        $tabs = new DynamicVisualTabsRenderer(Utilities :: get_classname_from_object($this, true) . '_type', implode("\n", $html));
        foreach ($this->get_types() as $type)
        {
            $selected = ($type == $this->type ? true : false);

            $label = htmlentities(Translation :: get(Utilities :: underscores_to_camelcase($type) . 'Rights'));
            $link = $this->get_url(array(self :: PARAM_TYPE => $type));

            $tabs->add_tab(new DynamicVisualTab($type, $label, Theme :: get_image_path('common\extensions\rights_editor_manager') . 'place_' . $type . '.png', $link, $selected));
        }

        return $tabs->render();
    }

    function display_location_course_group_browser()
    {
        $html = array();

        $renderer_name = Utilities :: get_classname_from_object($this, true);
        $tabs = new DynamicTabsRenderer($renderer_name);

        $html[] = '<div style="float: left; width: 18%; overflow: auto;">';

        $course_group = Request :: get(CoursesRightsEditorManager :: PARAM_COURSE_GROUP) ? Request :: get(CoursesRightsEditorManager :: PARAM_COURSE_GROUP) : 1;

        $url = $this->get_parent()->get_url(array(self :: PARAM_TYPE => 'group')) . '&course=%s&course_group=%s';
        $course_group_menu = new CourseGroupMenu($this->get_parent()->get_course(), $course_group, $url);
        $html[] = $course_group_menu->render_as_tree();

        $html[] = '</div>';
        $html[] = '<div style="float: right; width: 80%;">';

        $course_group_object = WeblcmsDataManager :: get_instance()->retrieve_course_group($course_group);
        if ($course_group_object->has_children())
        {
            $table = new LocationCourseGroupBrowserTable($this, $this->get_parameters(), $this->get_course_group_conditions());
            $tabs->add_tab(new DynamicContentTab(self :: TAB_SUBGROUPS, Translation :: get('SubGroups', null, 'groups'), Theme :: get_image_path('group') . 'logo/' . Theme :: ICON_MINI . '.png', $table->as_html()));
        }

        $table = new LocationCourseGroupBrowserTable($this, $this->get_parameters(), $this->get_course_group_conditions(false));
        $tabs->add_tab(new DynamicContentTab(self :: TAB_DETAILS, Translation :: get('Rights', null, 'rights'), Theme :: get_image_path('rights') . 'logo/' . Theme :: ICON_MINI . '.png', $table->as_html()));

        $html[] = $tabs->render();
        $html[] = '</div>';
        $html[] = ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PATH) . 'application/weblcms/php/courses_rights_editor/javascript/configure_course_group.js');

        return implode("\n", $html);
    }

    function get_course_group_conditions()
    {
        $conditions = array();

        $query = $this->action_bar->get_query();
        if (isset($query) && $query != '')
        {
            $conditions[] = new PatternMatchCondition(CourseGroup :: PROPERTY_NAME, '*' . $query . '*');
        }

        $course_group = Request :: get(CoursesRightsEditorManager :: PARAM_COURSE_GROUP) ? Request :: get(CoursesRightsEditorManager :: PARAM_COURSE_GROUP) : 0;
        if($course_group == 0)//request the parent
        {
            $conditions[] = new EqualityCondition(CourseGroup :: PROPERTY_PARENT_ID, $course_group);
            $conditions[] = new EqualityCondition(CourseGroup :: PROPERTY_COURSE_CODE, request :: get(WeblcmsManager :: PARAM_COURSE));
        }
        else
        {
            $conditions[] = new EqualityCondition(CourseGroup :: PROPERTY_ID, $course_group);
            $conditions[] = new EqualityCondition(CourseGroup :: PROPERTY_COURSE_CODE, request :: get(WeblcmsManager :: PARAM_COURSE));
        }
        return new AndCondition($conditions);
    }

}

?>