<?php
/**
 * $Id: learning_path_browser.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.learning_path.component
 */
require_once dirname(__FILE__) . '/learning_path_browser/learning_path_cell_renderer.class.php';
require_once dirname(__FILE__) . '/learning_path_browser/learning_path_column_model.class.php';
require_once dirname(__FILE__) . '/../../../browser/object_publication_table/object_publication_table.class.php';

class LearningPathToolBrowserComponent extends LearningPathToolComponent
{
    private $action_bar;

    function run()
    {
        if (! $this->is_allowed(WeblcmsRights :: VIEW_RIGHT))
        {
            Display :: not_allowed();
            return;
        }

        $conditions = array();
        $conditions[] = new EqualityCondition(ContentObjectPublication :: PROPERTY_COURSE_ID, $this->get_course_id());
        $conditions[] = new EqualityCondition(ContentObjectPublication :: PROPERTY_TOOL, 'learning_path');

        $subselect_condition = new EqualityCondition(ContentObject :: PROPERTY_TYPE, Introduction :: get_type_name());
        $conditions[] = new SubselectCondition(ContentObjectPublication :: PROPERTY_CONTENT_OBJECT_ID, ContentObject :: PROPERTY_ID, ContentObject :: get_table_name(), $subselect_condition, null, RepositoryDataManager :: get_instance());
        $condition = new AndCondition($conditions);

        $publications = WeblcmsDataManager :: get_instance()->retrieve_content_object_publications($condition);
        $this->introduction_text = $publications->next_result();

        $this->action_bar = $this->get_toolbar();

        $trail = BreadcrumbTrail :: get_instance();
        $trail->add_help('courses learnpath tool');
        $this->display_header();

        if ($this->get_course()->get_intro_text())
        {
            echo $this->display_introduction_text($this->introduction_text);
        }

        echo $this->action_bar->as_html();
        //$table = new LearningPathPublicationTable($this, $this->get_user(), array('learning_path'), null);
        $table = new ObjectPublicationTable($this, $this->get_user(), array(LearningPath :: get_type_name()), $this->get_condition(), new LearningPathCellRenderer($this), new LearningPathColumnModel());
        echo $table->as_html();

        $this->display_footer();
    }

    function get_toolbar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

        $action_bar->set_search_url($this->get_url());

        if ($this->is_allowed(WeblcmsRights :: ADD_RIGHT))
        {
            $action_bar->add_common_action(new ToolbarItem(Translation :: get('Publish'), Theme :: get_common_image_path() . 'action_publish.png', $this->get_url(array(LearningPathTool :: PARAM_ACTION => LearningPathTool :: ACTION_PUBLISH)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        }

        $action_bar->add_common_action(new ToolbarItem(Translation :: get('Browse'), Theme :: get_common_image_path() . 'action_browser.png', $this->get_url(array(LearningPathTool :: PARAM_ACTION => LearningPathTool :: ACTION_BROWSE_LEARNING_PATHS)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));

        if (! $this->introduction_text && $this->get_course()->get_intro_text() && $this->is_allowed(WeblcmsRights :: EDIT_RIGHT))
        {
            $action_bar->add_common_action(new ToolbarItem(Translation :: get('PublishIntroductionText'), Theme :: get_common_image_path() . 'action_introduce.png', $this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_PUBLISH_INTRODUCTION)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        }

        if ($this->is_allowed(WeblcmsRights :: ADD_RIGHT))
        {
            $action_bar->add_tool_action(new ToolbarItem(Translation :: get('ImportScorm'), Theme :: get_common_image_path() . 'action_import.png', $this->get_url(array(LearningPathTool :: PARAM_ACTION => LearningPathTool :: ACTION_IMPORT_SCORM)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
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