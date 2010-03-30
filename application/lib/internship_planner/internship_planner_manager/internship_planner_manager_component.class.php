<?php

abstract class InternshipPlannerManagerComponent extends WebApplicationComponent
{
	/**
	 * Constructor
	 * @param InternshipPlanner $internship_planner The internship_planner which
	 * provides this component
	 */
	function InternshipPlannerManagerComponent($internship_planner)
	{
		parent :: __construct($internship_planner);
	}

	
}
?>