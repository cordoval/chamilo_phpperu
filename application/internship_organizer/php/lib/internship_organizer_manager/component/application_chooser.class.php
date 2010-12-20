<?php
namespace application\internship_organizer;

use admin\AdminSearchForm;

use common\libraries\Utilities;
use common\libraries\Translation;
use common\libraries\DelegateComponent;
use common\libraries\DynamicAction;
use common\libraries\DynamicActionsTab;
use common\libraries\DynamicTabsRenderer;
use common\libraries\Theme;

class InternshipOrganizerManagerApplicationChooserComponent extends InternshipOrganizerManager implements DelegateComponent
{

    const TAB_PERIOD = 1;
    const TAB_AGREEMENT = 2;
    const TAB_ORGANISATION = 3;
    const TAB_CATEGORY = 4;
    const TAB_REGION = 5;
    const TAB_APPOINTMENT = 6;
    const TAB_ADMINISTARTION = 7;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {

        $links = $this->get_internship_organizer_links();

        $this->display_header();
        if (count($links) > 0)
        {
            echo $this->get_internship_organizer_tabs($links);
        }
        else
        {
            $this->display_error_message(Translation :: get('NotAllowed'));
        }
        $this->display_footer();
    }

    /**
     * Returns an HTML representation of the actions.
     * @return string $html HTML representation of the actions.
     */
    function get_internship_organizer_tabs($links)
    {
        $renderer_name = Utilities :: get_classname_from_object($this, true);
        $internship_organizer_tabs = new DynamicTabsRenderer($renderer_name);

        $index = 0;
        foreach ($links as $sub_manager_links)
        {
            if (count($sub_manager_links['links']))
            {
                $index ++;
                $actions_tab = new DynamicActionsTab($sub_manager_links['application']['class'], $sub_manager_links['application']['name'], Theme :: get_image_path() . 'place_mini_' . $sub_manager_links['application']['class'] . '.png', implode("\n", $html));

                if (isset($application_links['search']))
                {
                    $search_form = new AdminSearchForm($this, $sub_manager_links['search'], $index);
                    $actions_tab->add_action(new DynamicAction(null, $search_form->display(), Theme :: get_image_path() . 'browse_search.png'));
                }

                foreach ($sub_manager_links['links'] as $action)
                {
                    $actions_tab->add_action($action);
                }

                $internship_organizer_tabs->add_tab($actions_tab);
            }
        }

        return $internship_organizer_tabs->render();
    }

    private function get_tabs()
    {
        return array(self :: TAB_APPOINTMENT, self :: TAB_AGREEMENT, self :: TAB_PERIOD, self :: TAB_ORGANISATION, self :: TAB_CATEGORY, self :: TAB_REGION, self :: TAB_ADMINISTARTION);
    }

    function get_internship_organizer_links()
    {

        $links = array();
        $tabs = $this->get_tabs();
        foreach ($tabs as $tab)
        {

            switch ($tab)
            {
                case self :: TAB_PERIOD :
                    if (InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: RIGHT_VIEW, InternshipOrganizerRights :: LOCATION_PERIOD, InternshipOrganizerRights :: TYPE_COMPONENT))
                    {
                        $tab_links = $this->get_links_for_tab($tab);
                        $links[] = $tab_links;
                    }
                    break;
                case self :: TAB_AGREEMENT :
                    if (InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: RIGHT_VIEW, InternshipOrganizerRights :: LOCATION_AGREEMENT, InternshipOrganizerRights :: TYPE_COMPONENT))
                    {
                        $tab_links = $this->get_links_for_tab($tab);
                        $links[] = $tab_links;
                    }
                    break;
                case self :: TAB_ORGANISATION :
                    if (InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: RIGHT_VIEW, InternshipOrganizerRights :: LOCATION_ORGANISATION, InternshipOrganizerRights :: TYPE_COMPONENT))
                    {
                        $tab_links = $this->get_links_for_tab($tab);
                        $links[] = $tab_links;
                    }
                    break;
                case self :: TAB_CATEGORY :
                    if (InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: RIGHT_VIEW, InternshipOrganizerRights :: LOCATION_CATEGORY, InternshipOrganizerRights :: TYPE_COMPONENT))
                    {
                        $tab_links = $this->get_links_for_tab($tab);
                        $links[] = $tab_links;
                    }
                    break;
                case self :: TAB_REGION :
                    if (InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: RIGHT_VIEW, InternshipOrganizerRights :: LOCATION_REGION, InternshipOrganizerRights :: TYPE_COMPONENT))
                    {
                        $tab_links = $this->get_links_for_tab($tab);
                        $links[] = $tab_links;
                    }
                    break;
                case self :: TAB_APPOINTMENT :
                    if (InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: RIGHT_VIEW, InternshipOrganizerRights :: LOCATION_APPOINTMENT, InternshipOrganizerRights :: TYPE_COMPONENT))
                    {
                        $tab_links = $this->get_links_for_tab($tab);
                        $links[] = $tab_links;
                    }
                    break;
                case self :: TAB_ADMINISTARTION :
                    if (InternshipOrganizerRights :: is_allowed_in_internship_organizers_subtree(InternshipOrganizerRights :: RIGHT_VIEW, InternshipOrganizerRights :: LOCATION_ADMINISTRATION, InternshipOrganizerRights :: TYPE_COMPONENT))
                    {
                        $tab_links = $this->get_links_for_tab($tab);
                        $links[] = $tab_links;
                    }
                    break;
                default :

                    break;
            }

        }
        return $links;

    }

    function get_links_for_tab($index)
    {

        $links = array();
        $tab_links = array();

        switch ($index)
        {
            case self :: TAB_PERIOD :

                $tab_links['application'] = array('name' => Translation :: get('PeriodTab'), 'class' => 'period');

                $period_link = new DynamicAction();
                $period_link->set_title(Translation :: get('PeriodLink'));
                $period_link->set_description(Translation :: get('PeriodLinkDescription'));
                $period_link->set_image(Theme :: get_image_path() . 'browse_period.png');
                $period_link->set_url($this->get_period_application_url());
                $links[] = $period_link;

                $tab_links['links'] = $links;
                break;
            case self :: TAB_AGREEMENT :

                $tab_links['application'] = array('name' => Translation :: get('AgreementTab'), 'class' => 'agreement');

                $agreement_link = new DynamicAction();
                $agreement_link->set_title(Translation :: get('AgreementLink'));
                $agreement_link->set_description(Translation :: get('AgreementLinkDescription'));
                $agreement_link->set_image(Theme :: get_image_path() . 'browse_agreement.png');
                $agreement_link->set_url($this->get_agreement_application_url());
                $links[] = $agreement_link;

                $tab_links['links'] = $links;
                break;
            case self :: TAB_ORGANISATION :

                $tab_links['application'] = array('name' => Translation :: get('OrganisationTab'), 'class' => 'organisation');

                $organisation_link = new DynamicAction();
                $organisation_link->set_title(Translation :: get('OrganisationLink'));
                $organisation_link->set_description(Translation :: get('OrganisationLinkDescription'));
                $organisation_link->set_image(Theme :: get_image_path() . 'browse_organisation.png');
                $organisation_link->set_url($this->get_organisation_application_url());
                $links[] = $organisation_link;

                $tab_links['links'] = $links;
                break;
            case self :: TAB_CATEGORY :

                $tab_links['application'] = array('name' => Translation :: get('CategoryTab'), 'class' => 'category');

                $category_link = new DynamicAction();
                $category_link->set_title(Translation :: get('CategoryLink'));
                $category_link->set_description(Translation :: get('CategoryLinkDescription'));
                $category_link->set_image(Theme :: get_image_path() . 'browse_category.png');
                $category_link->set_url($this->get_category_application_url());
                $links[] = $category_link;

                $tab_links['links'] = $links;
                break;
            case self :: TAB_REGION :

                $tab_links['application'] = array('name' => Translation :: get('RegionTab'), 'class' => 'region');

                $region_link = new DynamicAction();
                $region_link->set_title(Translation :: get('RegionLink'));
                $region_link->set_description(Translation :: get('RegionLinkDescription'));
                $region_link->set_image(Theme :: get_image_path() . 'browse_region.png');
                $region_link->set_url($this->get_region_application_url());
                $links[] = $region_link;

                $tab_links['links'] = $links;
                break;
            case self :: TAB_APPOINTMENT :

                $tab_links['application'] = array('name' => Translation :: get('AppointmentTab'), 'class' => 'appointment');

                $appointment_link = new DynamicAction();
                $appointment_link->set_title(Translation :: get('AppointmentLink'));
                $appointment_link->set_description(Translation :: get('AppointmentLinkDescription'));
                $appointment_link->set_image(Theme :: get_image_path() . 'browse_region.png');
                $appointment_link->set_url($this->get_appointment_application_url());
                $links[] = $appointment_link;

                $tab_links['links'] = $links;
                break;
            case self :: TAB_ADMINISTARTION :

                $tab_links['application'] = array('name' => Translation :: get('AdministrationTab'), 'class' => 'administration');

                $period_link = new DynamicAction();
                $period_link->set_title(Translation :: get('PeriodRightLink'));
                $period_link->set_description(Translation :: get('PeriodRightLinkDescription'));
                $period_link->set_image(Theme :: get_image_path() . 'browse_period.png');
                $period_link->set_url($this->get_administration_url(InternshipOrganizerRights :: LOCATION_PERIOD));
                $links[] = $period_link;

                $agreement_link = new DynamicAction();
                $agreement_link->set_title(Translation :: get('AgreementRightLink'));
                $agreement_link->set_description(Translation :: get('AgreementRightLinkDescription'));
                $agreement_link->set_image(Theme :: get_image_path() . 'browse_agreement.png');
                $agreement_link->set_url($this->get_administration_url(InternshipOrganizerRights :: LOCATION_AGREEMENT));
                $links[] = $agreement_link;

                $organisation_link = new DynamicAction();
                $organisation_link->set_title(Translation :: get('OrganisationRightLink'));
                $organisation_link->set_description(Translation :: get('OrganisationRightLinkDescription'));
                $organisation_link->set_image(Theme :: get_image_path() . 'browse_organisation.png');
                $organisation_link->set_url($this->get_administration_url(InternshipOrganizerRights :: LOCATION_ORGANISATION));
                $links[] = $organisation_link;

                $category_link = new DynamicAction();
                $category_link->set_title(Translation :: get('CategoryRightLink'));
                $category_link->set_description(Translation :: get('CategoryRightLinkDescription'));
                $category_link->set_image(Theme :: get_image_path() . 'browse_category.png');
                $category_link->set_url($this->get_administration_url(InternshipOrganizerRights :: LOCATION_CATEGORY));
                $links[] = $category_link;

                $region_link = new DynamicAction();
                $region_link->set_title(Translation :: get('RegionRightLink'));
                $region_link->set_description(Translation :: get('RegionRightLinkDescription'));
                $region_link->set_image(Theme :: get_image_path() . 'browse_region.png');
                $region_link->set_url($this->get_administration_url(InternshipOrganizerRights :: LOCATION_REGION));
                $links[] = $region_link;

                $appointment_link = new DynamicAction();
                $appointment_link->set_title(Translation :: get('AppointmentRightLink'));
                $appointment_link->set_description(Translation :: get('AppointmentRightLinkDescription'));
                $appointment_link->set_image(Theme :: get_image_path() . 'browse_region.png');
                $appointment_link->set_url($this->get_administration_url(InternshipOrganizerRights :: LOCATION_APPOINTMENT));
                $links[] = $appointment_link;

                $reporting_link = new DynamicAction();
                $reporting_link->set_title(Translation :: get('ReportingRightLink'));
                $reporting_link->set_description(Translation :: get('ReportingRightLinkDescription'));
                $reporting_link->set_image(Theme :: get_image_path() . 'browse_region.png');
                $reporting_link->set_url($this->get_administration_url(InternshipOrganizerRights :: LOCATION_REPORTING));
                $links[] = $reporting_link;

                if ($this->get_user()->is_platform_admin())
                {
                    $administration_link = new DynamicAction();
                    $administration_link->set_title(Translation :: get('AdministrationRightLink'));
                    $administration_link->set_description(Translation :: get('AdministrationRightLinkDescription'));
                    $administration_link->set_image(Theme :: get_image_path() . 'browse_region.png');
                    $administration_link->set_url($this->get_administration_url(InternshipOrganizerRights :: LOCATION_ADMINISTRATION));
                    $links[] = $administration_link;
                }

                $tab_links['links'] = $links;
                break;
            default :

                break;
        }

        return $tab_links;
    }
}
?>