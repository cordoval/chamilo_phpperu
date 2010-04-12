<?php
/**
 * $Id: assessment_viewer.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.assessment.component
 */

require_once dirname(__FILE__) . '/../../../browser/learningobjectpublicationcategorytree.class.php';
require_once dirname(__FILE__) . '/../../../browser/object_publication_table/object_publication_table.class.php';
require_once dirname(__FILE__) . '/assessment_browser/assessment_cell_renderer.class.php';
require_once dirname(__FILE__) . '/assessment_browser/assessment_column_model.class.php';

/**
 * Represents the view component for the assessment tool.
 *
 */
class AssessmentToolViewerComponent extends AssessmentToolComponent
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
        $conditions[] = new EqualityCondition(ContentObjectPublication :: PROPERTY_TOOL, 'assessment');

        $subselect_condition = new EqualityCondition('type', 'introduction');
        $conditions[] = new SubselectCondition(ContentObjectPublication :: PROPERTY_CONTENT_OBJECT_ID, ContentObject :: PROPERTY_ID, RepositoryDataManager :: get_instance()->escape_table_name(ContentObject :: get_table_name()), $subselect_condition, ContentObjectPublication :: get_table_name());
        $condition = new AndCondition($conditions);

        $publications = WeblcmsDataManager :: get_instance()->retrieve_content_object_publications_new($condition);
        $this->introduction_text = $publications->next_result();

        $tree_id = WeblcmsManager :: PARAM_CATEGORY;
        $tree = new ContentObjectPublicationCategoryTree($this, $tree_id);
        $this->set_parameter($tree_id, Request :: get($tree_id));

        $trail = new BreadcrumbTrail();
        $trail->add_help('courses assessment tool');
        $this->display_header($trail, true);

        $this->action_bar = $this->get_toolbar(true);

        if ($this->get_course()->get_intro_text())
        {
            echo $this->display_introduction_text($this->introduction_text);
        }

        echo $this->action_bar->as_html();

        echo '<div style="width:18%; float: left; overflow: auto;">';

        echo $tree->as_html();

        echo '</div>';
        echo '<div style="width:80%; padding-left: 1%; float:right; ">';
        //$table = new AssessmentPublicationTable($this, $this->get_user(), array('assessment', 'survey', 'hotpotatoes'), null);
        $table = new ObjectPublicationTable($this, $this->get_user(), array('assessment', 'survey', 'hotpotatoes'), $this->get_condition(), new AssessmentCellRenderer($this), new AssessmentColumnModel());
        echo $table->as_html();

        echo '</div>';

        $this->display_footer();
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

    function get_toolbar($search)
    {
        $bar = parent :: get_toolbar($search);
        if ($this->is_allowed(EDIT_RIGHT))
        {
            $bar->add_common_action(new ToolbarItem(Translation :: get('ManageCategories'), Theme :: get_common_image_path() . 'action_category.png', $this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_MANAGE_CATEGORIES)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));

            if (! $this->introduction_text && $this->get_course()->get_intro_text())
            {
                $bar->add_common_action(new ToolbarItem(Translation :: get('PublishIntroductionText'), Theme :: get_common_image_path() . 'action_introduce.png', $this->get_url(array(AnnouncementTool :: PARAM_ACTION => Tool :: ACTION_PUBLISH_INTRODUCTION)), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
            }
        }
        return $bar;
    }
}

?>