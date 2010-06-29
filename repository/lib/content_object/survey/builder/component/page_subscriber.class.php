<?php

require_once Path :: get_repository_path() . '/lib/content_object/survey/survey_context_template_rel_page.class.php';


class SurveyBuilderPageSubscriberComponent extends SurveyBuilder 
{
	
	/**
	 * Runs this component and displays its output.
	 */
	function run() 
	{
		
		$template_id = Request::get ( SurveyBuilder::PARAM_TEMPLATE_ID );
		$pages = Request::get ( SurveyBuilder::PARAM_SURVEY_PAGE_ID );
		$survey_id = $this->get_root_content_object_id();
			
		$failures = 0;
		
		if (! empty ( $pages )) 
		{
			if (! is_array ( $pages )) 
			{
				$pages = array ($pages );
			}
					
			foreach ( $pages as $page_id ) 
			{
				$conditions = array();
				$conditions[] = new EqualityCondition(SurveyContextTemplateRelPage::PROPERTY_PAGE_ID, $page_id, SurveyContextTemplateRelPage :: get_table_name());
				$conditions[] = new EqualityCondition(SurveyContextTemplateRelPage::PROPERTY_SURVEY_ID, $survey_id, SurveyContextTemplateRelPage :: get_table_name());
				$conditions[] = new EqualityCondition(SurveyContextTemplateRelPage::PROPERTY_TEMPLATE_ID, $template_id, SurveyContextTemplateRelPage :: get_table_name());
				$condition = new AndCondition($conditions);
							
				$existing_templaterelpage = SurveyContextDataManager::get_instance()->retrieve_template_rel_pages ($condition)->next_result();
				
			if (! $existing_templaterelpage) 
				{
					$templaterelpage = new SurveyContextTemplateRelPage ();
					$templaterelpage->set_page_id ( $page_id );
					$templaterelpage->set_survey_id ( $survey_id );
					$templaterelpage->set_template_id ( $template_id );
															
					if (! $templaterelpage->create ()) 
					{
						$failures ++;
					}
					//                    else
				//                    {
				//                        Event :: trigger('subscribe_page', 'template', array('target_template_id' => $templaterelpage->get_template_id(), 'target_page_id' => $templaterelpage->get_page_id(), 'action_survey_id' => $templaterelpage->get_survey_id()));
				//                    }
				} else 
				{
					$contains_dupes = true;
				}
			}
			
			//$this->get_result( not good enough?
			if ($failures) 
			{
				if (count ( $pages ) == 1) 
				{
					$message = 'SelectedPageNotAddedToSurveyContextTemlplate' . ($contains_dupes ? 'Dupes' : '');
				} else 
				{
					$message = 'SelectedPagesNotAddedToSurveyContextTemlplate' . ($contains_dupes ? 'Dupes' : '');
				}
			} else 
			{
				if (count ( $pages ) == 1) 
				{
					$message = 'SelectedPageAddedToSurveyContextTemlplate' . ($contains_dupes ? 'Dupes' : '');
				} else 
				{
					$message = 'SelectedPagesAddedToSurveyContextTemlplate' . ($contains_dupes ? 'Dupes' : '');
				}
			}
			
			$this->redirect ( Translation::get ( $message ), ($failures ? true : false), array (SurveyBuilder :: PARAM_BUILDER_ACTION => SurveyBuilder :: ACTION_VIEW_CONTEXT, SurveyBuilder :: PARAM_TEMPLATE_ID => $template_id) );
			
		} else 
		{
			$this->display_error_page ( htmlentities ( Translation::get ( 'NoSurveyContextTemplateRelPageSelected' ) ) );
		}
	}
}
?>