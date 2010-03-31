<?php

class InternshipPlannerManagerApplicationChooserComponent extends InternshipPlannerManagerComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
       	$trail = new BreadcrumbTrail ();
		//$trail->add ( new Breadcrumb ( $this->get_url (array(InternshipPlannerOrganisationManager::PARAM_ACTION => InternshipPlannerOrganisationManager :: ACTION_BROWSE_ORGANISATION) ), Translation::get ( 'BrowseOrganisations' ) ) );

		
		$this->display_header ( $trail );

		
		echo '<div>';
		echo '<a href="' . $this->get_category_application_url(). '">' . 'Category Manager' . '</a><br/>';
		echo '<a href="' . $this->get_organisation_application_url(). '">' . 'Organisation Manager' . '</a><br/>';
		echo '</div>';
	
		$this->display_footer ();
    }
}
?>