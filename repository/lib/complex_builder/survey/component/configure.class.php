<?php
require_once dirname(__FILE__) . '/../survey_manager.class.php';

require_once dirname(__FILE__) . '/../survey_builder_component.class.php';
require_once dirname(__FILE__) . '/../../complex_repo_viewer.class.php';
require_once Path :: get_repository_path() . '/lib/content_object/survey/survey.class.php';

class SurveyBuilderConfigureComponent extends SurveyBuilderComponent
{

    function run()
    {
    	$menu_trail = $this->get_clo_breadcrumbs ();
		$trail = new BreadcrumbTrail ( false );
		$trail->merge ( $menu_trail );
		$trail->add_help ( 'repository survey_page component configurer' );
		$trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => SurveyBuilder :: ACTION_CONFIGURE_COMPONENT)), Translation :: get('SurveyPageConfigure')));
		
    	if ($this->get_cloi ()) {
			$lo = RepositoryDataManager::get_instance ()->retrieve_content_object ( $this->get_cloi ()->get_ref () );
		} else {
			$lo = $this->get_root_lo ();
		}
			
		$survey_page = SurveyDataManager::get_instance()->retrieve_survey_page(Request::get(SurveyBuilder :: PARAM_SELECTED_CLOI_ID));
		
		dump($survey_page);
		
    	$this->display_header ( $trail );
		
		$this->display_footer ();
    }
}

?>