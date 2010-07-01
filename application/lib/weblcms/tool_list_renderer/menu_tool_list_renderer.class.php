<?php
/**
 * $Id: menu_tool_list_renderer.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool_list_renderer
 */
require_once (dirname(__FILE__) . '/../tool_list_renderer.class.php');
require_once ('HTML/Table.php');
/**
 * Tool list renderer to display a navigation menu.
 */
class MenuToolListRenderer extends ToolListRenderer
{
    private $is_course_admin;
    private $menu_properties;

    /**
     * Constructor
     * @param  WebLcms $parent The parent application
     */
    function MenuToolListRenderer($parent, $visible_tools)
    {
        parent :: ToolListRenderer($parent, $visible_tools);
        $this->is_course_admin = $this->get_parent()->is_allowed(EDIT_RIGHT);
        $this->menu_properties = $this->load_menu_properties();
    }

    // Inherited
    function display()
    {
        $parent = $this->get_parent();
        $this->show_tools($this->get_visible_tools());
    }

    /**
     * Show the tools of a given section
     * @param array $tools
     */
    private function show_tools($tools)
    {
        $parent = $this->get_parent();
        $course = $parent->get_course();

        $menu_style = $this->get_menu_style();

        $html[] = '<div id="tool_bar" class="tool_bar tool_bar_' . ($this->display_menu_icons() && ! $this->display_menu_text() ? 'icon_' : '') . $menu_style . '">';

        if ($this->get_menu_style() == 'right')
        {
            $html[] = '<div id="tool_bar_hide_container" class="hide">';
            $html[] = '<a id="tool_bar_hide" href="#"><img src="' . Theme :: get_common_image_path() . 'action_action_bar_' . $menu_style . '_hide.png" /></a>';
            $html[] = '<a id="tool_bar_show" href="#"><img src="' . Theme :: get_common_image_path() . 'action_action_bar_' . $menu_style . '_show.png" /></a>';
            $html[] = '</div>';
        }

        $html[] = '<div class="tool_menu">';
        $html[] = '<ul>';

        $show_search = false;
        
        foreach ($tools as $index => $tool)
        {
            $sections = WeblcmsDataManager :: get_instance()->retrieve_course_sections(new EqualityCondition('id', $tool->section));
            $section = $sections->next_result();
            $section = WeblcmsDataManager :: get_instance()->retrieve_course_sections(new EqualityCondition('id', $tool->section))->next_result();

            if ($section->get_type() == CourseSection :: TYPE_ADMIN)
            {
                $admin_tools[] = $tool;
                continue;
            }

            if($tool->name == 'search')
            {
              	$show_search = true;
            }
                
            $html[] = $this->display_tool($tool);
        }
        
        if (count($admin_tools) && $this->is_course_admin)
        {
            $html[] = '<div style="margin: 10px 0 10px 0; border-bottom: 1px dotted #4271B5; height: 0px;"></div>';
            foreach ($admin_tools as $tool)
            {
                $html[] = $this->display_tool($tool);
            }
        }
        $html[] = '</ul>';

        if ($this->display_menu_text() && $show_search)
        {
            $html[] = '<div style="margin: 10px 0 10px 0; border-bottom: 1px dotted #4271B5; height: 0px; text-align: center;"></div>';

            $form = new FormValidator('search_simple', 'post', $parent->get_url(array('tool' => 'search')), '', array('style' => 'text-align: center;'), false);
            $renderer = clone $form->defaultRenderer();
            $renderer->setFormTemplate('<form {attributes}>{content}</form>');
            $renderer->setElementTemplate('{element}<br />');
            $form->addElement('text', 'query', '', 'size="18" class="search_query_no_icon" style="background-color: white; border: 1px solid grey; height: 18px; margin-bottom: 10px;"');
            $form->addElement('style_submit_button', 'submit', Translation :: get('Search'), array('class' => 'normal search'));
            $form->accept($renderer);
            $html[] = $renderer->toHtml();
        }

        $html[] = '</div>';
        $html[] = '<div class="clear">&nbsp;</div>';

        if ($this->get_menu_style() == 'left')
        {
            $html[] = '<div id="tool_bar_hide_container" class="hide">';
            $html[] = '<a id="tool_bar_hide" href="#"><img src="' . Theme :: get_common_image_path() . 'action_action_bar_' . $menu_style . '_hide.png" /></a>';
            $html[] = '<a id="tool_bar_show" href="#"><img src="' . Theme :: get_common_image_path() . 'action_action_bar_' . $menu_style . '_show.png" /></a>';
            $html[] = '</div>';
        }

        $html[] = '</div>';
        $html[] = '<script type="text/javascript" src="' . Path :: get(WEB_LIB_PATH) . 'javascript/tool_bar.js' . '"></script>';

        if ($_SESSION['toolbar_state'] == 'hide')
        {
            $html[] = '<script type="text/javascript">var hide = "true";</script>';
        }
        else
        {
            $html[] = '<script type="text/javascript">var hide = "false";</script>';
        }

        $html[] = '<div class="clear">&nbsp;</div>';

        echo implode("\n", $html);
    }

    function display_tool($tool)
    {
        $parent = $this->get_parent();
        $course = $parent->get_course();

        $new = '';
        if ($parent->tool_has_new_publications($tool->name))
        {
            $new = '_new';
        }
        $tool_image = 'tool_mini_' . $tool->name . $new . '.png';
        $title = htmlspecialchars(Translation :: get(Tool :: type_to_class($tool->name) . 'Title'));
        $html[] = '<li class="tool_list_menu" style="padding: 0px 0px 2px 0px;">';
        $html[] = '<a href="' . $parent->get_url(array(Application :: PARAM_ACTION => WeblcmsManager :: ACTION_VIEW_COURSE, WeblcmsManager :: PARAM_TOOL => $tool->name, Tool :: PARAM_ACTION => null, Tool :: PARAM_PUBLICATION_ID => null), array(), true) . '" title="' . $title . '">';

        if ($this->display_menu_icons())
        {
            $html[] = '<img src="' . Theme :: get_image_path() . $tool_image . '" style="vertical-align: middle;" alt="' . $title . '"/> ';
        }

        if ($this->display_menu_text())
        {
            $html[] = $title;
        }

        $html[] = '</a>';
        $html[] = '</li>';

        return implode("\n", $html);
    }

    function load_menu_properties()
    {
        $menu_style = $this->get_parent()->get_course()->get_menu();

        $properties = array();

        switch ($menu_style)
        {
            case CourseLayout :: MENU_LEFT_ICON :
                $properties['style'] = 'left';
                $properties['icons'] = true;
                $properties['text'] = false;
                break;
            case CourseLayout :: MENU_LEFT_ICON_TEXT :
                $properties['style'] = 'left';
                $properties['icons'] = true;
                $properties['text'] = true;
                break;
            case CourseLayout :: MENU_LEFT_TEXT :
                $properties['style'] = 'left';
                $properties['icons'] = false;
                $properties['text'] = true;
                break;

            case CourseLayout :: MENU_RIGHT_ICON :
                $properties['style'] = 'right';
                $properties['icons'] = true;
                $properties['text'] = false;
                break;
            case CourseLayout :: MENU_RIGHT_ICON_TEXT :
                $properties['style'] = 'right';
                $properties['icons'] = true;
                $properties['text'] = true;
                break;
            case CourseLayout :: MENU_RIGHT_TEXT :
                $properties['style'] = 'right';
                $properties['icons'] = false;
                $properties['text'] = true;
                break;

            default :
                $properties['style'] = 'left';
                $properties['icons'] = true;
                $properties['text'] = true;
                break;
        }

        return $properties;
    }

    function get_menu_properties()
    {
        return $this->menu_properties;
    }

    function get_menu_style()
    {
        $properties = $this->get_menu_properties();
        return $properties['style'];
    }

    function display_menu_icons()
    {
        $properties = $this->get_menu_properties();
        return $properties['icons'];
    }

    function display_menu_text()
    {
        $properties = $this->get_menu_properties();
        return $properties['text'];
    }
}
?>