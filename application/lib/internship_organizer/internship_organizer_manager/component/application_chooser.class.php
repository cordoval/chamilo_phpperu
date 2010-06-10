<?php

class InternshipOrganizerManagerApplicationChooserComponent extends InternshipOrganizerManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
       	$trail = BreadcrumbTrail :: get_instance();
       	$trail->add ( new Breadcrumb ( $this->get_url (array(InternshipOrganizerManager::PARAM_ACTION => InternshipOrganizerManager :: ACTION_APPLICATION_CHOOSER) ), Translation::get ( 'InternshipOrganizer' ) ) );
	
		$this->display_header ( $trail );
		
		echo '<div>';
		echo '<a href="' . $this->get_agreement_application_url(). '">' . 'Agreement Manager' . '</a><br/>';
		echo '<a href="' . $this->get_category_application_url(). '">' . 'Category Manager' . '</a><br/>';
		echo '<a href="' . $this->get_mentor_application_url(). '">' . 'Mentor Manager' . '</a><br/>';
		echo '<a href="' . $this->get_organisation_application_url(). '">' . 'Organisation Manager' . '</a><br/>';
		echo '<a href="' . $this->get_period_application_url(). '">' . 'Period Manager' . '</a><br/>';
		echo '<a href="' . $this->get_region_application_url(). '">' . 'Region Manager' . '</a><br/>';
		echo '</div>';
	
		$this->display_footer ();
    }
}
?>