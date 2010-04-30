<?php
/**
 * $Id: glossary_browser.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.glossary.component
 */
require_once dirname(__FILE__) . '/../glossary_tool.class.php';
require_once dirname(__FILE__) . '/../glossary_tool_component.class.php';
require_once Path :: get_repository_path() . 'lib/content_object/glossary/glossary.class.php';
require_once dirname(__FILE__) . '/../../../browser/object_publication_table/object_publication_table.class.php';
require_once dirname(__FILE__) . '/glossary_browser/glossary_cell_renderer.class.php';

class GlossaryToolBrowserComponent extends GlossaryToolComponent
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
        $conditions[] = new EqualityCondition(ContentObjectPublication :: PROPERTY_TOOL, 'glossary');

        $subselect_condition = new EqualityCondition(ContentObject :: PROPERTY_TYPE, Introduction :: get_type_name());
        $conditions[] = new SubselectCondition(ContentObjectPublication :: PROPERTY_CONTENT_OBJECT_ID, ContentObject :: PROPERTY_ID, ContentObject :: get_table_name(), $subselect_condition);
        $condition = new AndCondition($conditions);

        $publications = WeblcmsDataManager :: get_instance()->retrieve_content_object_publications_new($condition);
        $this->introduction_text = $publications->next_result();

        $this->action_bar = $this->get_action_bar();

        $trail = new BreadcrumbTrail();
        $trail->add_help('courses glossary tool');

        $this->display_header($trail, true);

        //echo '<br /><a name="top"></a>';
        //echo $this->perform_requested_actions();
        if (! Request :: get(Tool :: PARAM_PUBLICATION_ID))
        {
            if ($this->get_course()->get_intro_text())
            {
                echo $this->display_introduction_text($this->introduction_text);
            }
        }
        echo $this->action_bar->as_html();

        $table = new ObjectPublicationTable($this, $this->get_user(), array(Glossary :: get_type_name()), $this->get_condition(), new GlossaryCellRenderer($this));
        echo $table->as_html();

        $this->display_footer();
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

        if (! Request :: get(Tool :: PARAM_PUBLICATION_ID))
        {
            $action_bar->set_search_url($this->get_url());

            if ($this->is_allowed(ADD_RIGHT))
            {
                $action_bar->add_common_action(new ToolbarItem(Translation :: get('Publish'), Theme :: get_common_image_path() . 'action_publish.png', $this->get_url(array(GlossaryTool :: PARAM_ACTION => GlossaryTool :: ACTION_PUBLISH)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
            }
        }

        $action_bar->add_common_action(new ToolbarItem(Translation :: get('ShowAll'), Theme :: get_common_image_path() . 'action_browser.png', $this->get_url(array(Tool :: PARAM_ACTION => null)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));

        if (! $this->introduction_text && $this->get_course()->get_intro_text() && $this->is_allowed(EDIT_RIGHT))
        {
            $action_bar->add_common_action(new ToolbarItem(Translation :: get('PublishIntroductionText'), Theme :: get_common_image_path() . 'action_introduce.png', $this->get_url(array(AnnouncementTool :: PARAM_ACTION => Tool :: ACTION_PUBLISH_INTRODUCTION)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
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