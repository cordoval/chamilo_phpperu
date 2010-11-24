<?php
namespace application\weblcms;

use application\weblcms\tool\link;

use common\libraries\Theme;
use common\libraries\DynamicContentTab;
use common\libraries\DynamicTabsRenderer;
use common\libraries\AndCondition;
use common\libraries\EqualityCondition;
use common\libraries\Path;
use common\libraries\Translation;
use common\libraries\Utilities;
use application\weblcms\tool\home\HomeTool;
use application\weblcms\tool\link\LinkTool;
use HTML_Table;

/**
 * $Id: fixed_location_tool_list_renderer.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool_list_renderer
 */
require_once (dirname(__FILE__) . '/../tool_list_renderer.class.php');
require_once (dirname(__FILE__) . '/../course/course_section.class.php');
/**
 * Tool list renderer which displays all course tools on a fixed location.
 * Disabled tools will be shown in a disabled looking way.
 */
class FixedLocationToolListRenderer extends ToolListRenderer
{
    private $number_of_columns = 2;
    private $is_course_admin;
    private $course;

    /**
     * Constructor
     * @param  WebLcms $parent The parent application
     */
    function __construct($parent, $visible_tools)
    {
        parent :: __construct($parent, $visible_tools);

        $course = $parent->get_course();
        $this->course = $course;
        $this->number_of_columns = ($course->get_layout() % 2 == 0) ? 3 : 2;
        $this->is_course_admin = $this->get_parent()->is_allowed(WeblcmsRights :: EDIT_RIGHT);
    }

    // Inherited
    function display()
    {
        $parent = $this->get_parent();
        $tools = array();
        foreach ($this->get_visible_tools() as $tool)
        {
            if ($this->course->get_layout() > 2)
            {
                if ($tool->visible)
                {
                    $tools[$tool->section][] = $tool;
                }
                else
                {
                    $tools[CourseSection :: TYPE_DISABLED][] = $tool;
                }
            }
            else
            {
                $tools[$tool->section][] = $tool;
            }

        }

        echo '<div id="coursecode" style="display: none;">' . $this->course->get_id() . '</div>';

        $tabs = new DynamicTabsRenderer('admin');

        $sections = WeblcmsDataManager :: get_instance()->retrieve_course_sections(new EqualityCondition('course_id', $this->course->get_id()));
        while ($section = $sections->next_result())
        {
            if (! $section->get_visible() && ! $this->is_course_admin)
            {
                continue;
            }

            if ($section->get_type() == CourseSection :: TYPE_LINK)
            {
                if ($this->get_publication_links()->size() > 0)
                {
                    $content = $this->show_links($section);
                    $tabs->add_tab(new DynamicContentTab($section->get_id(), $section->get_name(), null, $content));
                }
            }
            else
            {
                if ($section->get_type() == CourseSection :: TYPE_DISABLED && ($this->course->get_layout() < 3 || ! $this->is_course_admin))
                {
                    continue;
                }

                if ($section->get_type() == CourseSection :: TYPE_ADMIN && ! $this->is_course_admin)
                {
                    continue;
                }

                $id = ($section->get_type() == CourseSection :: TYPE_DISABLED && $this->course->get_layout() > 2) ? 0 : $section->get_id();

                if (($section->get_visible() && (count($tools[$id]) > 0)) || $this->is_course_admin)
                {
                    $content = $this->display_block_header($section, $section->get_name());
                    $content .= $this->show_section_tools($section, $tools[$id]);
                    $content .= $this->display_block_footer($section);
                    $tabs->add_tab(new DynamicContentTab($section->get_id(), $section->get_name(), null, $content));
                }
            }
        }

        if (count($tabs->get_tabs()) > O)
        {
            echo $tabs->render();
        }
        else
        {
            echo '<div class="warning-message">' . Translation :: get('NoVisibleCourseSections') . '</div>';
        }

        echo '<script type="text/javascript" src="' . Path :: get(WEB_LIB_PATH) . 'libraries/resources/javascript/home_ajax.js' . '"></script>';
        echo '<script type="text/javascript" src="' . Path :: get(WEB_APP_PATH) . 'weblcms/resources/javascript/course_home.js' . '"></script>';
    }

    private function get_publication_links()
    {
        if (! isset($this->publication_links))
        {
            $parent = $this->get_parent();

            $conditions = array();
            $conditions[] = new EqualityCondition(ContentObjectPublication :: PROPERTY_COURSE_ID, $parent->get_course_id());
            $conditions[] = new EqualityCondition(ContentObjectPublication :: PROPERTY_SHOW_ON_HOMEPAGE, 1);
            $condition = new AndCondition($conditions);

            $this->publication_links = WeblcmsDataManager :: get_instance()->retrieve_content_object_publications($condition);
        }

        return $this->publication_links;
    }

    /**
     * Show the links to publications in this course
     */
    private function show_links($section)
    {
        $parent = $this->get_parent();
        $publications = $this->get_publication_links();

        $table = new HTML_Table('style="width: 100%;"');
        $table->setColCount($this->number_of_columns);
        $count = 0;

        if ($publications->size() == 0)
        {
            $html[] = '<div class="normal-message">' . Translation :: get('NoLinksAvailable') . '</div>';
        }

        while ($publication = $publications->next_result())
        {
            if ($publication->is_hidden() == 0)
            {
                $lcms_action = HomeTool :: ACTION_HIDE_PUBLICATION;
                $visible_image = 'action_visible.png';
                $tool_image = Theme :: ICON_MEDIUM . '.png';
                $link_class = '';
            }
            else
            {
                $lcms_action = HomeTool :: ACTION_SHOW_PUBLICATION;
                $visible_image = 'action_invisible.png';
                $tool_image = Theme :: ICON_MEDIUM . '_na.png';
                $link_class = ' class="invisible"';
            }

            $title = htmlspecialchars($publication->get_content_object()->get_title());
            $row = $count / $this->number_of_columns;
            $col = $count % $this->number_of_columns;
            $cell_contents = array();
            if ($parent->is_allowed(WeblcmsRights :: EDIT_RIGHT) || $publication->is_visible_for_target_users())
            {
                // Show visibility-icon
                if ($parent->is_allowed(WeblcmsRights :: EDIT_RIGHT))
                {
                    $cell_contents[] = '<a href="' . $parent->get_url(array(Tool :: PARAM_ACTION => $lcms_action, Tool :: PARAM_PUBLICATION_ID => $publication->get_id())) . '"><img src="' . Theme :: get_common_image_path() . $visible_image . '" style="vertical-align: middle;" alt=""/></a>';
                    $cell_contents[] = '<a href="' . $parent->get_url(array(Tool :: PARAM_ACTION => HomeTool :: ACTION_DELETE_LINKS, Tool :: PARAM_PUBLICATION_ID => $publication->get_id())) . '"><img src="' . Theme :: get_common_image_path() . 'action_delete.png" style="vertical-align: middle;" alt=""/></a>';
                    $cell_contents[] = '&nbsp;&nbsp;&nbsp;';
                }

                // Show tool-icon + name


                if ($publication->get_tool() == LinkTool :: TOOL_NAME)
                {
                    $url = $publication->get_content_object()->get_url();
                    $target = ' target="_blank"';
                }
                else
                {
                    $url = $parent->get_url(array('tool_action' => null, WeblcmsManager :: PARAM_COMPONENT_ACTION => null, WeblcmsManager :: PARAM_TOOL => $publication->get_tool(), Tool :: PARAM_PUBLICATION_ID => $publication->get_id()), array(), true);
                    $target = '';
                }

                $cell_contents[] = '<a href="' . $url . '"' . $target . $link_class . '>';
                $cell_contents[] = '<img src="' . Theme :: get_image_path(Tool :: get_tool_type_namespace($publication->get_tool())) . 'logo/' . $tool_image . '" style="vertical-align: middle;" alt="' . $title . '"/>';
                $cell_contents[] = '&nbsp;';
                $cell_contents[] = $title;
                $cell_contents[] = '</a>';

                $table->setCellContents($row, $col, implode("\n", $cell_contents));
                $table->updateColAttributes($col, 'style="width: ' . floor(100 / $this->number_of_columns) . '%;"');
                $count ++;
            }
        }

        $html[] = $table->toHtml();

        return implode("\n", $html);
    }

    function display_block_header($section, $block_name)
    {
        $html = array();

        if ($section->get_type() == CourseSection :: TYPE_TOOL)
        {
            $html[] = '<div class="toolblock" id="block_' . $section->get_id() . '" style="width:100%; height: 100%;">';
        }

        if ($section->get_type() == CourseSection :: TYPE_DISABLED)
        {
            $html[] = '<div class="disabledblock" id="block_' . $section->get_id() . '" style="width:100%; height: 100%;">';
        }

        return implode("\n", $html);
    }

    function display_block_footer($section)
    {
        $html = array();

        $html[] = '<div class="clear"></div>';

        if ($section->get_type() == CourseSection :: TYPE_TOOL || $section->get_type() == CourseSection :: TYPE_DISABLED)
        {
            $html[] = '</div>';
        }

        return implode("\n", $html);
    }

    private function show_section_tools($section, $tools)
    {
        $parent = $this->get_parent();

        $column_width = 99.9 / $this->number_of_columns;

        $count = 0;

        $html = array();

        if (count($tools) == 0)
        {
            $html[] = '<div class="normal-message">' . Translation :: get('NoToolsAvailable') . '</div>';
        }

        foreach ($tools as $index => $tool)
        {
            if ($tool->visible || $section->get_name() == 'course_admin')
            {
                $lcms_action = HomeTool :: ACTION_MAKE_TOOL_INVISIBLE;
                $visible_image = 'action_visible.png';
                $new = '';
                if ($parent->tool_has_new_publications($tool->name, $course))
                {
                    $new = '_new';
                }
                $tool_image = Theme :: ICON_MEDIUM . $new . '.png';
                $link_class = '';
            }
            else
            {
                $lcms_action = HomeTool :: ACTION_MAKE_TOOL_VISIBLE;
                $visible_image = 'action_invisible.png';
                $tool_image = Theme :: ICON_MEDIUM . '_na.png';
                $link_class = ' class="invisible"';
            }

            $title = htmlspecialchars(Translation :: get('TypeName', null, Tool :: get_tool_type_namespace($tool->name)));
            $row = $count / $this->number_of_columns;
            $col = $count % $this->number_of_columns;
            //$html = array();
            if ($this->is_course_admin || $tool->visible)
            {
                if ($section->get_type() == CourseSection :: TYPE_TOOL || $section->get_type() == CourseSection :: TYPE_DISABLED)
                {
                    $html[] = '<div id="tool_' . $tool->id . '" class="tool" style="width: ' . $column_width . '%;">';
                    $id = 'id="drag_' . $tool->id . '"';
                }
                else
                {
                    $html[] = '<div class="tool" style="width: ' . $column_width . '%;">';
                }

                // Show visibility-icon
                if ($this->is_course_admin && $section->get_type() != CourseSection :: TYPE_ADMIN)
                {
                    $html[] = '<a href="' . $parent->get_url(array(HomeTool :: PARAM_ACTION => $lcms_action, HomeTool :: PARAM_TOOL => $tool->name)) . '"><img class="tool_visible" src="' . Theme :: get_common_image_path() . $visible_image . '" style="vertical-align: middle;" alt=""/></a>';
                    $html[] = '&nbsp;&nbsp;&nbsp;';
                }

                // Show tool-icon + name


                $html[] = '<img class="tool_image"' . $id . ' src="' . Theme :: get_image_path(Tool :: get_tool_type_namespace($tool->name)) . 'logo/' . $tool_image . '" style="vertical-align: middle;" alt="' . $title . '"/>';
                $html[] = '&nbsp;';
                $html[] = '<a id="tool_text" href="' . $parent->get_url(array(WeblcmsManager :: PARAM_COMPONENT_ACTION => null, WeblcmsManager :: PARAM_TOOL => $tool->name, 'tool_action' => null, Tool :: PARAM_BROWSER_TYPE => null), array(), true) . '" ' . $link_class . '>';
                $html[] = $title;
                $html[] = '</a>';

                $html[] = '<div class="clear"></div>';

                $html[] = '</div>';

                $count ++;
            }
        }
        //$table->display();


        $html[] = ' ';

        return implode("\n", $html);
    }
}
?>