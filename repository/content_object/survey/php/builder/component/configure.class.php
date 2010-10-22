<?php namespace repository\content_object\survey;

//require_once Path :: get_repository_path() . '/lib/content_object/survey/survey.class.php';
require_once dirname ( __FILE__ ) . '/page_question_browser/question_browser_table.class.php';

class SurveyBuilderConfigureComponent extends SurveyBuilder {
	
	const VISIBLE_QUESTION_ID = 'visible_question_id';
	const INVISIBLE_QUESTION_ID = 'invisible_question_id';
	const ANSWERMATCH = 'answer_match';
	
	private $page_id;
	
	function run() {
		
		$this->page_id = Request::get ( SurveyBuilder::PARAM_SURVEY_PAGE_ID );
		
//		$menu_trail = $this->get_complex_content_object_breadcrumbs ();
//		$trail = BreadcrumbTrail :: get_instance ( false );
//		$trail->merge ( $menu_trail );
//		$trail->add_help ( 'repository survey_page component configurer' );
//		$trail->add ( new Breadcrumb ( $this->get_url ( array (SurveyBuilder::PARAM_BUILDER_ACTION => SurveyBuilder::ACTION_CONFIGURE_PAGE, SurveyBuilder::PARAM_SURVEY_PAGE_ID =>$this->page_id ) ), Translation::get ( 'SurveyPageConfigure' ) ) );
//		
		$this->action_bar = $this->get_action_bar ();
		$this->display_header ();
		
		echo $this->action_bar->as_html ();
		echo '<div id="action_bar_browser">';
		echo '<div >';
		echo $this->get_table ();
		echo '</div>';
		echo '</div>';
		$this->display_footer ();
	}
	
	private function get_table() {
		$parameters = $this->get_parameters ();
		$table = new SurveyPageQuestionBrowserTable ( $this, $parameters, $this->get_condition () );
		return $table->as_html ();
	}
	
	function get_condition() {
		
		$page_id = Request::get ( SurveyBuilder::PARAM_SURVEY_PAGE_ID );
		$condition = new EqualityCondition ( ComplexContentObjectItem::PROPERTY_PARENT, $page_id, ComplexContentObjectItem::get_table_name () );
		return $condition;
	}
	
	function get_action_bar() {
		$action_bar = new ActionBarRenderer ( ActionBarRenderer::TYPE_HORIZONTAL );
		$parameters = $this->get_parameters ();
//		$action_bar->set_search_url ( $this->get_url ( $parameters ) );
		return $action_bar;
	}

  function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(self :: PARAM_BUILDER_ACTION => self :: ACTION_BROWSE)), Translation :: get('BrowseSurvey')));
        
    }

    function get_additional_parameters()
    {
        return array(self :: PARAM_SURVEY_PAGE_ID);
    }
	
}

?>