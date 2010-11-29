<?php
namespace repository\content_object\survey;

use repository\RepositoryDataManager;
use common\libraries\Path;
use common\libraries\BreadcrumbTrail;
use common\libraries\Breadcrumb;
use common\libraries\EqualityCondition;
use repository\ComplexContentObjectItem;
use common\libraries\Translation;
use common\libraries\Request;

//require_once Path :: get_repository_content_object_path() . '/survey/php_page/survey_page.class.php';
require_once dirname(__FILE__) . '/page_question_browser/question_browser_table.class.php';
require_once dirname(__FILE__) . '/../forms/configure_question_form.class.php';

class SurveyBuilderConfigureQuestionComponent extends SurveyBuilder
{

    private $page_id;

    function run()
    {

        $complex_item_id = Request :: get(SurveyBuilder :: PARAM_COMPLEX_QUESTION_ITEM_ID);
        $complex_item = RepositoryDataManager :: get_instance()->retrieve_complex_content_object_item($complex_item_id);
        $this->page_id = $complex_item->get_parent();

        $form = new ConfigureQuestionForm(ConfigureQuestionForm :: TYPE_CREATE, $complex_item, $this->get_url(array(SurveyBuilder :: PARAM_SURVEY_PAGE_ID => $this->page_id, SurveyBuilder :: PARAM_COMPLEX_QUESTION_ITEM_ID => $complex_item_id)), $this->page_id);

        if ($form->validate())
        {
            $form->create_config();
            $this->redirect(Translation :: get('QuestionConfigurationCreated'), (false), array(SurveyBuilder :: PARAM_BUILDER_ACTION => SurveyBuilder :: ACTION_CONFIGURE_PAGE, SurveyBuilder :: PARAM_SURVEY_PAGE_ID => $this->page_id));

        }
        else
        {
            $this->display_header();
            $form->display();
            $this->display_footer();
        }
    }

    private function get_table()
    {
        $parameters = $this->get_parameters();
        $table = new SurveyPageQuestionBrowserTable($this, $parameters, $this->get_condition());
        return $table->as_html();
    }

    function get_condition()
    {

        $page_id = Request :: get(SurveyBuilder :: PARAM_SURVEY_PAGE_ID);
        $condition = new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $page_id, ComplexContentObjectItem :: get_table_name());
        return $condition;
    }

    function get_complex_content_object_table_html($show_subitems_column = true, $model = null, $renderer = null)
    {
        return parent :: get_complex_content_object_table_html($show_subitems_column, $model, new SurveyBrowserTableCellRenderer($this, $this->get_complex_content_object_table_condition()));
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(self :: PARAM_BUILDER_ACTION => self :: ACTION_BROWSE)), Translation :: get('BrowseSurvey')));
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(self :: PARAM_BUILDER_ACTION => self :: ACTION_CONFIGURE_PAGE, self :: PARAM_SURVEY_PAGE_ID => Request :: get(self :: PARAM_SURVEY_PAGE_ID))), Translation :: get('ConfigurePage')));

    }

    function get_additional_parameters()
    {
        return array(self :: PARAM_SURVEY_PAGE_ID, self :: PARAM_COMPLEX_QUESTION_ITEM_ID);
    }

}

?>