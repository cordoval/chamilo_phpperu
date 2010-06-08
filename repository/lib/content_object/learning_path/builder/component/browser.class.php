<?php
/**
 * $Id: browser.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_builder.learning_path.component
 */
require_once dirname(__FILE__) . '/browser/learning_path_browser_table_cell_renderer.class.php';

class LearningPathBuilderBrowserComponent extends LearningPathBuilder
{

    function run()
    {
        $menu_trail = $this->get_complex_content_object_breadcrumbs();
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add_help('repository learnpath builder');

        if ($this->get_complex_content_object_item())
        {
            $content_object = RepositoryDataManager :: get_instance()->retrieve_content_object($this->get_complex_content_object_item()->get_ref());
        }
        else
        {
            $content_object = $this->get_root_content_object();
        }

        $this->display_header($trail);
        $action_bar = $this->get_action_bar($content_object);

        if ($action_bar)
        {
            echo '<br />';
            echo $action_bar->as_html();
        }

        //echo $this->get_object_info();


        if ($content_object->get_version() == 'chamilo')
        {
            echo '<br />';
            $types = array(LearningPath :: get_type_name(), Announcement :: get_type_name(), Assessment :: get_type_name(), BlogItem :: get_type_name(), CalendarEvent :: get_type_name(),
            			  Description :: get_type_name(), Document :: get_type_name(), Forum :: get_type_name(), Glossary :: get_type_name(), Hotpotatoes :: get_type_name(), Link :: get_type_name(),
            			  Note :: get_type_name(), Wiki :: get_type_name());
            echo $this->get_creation_links($content_object, $types);
            echo '<div class="clear">&nbsp;</div><br />';
        }

        echo '<div style="width: 18%; overflow: auto; float: left;">';
        echo $this->get_complex_content_object_menu();
        echo '</div><div style="width: 80%; float: right;">';
        echo $this->get_complex_content_object_table_html(false, null, new LearningPathBrowserTableCellRenderer($this, $this->get_complex_content_object_table_condition()));
        echo '</div>';
        echo '<div class="clear">&nbsp;</div>';

        $this->display_footer();
    }

    function get_object_info()
    {
        $html = array();

        $content_object = $this->get_root_content_object();
        $display = ContentObjectDisplay :: factory($content_object);
        $content_object_display = $display->get_full_html();
        $check_empty = trim(strip_tags($content_object_display));

        if (! empty($check_empty) && $check_empty != $content_object->get_title())
        {
            $html[] = '<div class="complex_browser_display">';
            $html[] = $content_object_display;
            $html[] = '<div class="clear">&nbsp;</div>';
            $html[] = '</div>';
        }

        return implode("\n", $html);
    }

    function get_action_bar()
    {
        $pub = Request :: get('publish');

        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

        if ($pub && $pub != '')
        {
            $action_bar->add_common_action(new ToolbarItem(Translation :: get('Publish'), Theme :: get_common_image_path() . 'action_publish.png', $_SESSION['redirect_url']));
            return $action_bar;
        }
    }

    function get_creation_links($content_object, $types = array())
    {
        $html[] = '<div class="select_complex_element">';
        $html[] = '<span class="title">' . Theme :: get_common_image('place_content_objects') . Translation :: get('LearningPathAddContentObject') . '</span>';
        $html[] = '<div id="content_object_selection">';

        if (count($types) == 0)
        {
            $types = $content_object->get_allowed_types();
        }

        foreach ($types as $type)
        {
            if ($type == LearningPath :: get_type_name())
            {
                $url = $this->get_url(array(ComplexBuilder :: PARAM_BUILDER_ACTION => ComplexBuilder :: ACTION_CREATE_COMPLEX_CONTENT_OBJECT_ITEM, ComplexBuilder :: PARAM_TYPE => $type, ComplexBuilder :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $this->get_complex_content_object_item_id()));
            }
            else
            {
                $url = $this->get_url(array(ComplexBuilder :: PARAM_BUILDER_ACTION => LearningPathBuilder :: ACTION_CREATE_LP_ITEM, ComplexBuilder :: PARAM_TYPE => $type, ComplexBuilder :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $this->get_complex_content_object_item_id()));
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