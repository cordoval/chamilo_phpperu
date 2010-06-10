<?php

require_once Path :: get_application_path() . 'lib/internship_organizer/forms/subscribe_users_form.class.php';

class InternshipOrganizerPeriodManagerSubscribeUsersComponent extends InternshipOrganizerPeriodManager
{
	private $period;
	private $ab;

	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = BreadcrumbTrail :: get_instance();
		 
		$trail->add(new Breadcrumb($this->get_browse_periods_url(), Translation :: get('BrowseInternshipOrganizerPeriods')));

		$period_id = Request :: get(InternshipOrganizerPeriodManager :: PARAM_PERIOD_ID);
		$this->period = $this->retrieve_period($period_id);
		 
		$trail->add(new Breadcrumb($this->get_period_viewing_url($this->period), $this->period->get_name()));
		$trail->add(new Breadcrumb($this->get_period_subscribe_users_url($this->period), Translation :: get('AddInternshipOrganizerUsers')));
		$trail->add_help('period subscribe users');

		$form = new InternshipOrganizerSubscribeUsersForm($period, $this->get_url(array(InternshipOrganizerPeriodManager :: PARAM_PERIOD_ID => Request :: get(InternshipOrganizerPeriodManager :: PARAM_PERIOD_ID))), $this->get_user());
		
        if ($form->validate())
        {
            $success = $form->create_period();
            if ($success)
            {
                $period = $form->get_period();
                $this->redirect(Translation :: get('InternshipOrganizerPeriodCreated'), (false), array(InternshipOrganizerPeriodManager :: PARAM_ACTION => InternshipOrganizerPeriodManager :: ACTION_VIEW_PERIOD, InternshipOrganizerPeriodManager :: PARAM_PERIOD_ID => $period->get_id()));
            }
            else
            {
                $this->redirect(Translation :: get('InternshipOrganizerPeriodNotCreated'), (true), array(InternshipOrganizerPeriodManager :: PARAM_ACTION => InternshipOrganizerPeriodManager :: ACTION_BROWSE_PERIODS));
            }
        }
        else
        {	$this->display_header($trail, true);
            $form->display();
            $this->display_footer();
        }
		
//		//$output = $this->get_users_subscribe_html();
//
//		$this->display_header($trail);
//		//echo $this->ab->as_html() . '<br />';
//		echo $output;
//		$this->display_footer();
	}

//	function get_users_subscribe_html()
//	{
//		$parameters = $this->get_parameters();
//
//		$parameters[InternshipOrganizerPeriodManager :: PARAM_ACTION] = InternshipOrganizerPeriodManager :: ACTION_SUBSCRIBE_USERS_BROWSER;
//		$parameters[InternshipOrganizerPeriodManager :: PARAM_PERIOD_ID ] = $this->period->get_id();
//		 
//		$table = new SubscribeUsersBrowserTable($this, $parameters, $this->get_subscribe_condition());
//
//		$html = array();
//		$html[] = $table->as_html();
//
//		return implode($html, "\n");
//	}

//	function get_subscribe_condition()
//	{
//		$condition = new EqualityCondition(InternshipOrganizerPeriodRelUser :: PROPERTY_PERIOD_ID, Request :: get(InternshipOrganizerPeriodRelUser :: PROPERTY_PERIOD_ID));
//
//		$period_rel_users = $this->retrieve_period_rel_users($condition);
//
//		$conditions = array();
//
//		while ($category_rel_location = $category_rel_locations->next_result())
//		{
//			$conditions[] = new NotCondition(new EqualityCondition(InternshipOrganizerLocation :: PROPERTY_ID, $category_rel_location->get_location_id()));
//		}
//
//		$query = $this->ab->get_query();
//
//		if (isset($query) && $query != '')
//		{
//			 
//			$or_conditions[] = new PatternMatchCondition(InternshipOrganizerLocation :: PROPERTY_NAME, '*' . $query . '*');
//			$or_conditions[] = new PatternMatchCondition(InternshipOrganizerLocation :: PROPERTY_ADDRESS, '*' . $query . '*');
//
//			$search_city_conditions[] = new PatternMatchCondition(InternshipOrganizerRegion :: PROPERTY_CITY_NAME, '*' . $query . '*');
//			$search_city_conditions[] = new PatternMatchCondition(InternshipOrganizerRegion :: PROPERTY_ZIP_CODE, '*' . $query . '*');
//			$city_conditions = new OrCondition($search_city_conditions);
//
//			$search_city_subselect_condition = new SubselectCondition(InternshipOrganizerLocation :: PROPERTY_REGION_ID, InternshipOrganizerRegion::PROPERTY_ID, InternshipOrganizerRegion::get_table_name(),$city_conditions);
//			$or_conditions[] = $search_city_subselect_condition;
//
//			$conditions[] = new OrCondition($or_conditions);
//		}
//
//		if (count($conditions) == 0){
//			return null;
//		}
//		 
//
//		$condition = new AndCondition($conditions);
//
//		return $condition;
//	}

	function get_period()
	{
		return $this->period;
	}

//	function get_action_bar()
//	{
//		$period = $this->period;
//
//		$action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
//
//		$action_bar->set_search_url($this->get_period_subscribe_users_url($period));
//
//		$action_bar->add_common_action(new ToolbarItem(Translation :: get('ShowAll'), Theme :: get_common_image_path() . 'action_browser.png', $this->get_period_subscribe_users_url($period), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
//
//		return $action_bar;
//	}
}
?>