<?php

require_once Path::get_repository_path () . 'lib/content_object/survey_page/survey_page.class.php';
require_once dirname ( __FILE__ ) . '/page_question_browser/question_browser_table.class.php';
require_once dirname ( __FILE__ ) . '/../forms/configure_question_form.class.php';

class SurveyBuilderConfigureQuestionComponent extends SurveyBuilder {
	
		
	private $page_id;
	
	function run() {
		
		$complex_item_id = Request::get ( SurveyBuilder::PARAM_COMPLEX_QUESTION_ITEM );
		$complex_item = RepositoryDataManager::get_instance ()->retrieve_complex_content_object_item ( $complex_item_id );
		$this->page_id = $complex_item->get_parent ();
		
		$menu_trail = $this->get_complex_content_object_breadcrumbs ();
		$trail = new BreadcrumbTrail ( false );
		$trail->merge ( $menu_trail );
		$trail->add_help ( 'repository survey_page question configurer' );
		$trail->add ( new Breadcrumb ( $this->get_url ( array (SurveyBuilder::PARAM_BUILDER_ACTION => SurveyBuilder::ACTION_CONFIGURE_PAGE, SurveyBuilder::PARAM_SURVEY_PAGE_ID => $this->page_id ) ), Translation::get ( 'SurveyPageConfigure' ) ) );
		$trail->add ( new Breadcrumb ( $this->get_url ( array (SurveyBuilder::PARAM_BUILDER_ACTION => SurveyBuilder::ACTION_CONFIGURE_QUESTION,  SurveyBuilder::PARAM_COMPLEX_QUESTION_ITEM => $complex_item_id ) ), Translation::get ( 'SurveyQuestionConfigure' ) ) );
				
		$form = new ConfigureQuestionForm ( ConfigureQuestionForm::TYPE_CREATE, $complex_item, $this->get_url ( array (SurveyBuilder::PARAM_SURVEY_PAGE_ID => $this->page_id, SurveyBuilder::PARAM_COMPLEX_QUESTION_ITEM => $complex_item_id ) ), $this->page_id );
		
		if ($form->validate ()) {
			$form->create_config ();
			$this->redirect ( Translation::get ( 'QuestionConfigurationCreated' ), (false), array (SurveyBuilder::PARAM_BUILDER_ACTION => SurveyBuilder::ACTION_CONFIGURE_PAGE, SurveyBuilder::PARAM_SURVEY_PAGE_ID => $this->page_id ) );

		} else {
			$this->display_header ( $trail, false );
			$form->display ();
			$this->display_footer ();
		}
	}
	
	private function get_table() {
		$parameters = $this->get_parameters ();
		$table = new SurveyPageQuestionBrowserTable ( $this, $parameters, $this->get_condition () );
		return $table->as_html ();
	}
	
	function get_condition() {
		
		$page_id = Request::get ( SurveyBuilder::PARAM_SURVEY_PAGE_ID );
		
		$condition = new EqualityCondition ( ComplexContentObjectItem::PROPERTY_PARENT, $page_id, ComplexContentObjectItem::get_table_name () );
		
		//		$survey = SurveyDataManager::get_instance()->retrieve_survey_publication($survey_pub_id);
		//		$excluded_users = $survey->get_excluded_participants();
		//		$condition = null;
		//		if(count($excluded_users) > 0){
		//			
		//				$condition = new InCondition(User :: PROPERTY_ID, $excluded_users);
		//			
		//		}else{
		//			$condition = new EqualityCondition(User :: PROPERTY_ID, 0);
		//		}
		//				
		//		$query = $this->action_bar->get_query ();
		//		
		//		if (isset ( $query ) && $query != '') {
		//			$or_conditions [] = new PatternMatchCondition ( User::PROPERTY_FIRSTNAME, '*' . $query . '*' );
		//			$or_conditions [] = new PatternMatchCondition ( User::PROPERTY_LASTNAME, '*' . $query . '*' );
		//			$or_conditions [] = new PatternMatchCondition ( User::PROPERTY_USERNAME, '*' . $query . '*' );
		//			$or_condition = new OrCondition ( $or_conditions );
		//		}
		//		
		//		if($or_condition){
		//			$conditions = array($condition,$or_condition);
		//			$condition = new AndCondition($conditions);
		//		}
		

		return $condition;
	}

}

?>