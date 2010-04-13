<?php
/**
 * $Id: fixed_location_tool_list_renderer.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool_list_renderer
 */
require_once (dirname(__FILE__) . '/../tool_list_renderer.class.php');
require_once (dirname(__FILE__) . '/../course/course_section.class.php');
require_once ('HTML/Table.php');
/**
 * Tool list renderer which displays all course tools on a fixed location.
 * Disabled tools will be shown in a disabled looking way.
 */
class FixedLocationToolListRenderer extends ToolListRenderer
{
    private $number_of_columns = 2;
    private $group_inactive;
    private $is_course_admin;
    private $course;

    /**
     * Constructor
     * @param  WebLcms $parent The parent application
     */
    function FixedLocationToolListRenderer($parent)
    {
        parent :: ToolListRenderer($parent);
        $course = $parent->get_course();
        $this->course = $course;
        $this->number_of_columns = ($course->get_layout() % 2 == 0) ? 3 : 2;
        $this->group_inactive = ($course->get_layout() > 2);
        $this->is_course_admin = $this->get_parent()->is_allowed(EDIT_RIGHT);
    }

    // Inherited
    function display()
    {
        $parent = $this->get_parent();
        $tools = array();
        
        foreach ($parent->get_registered_tools() as $tool)
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
        
        //$section_types = $parent->get_registered_sections();
        //dump($section_types);
        

        /*foreach($section_types as $section_type => $sections)
		{
			if ($section_type == CourseSection :: TYPE_LINK)
			{
				$this->show_links();
			}
			else
			{
				if($section_type == CourseSection :: TYPE_DISABLED && $this->course->get_layout() < 3)
					continue;

				foreach($sections as $section)
				{
					$id = ($section_type == CourseSection :: TYPE_DISABLED && $this->course->get_layout() > 2)?0:$section->id;

					if((count($tools[$id]) > 0 && $section->visible) || $this->is_course_admin)
					{
						echo $this->display_block_header($section->id, $section->name);
						$this->show_section_tools($section, $tools[$id]);
						echo $this->display_block_footer();
					}
				}
			}
		}*/
        
        echo '<div id="coursecode" style="display: none;">' . $this->course->get_id() . '</div>';
        
        $sections = WeblcmsDataManager :: get_instance()->retrieve_course_sections(new EqualityCondition('course_id', $this->course->get_id()));
        while ($section = $sections->next_result())
        {
            if ($section->get_type() == CourseSection :: TYPE_LINK)
            {
                $this->show_links($section);
            }
            else
            {
                if ($section->get_type() == CourseSection :: TYPE_DISABLED && ($this->course->get_layout() < 3 || !$this->is_course_admin))
                    continue;
                
                if ($section->get_type() == CourseSection :: TYPE_ADMIN && ! $this->is_course_admin)
                    continue;
                
                $id = ($section->get_type() == CourseSection :: TYPE_DISABLED && $this->course->get_layout() > 2) ? 0 : $section->get_id();
                
                if ($section->get_visible() && (count($tools[$id]) > 0 || $this->is_course_admin))
                {
                    echo $this->display_block_header($section, $section->get_name());
                    $this->show_section_tools($section, $tools[$id]);
                    echo $this->display_block_footer($section);
                }
            }
        }
        
        echo '<script type="text/javascript" src="' . Path :: get(WEB_LIB_PATH) . 'javascript/home_ajax.js' . '"></script>';
        echo '<script type="text/javascript" src="' . Path :: get(WEB_LIB_PATH) . 'javascript/course_home.js' . '"></script>';
    }

    /**
     * Show the links to publications in this course
     */
    private function show_links($section)
    {
        $parent = $this->get_parent();
        
        $conditions = array();
        $conditions[] = new EqualityCondition(ContentObjectPublication :: PROPERTY_COURSE_ID, $parent->get_course_id());
        $conditions[] = new EqualityCondition(ContentObjectPublication :: PROPERTY_SHOW_ON_HOMEPAGE, 1);
        $condition = new AndCondition($conditions);
        
        $publications = WeblcmsDataManager :: get_instance()->retrieve_content_object_publications_new($condition);
        
        if ($publications->size() > 0)
        {
            echo $this->display_block_header($section, Translation :: get('Links'));
        }
        
        $table = new HTML_Table('style="width: 100%;"');
        $table->setColCount($this->number_of_columns);
        $count = 0;
        while ($publication = $publications->next_result())
        {
            if ($publication->is_visible_for_target_users())
            {
                $lcms_action = 'make_publication_invisible';
                $visible_image = 'action_visible.png';
                $tool_image = 'tool_' . $publication->get_tool() . '.png';
                $link_class = '';
            }
            else
            {
                $lcms_action = 'make_publication_visible';
                $visible_image = 'action_invisible.png';
                $tool_image = 'tool_' . $publication->get_tool() . '_na.png';
                $link_class = ' class="invisible"';
            }
            $title = htmlspecialchars($publication->get_content_object()->get_title());
            $row = $count / $this->number_of_columns;
            $col = $count % $this->number_of_columns;
            $html = array();
            if ($parent->is_allowed(EDIT_RIGHT) || $publication->is_visible_for_target_users())
            {
                // Show visibility-icon
                if ($parent->is_allowed(EDIT_RIGHT))
                {
                    $html[] = '<a href="' . $parent->get_url(array(WeblcmsManager :: PARAM_COMPONENT_ACTION => $lcms_action, Tool :: PARAM_PUBLICATION_ID => $publication->get_id(), 'tool_action' => null)) . '"><img src="' . Theme :: get_common_image_path() . $visible_image . '" style="vertical-align: middle;" alt=""/></a>';
                    $html[] = '<a href="' . $parent->get_url(array(WeblcmsManager :: PARAM_COMPONENT_ACTION => 'delete_publication', Tool :: PARAM_PUBLICATION_ID => $publication->get_id(), 'tool_action' => null)) . '"><img src="' . Theme :: get_common_image_path() . 'action_delete.png" style="vertical-align: middle;" alt=""/></a>';
                    $html[] = '&nbsp;&nbsp;&nbsp;';
                }
                
                // Show tool-icon + name
                $html[] = '<a href="' . $parent->get_url(array('tool_action' => null, WeblcmsManager :: PARAM_COMPONENT_ACTION => null, WeblcmsManager :: PARAM_TOOL => $publication->get_tool(), Tool :: PARAM_PUBLICATION_ID => $publication->get_id()), array(), true) . '" ' . $link_class . '>';
                $html[] = '<img src="' . Theme :: get_image_path() . $tool_image . '" style="vertical-align: middle;" alt="' . $title . '"/>';
                $html[] = '&nbsp;';
                $html[] = $title;
                $html[] = '</a>';
                
                $table->setCellContents($row, $col, implode("\n", $html));
                $table->updateColAttributes($col, 'style="width: ' . floor(100 / $this->number_of_columns) . '%;"');
                $count ++;
            }
        }
        $table->display();
        
        if ($publications->size() > 0)
        {
            echo $this->display_block_footer($section);
        }
    }

    function display_block_header($section, $block_name)
    {
        $html = array();
        
        $icon = 'block_weblcms.png';
        
        if ($section->get_type() == CourseSection :: TYPE_ADMIN)
            $icon = 'block_admin.png';
        
        if ($section->get_type() == CourseSection :: TYPE_TOOL)
        {
            $html[] = '<div class="toolblock" id="block_' . $section->get_id() . '" style="width:100%; height: 100%;">';
        }
        
    	if ($section->get_type() == CourseSection :: TYPE_DISABLED)
        {
            $html[] = '<div class="disabledblock" id="block_' . $section->get_id() . '" style="width:100%; height: 100%;">';
        }
        
        $html[] = '<div class="block" id="block_' . $section->get_id() . '" style="background-image: url(' . Theme :: get_image_path('home') . $icon . ');">';
        
        $html[] = '<div class="title"><div style="float: left;">' . $block_name . '</div>';
        $html[] = '<a href="#" class="closeEl"><img class="visible" src="' . Theme :: get_common_image_path() . 'action_visible.png" /><img class="invisible" style="display: none;" src="' . Theme :: get_common_image_path() . 'action_invisible.png" /></a>';
        $html[] = '<div style="clear: both;"></div>';
        $html[] = '</div>';
        $html[] = '<div class="description">';
        
        return implode("\n", $html);
    }

    function display_block_footer($section)
    {
        $html = array();
        
        $html[] = '<div class="clear"></div>';
        $html[] = '</div>';
        $html[] = '</div>';
        
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
        
        //$table = new HTML_Table('style="width: 100%;"');
        //$table->setColCount($this->number_of_columns);
        $count = 0;
        
        $html = array();
        
        foreach ($tools as $index => $tool)
        {
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
            $row = $count / $this->number_of_columns;
            $col = $count % $this->number_of_columns;
            //$html = array();
            if ($this->is_course_admin || $tool->visible)
            {
                if ($section->get_type() == CourseSection :: TYPE_TOOL || $section->get_type() == CourseSection :: TYPE_DISABLED)
                {
                    $html[] = '<div id="tool_' . $tool->id . '" class="tool" style="width: ' . $column_width . '%;">';
                    //$html[] = '<div id="drag_' . $tool->id . '" class="tooldrag" style="width: 20px; cursor: pointer; display:none;"><img src="'. Theme :: get_common_image_path() .'action_drag.png" alt="'. Translation :: get('DragAndDrop') .'" title="'. Translation :: get('DragAndDrop') .'" /></div>';
                    $id = 'id="drag_' . $tool->id . '"';
                }
                else
                {
                    $html[] = '<div class="tool" style="width: ' . $column_width . '%;">';
                }
                
                // Show visibility-icon
                if ($this->is_course_admin && $section->get_type() != CourseSection :: TYPE_ADMIN)
                {
                    $html[] = '<a href="' . $parent->get_url(array(WeblcmsManager :: PARAM_COMPONENT_ACTION => $lcms_action, WeblcmsManager :: PARAM_TOOL => $tool->name, 'tool_action' => null)) . '"><img class="tool_visible" src="' . Theme :: get_common_image_path() . $visible_image . '" style="vertical-align: middle;" alt=""/></a>';
                    $html[] = '&nbsp;&nbsp;&nbsp;';
                }
                
                // Show tool-icon + name
                

                $html[] = '<img class="tool_image"' . $id . ' src="' . Theme :: get_image_path() . $tool_image . '" style="vertical-align: middle;" alt="' . $title . '"/>';
                $html[] = '&nbsp;';
                $html[] = '<a id="tool_text" href="' . $parent->get_url(array(WeblcmsManager :: PARAM_COMPONENT_ACTION => null, WeblcmsManager :: PARAM_TOOL => $tool->name, 'tool_action' => null), array(), true) . '" ' . $link_class . '>';
                $html[] = $title;
                $html[] = '</a>';
                
                $html[] = '<div class="clear"></div>';
                
                $html[] = '</div>';
                
                //$table->setCellContents($row,$col,implode("\n",$html));
                //$table->updateColAttributes($col,'style="width: '.floor(100/$this->number_of_columns).'%;"');
                $count ++;
            }
        }
        //$table->display();
        

        echo implode("\n", $html);
    }
}
?>