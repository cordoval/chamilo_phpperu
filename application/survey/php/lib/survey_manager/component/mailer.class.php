<?php
namespace application\survey;

use admin\AdminSearchForm;

use common\libraries\Utilities;
use common\libraries\Translation;
use common\libraries\DelegateComponent;
use common\libraries\DynamicAction;
use common\libraries\DynamicActionsTab;
use common\libraries\DynamicTabsRenderer;
use common\libraries\Theme;
use common\libraries\Breadcrumb;
use common\libraries\BreadcrumbTrail;
use common\libraries\Request;

class SurveyManagerMailerComponent extends SurveyManager
{
    
    const TAB_INVITEES = 1;
    const TAB_REPORTING = 2;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        
        $this->publication_id = Request :: get(SurveyManager :: PARAM_PUBLICATION_ID);
        
        if (! SurveyRights :: is_allowed_in_surveys_subtree(SurveyRights :: RIGHT_INVITE, $this->publication_id, SurveyRights :: TYPE_PUBLICATION, $this->get_user_id()))
        {
            $this->display_header();
            $this->display_error_message(Translation :: get('NotAllowed'));
            $this->display_footer();
            exit();
        }
        
        $links = $this->get_survey_mail_links();
        
        $this->display_header();
        if (count($links) > 0)
        {
            echo $this->get_survey_mail_tabs($links);
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
    function get_survey_mail_tabs($links)
    {
        $renderer_name = Utilities :: get_classname_from_object($this, true);
        $survey_tabs = new DynamicTabsRenderer($renderer_name);
        
        $index = 0;
        foreach ($links as $sub_manager_links)
        {
            if (count($sub_manager_links['links']))
            {
                $index ++;
                $actions_tab = new DynamicActionsTab($sub_manager_links['application']['class'], $sub_manager_links['application']['name'], Theme :: get_image_path() . 'place_mini_' . $sub_manager_links['application']['class'] . '.png');
                
                foreach ($sub_manager_links['links'] as $action)
                {
                    $actions_tab->add_action($action);
                }
                
                $survey_tabs->add_tab($actions_tab);
            }
        }
        
        return $survey_tabs->render();
    }

    private function get_tabs()
    {
        return array(self :: TAB_INVITEES, self :: TAB_REPORTING);
    }

    function get_survey_mail_links()
    {
        
        $links = array();
        $tabs = $this->get_tabs();
        foreach ($tabs as $tab)
        {
            
            switch ($tab)
            {
                case self :: TAB_INVITEES :
                    if (SurveyRights :: is_allowed_in_surveys_subtree(SurveyRights :: RIGHT_INVITE, SurveyRights :: LOCATION_MAILER, SurveyRights :: TYPE_COMPONENT))
                    {
                        $tab_links = $this->get_links_for_tab($tab);
                        $links[] = $tab_links;
                    }
                    break;
                case self :: TAB_REPORTING :
                    if (SurveyRights :: is_allowed_in_surveys_subtree(SurveyRights :: RIGHT_INVITE, SurveyRights :: LOCATION_MAILER, SurveyRights :: TYPE_COMPONENT))
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
            case self :: TAB_INVITEES :
                
                $tab_links['application'] = array('name' => Translation :: get('InviteeTab'), 'class' => 'invitee');
                
                $period_link = new DynamicAction();
                $period_link->set_title(Translation :: get('InviteeLink'));
                $period_link->set_description(Translation :: get('InviteeLinkDescription'));
                $period_link->set_image(Theme :: get_image_path() . 'browse.png');
                //                $period_link->set_url($this->get_period_application_url());
                $links[] = $period_link;
                
                $agreement_link = new DynamicAction();
                $agreement_link->set_title(Translation :: get('ReportingRightLink'));
                $agreement_link->set_description(Translation :: get('ReportingRightLinkDescription'));
                $agreement_link->set_image(Theme :: get_image_path() . 'browse_agreement.png');
                //                $agreement_link->set_url($this->get_administration_url(SurveyRights :: LOCATION_MAILER_COMPONENT_AGREEMENT));
                $links[] = $agreement_link;
                
                $tab_links['links'] = $links;
                break;
            case self :: TAB_REPORTING :
                
                $tab_links['application'] = array('name' => Translation :: get('ReportingTab'), 
                        'class' => 'administration');
                
                $period_link = new DynamicAction();
                $period_link->set_title(Translation :: get('InviteeRightLink'));
                $period_link->set_description(Translation :: get('InviteeRightLinkDescription'));
                $period_link->set_image(Theme :: get_image_path() . 'browse_period.png');
                //                $period_link->set_url($this->get_administration_url(SurveyRights :: LOCATION_MAILER_COMPONENT_PERIOD));
                $links[] = $period_link;
                
                $agreement_link = new DynamicAction();
                $agreement_link->set_title(Translation :: get('ReportingRightLink'));
                $agreement_link->set_description(Translation :: get('ReportingRightLinkDescription'));
                $agreement_link->set_image(Theme :: get_image_path() . 'browse_agreement.png');
                //                $agreement_link->set_url($this->get_administration_url(SurveyRights :: LOCATION_MAILER_COMPONENT_AGREEMENT));
                $links[] = $agreement_link;
                
                $tab_links['links'] = $links;
                break;
            default :
                
                break;
        }
        
        return $tab_links;
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(self :: PARAM_ACTION => self :: ACTION_BROWSE)), Translation :: get('BrowseSurveys')));
    }

    function get_additional_parameters()
    {
        return array(self :: PARAM_PUBLICATION_ID);
    }
}
?>