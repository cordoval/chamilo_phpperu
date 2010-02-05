<?php
/**
 * $Id: course_sections_change_section.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.course_sections.component
 */
require_once dirname(__FILE__) . '/../course_sections_tool.class.php';
require_once dirname(__FILE__) . '/../course_sections_tool_component.class.php';
require_once dirname(__FILE__) . '/../course_section_form.class.php';

class CourseSectionsToolChangeSectionComponent extends CourseSectionsToolComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $target = $_POST['target'];
        $source = $_POST['source'];

        $targets = split('_', $target);
        $target = $targets[1];

        $sources = split('_', $source);
        $source = $sources[1];

        $wdm = WeblcmsDataManager :: get_instance();
        $wdm->change_module_course_section($source, $target);

        $parent = $this->get_parent();

        foreach ($wdm->get_course_modules(Request :: get('course')) as $tool)
        {
            if ($this->group_inactive)
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
                    $tools[$tool->section][] = $tool;
            }
            else
            {
                $tools[$tool->section][] = $tool;
            }
        }

        $section = WeblcmsDataManager :: get_instance()->retrieve_course_sections(new EqualityCondition('id', $target))->next_result();

        //dump($tools);
        $this->show_section_tools($section, $tools[$section->get_id()]);
    }

    private function show_section_tools($section, $tools)
    {
        $parent = $this->get_parent();

        $is_course_admin = $parent->get_course()->is_course_admin($parent->get_user());
        $course = $parent->get_course();
        $number_of_columns = ($course->get_layout() % 2 == 0) ? 3 : 2;

        $table = new HTML_Table('style="width: 100%;"');
        $table->setColCount($number_of_columns);
        $count = 0;
        foreach ($tools as $index => $tool)
        { //dump($tool);
            if ($tool->visible || $section->get_name() == 'course_admin')
            {
                $lcms_action = 'make_invisible';
                $visible_image = 'action_visible.png';
                $new = '';
                if ($parent->tool_has_new_publications($tool->name))
                {
                    $new = '_new';
                }
                $tool_image = 'tool_' . $tool->name . $new . '.png';
                $link_class = '';
            }
            else
            {
                $lcms_action = 'make_visible';
                $visible_image = 'action_invisible.png';
                $tool_image = 'tool_' . $tool->name . '_na.png';
                $link_class = ' class="invisible"';
            }
            $title = htmlspecialchars(Translation :: get(Tool :: type_to_class($tool->name) . 'Title'));
            $row = $count / $number_of_columns;
            $col = $count % $number_of_columns;
            $html = array();
            if ($is_course_admin || $tool->visible)
            {
                if ($section->get_type() == CourseSection :: TYPE_TOOL)
                {
                    $html[] = '<div id="tool_' . $tool->id . '" class="tool" style="display:inline">';
                    //$html[] = '<div id="drag_' . $tool->id . '" class="tooldrag" style="width: 20px; cursor: pointer; display:none;"><img src="'. Theme :: get_common_image_path() .'action_drag.png" alt="'. Translation :: get('DragAndDrop') .'" title="'. Translation :: get('DragAndDrop') .'" /></div>';
                    $id = 'id="drag_' . $tool->id . '"';
                }

                // Show visibility-icon
                if ($is_course_admin && $section->get_name() != 'course_admin')
                {
                    $html[] = '<a href="' . $parent->get_url(array(WeblcmsManager :: PARAM_COMPONENT_ACTION => $lcms_action, WeblcmsManager :: PARAM_TOOL => $tool->name)) . '"><img src="' . Theme :: get_common_image_path() . $visible_image . '" style="vertical-align: middle;" alt=""/></a>';
                    $html[] = '&nbsp;&nbsp;&nbsp;';
                }

                // Show tool-icon + name


                $html[] = '<img ' . $id . ' src="' . Theme :: get_image_path() . $tool_image . '" style="vertical-align: middle;" alt="' . $title . '"/>';
                $html[] = '&nbsp;';
                $html[] = '<a href="' . $parent->get_url(array(WeblcmsManager :: PARAM_COMPONENT_ACTION => null, WeblcmsManager :: PARAM_TOOL => $tool->name), true) . '" ' . $link_class . '>';
                $html[] = $title;
                $html[] = '</a>';
                if ($section->get_type() == CourseSection :: TYPE_TOOL)
                {
                    $html[] = '</div>';
                    $html[] = '<script type="text/javascript">$("#tool_' . $tool->id . '").draggable({ handle: "div", revert: true, helper: "original"});</script>';
                }

                $table->setCellContents($row, $col, implode("\n", $html));
                $table->updateColAttributes($col, 'style="width: ' . floor(100 / $number_of_columns) . '%;"');
                $count ++;
            }
        }
        $table->display();
    }
}
?>