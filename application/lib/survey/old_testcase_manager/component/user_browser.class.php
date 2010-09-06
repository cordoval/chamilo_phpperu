<?php

require_once dirname ( __FILE__ ) . '/user_browser/user_browser_table.class.php';

class TestcaseManagerUserBrowserComponent extends TestcaseManager {
	
	
	private $action_bar;
	
	/**
	 * Runs this component and displays its output.
	 */
	function run() {
		
		$survey_pub_id = Request::get(TestcaseManager:: PARAM_SURVEY_PUBLICATION);
		
		$trail = BreadcrumbTrail :: get_instance();
		$trail->add ( new Breadcrumb ( $this->get_url ( array (self::PARAM_ACTION => self::ACTION_BROWSE_SURVEY_PUBLICATIONS)), Translation::get ( 'BrowseTestCaseSurveyPublications' ) ));
		$trail->add ( new Breadcrumb ( $this->get_url ( array (self::PARAM_ACTION => self::ACTION_BROWSE_SURVEY_EXCLUDED_USERS, self::PARAM_SURVEY_PUBLICATION => $survey_pub_id) ), Translation::get ( 'BrowseTestCaseExcludedUsers' ) ) );
		
		$this->action_bar = $this->get_action_bar ();
		$this->display_header ( $trail );
		
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
		$parameters[TestcaseManager:: PARAM_SURVEY_PUBLICATION] = Request::get(TestcaseManager:: PARAM_SURVEY_PUBLICATION);
		$table = new TestcaseManagerUserBrowserTable ( $this, $parameters, $this->get_condition () );
		return $table->as_html ();
	}
	
	function get_condition() {
		
		
		$survey_pub_id = Request::get(TestcaseManager:: PARAM_SURVEY_PUBLICATION);
		
		$survey = SurveyDataManager::get_instance()->retrieve_survey_publication($survey_pub_id);
		$excluded_users = $survey->get_excluded_participants();
		$condition = null;
		if(count($excluded_users) > 0){
			
				$condition = new InCondition(User :: PROPERTY_ID, $excluded_users);
			
		}else{
			$condition = new EqualityCondition(User :: PROPERTY_ID, 0);
		}
				
		$query = $this->action_bar->get_query ();
		
		if (isset ( $query ) && $query != '') {
			$or_conditions [] = new PatternMatchCondition ( User::PROPERTY_FIRSTNAME, '*' . $query . '*' );
			$or_conditions [] = new PatternMatchCondition ( User::PROPERTY_LASTNAME, '*' . $query . '*' );
			$or_conditions [] = new PatternMatchCondition ( User::PROPERTY_USERNAME, '*' . $query . '*' );
			$or_condition = new OrCondition ( $or_conditions );
		}
		
		if($or_condition){
			$conditions = array($condition,$or_condition);
			$condition = new AndCondition($conditions);
		}
		
		
		return $condition;
	}
	
	function get_action_bar() {
		$action_bar = new ActionBarRenderer ( ActionBarRenderer::TYPE_HORIZONTAL );
		$parameters = $this->get_parameters ();
		$parameters[TestcaseManager:: PARAM_SURVEY_PUBLICATION] = Request::get(TestcaseManager:: PARAM_SURVEY_PUBLICATION);
			
		$action_bar->set_search_url ( $this->get_url ($parameters) );
		return $action_bar;
	}

}
?>