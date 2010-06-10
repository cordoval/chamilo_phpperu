<?php
/**
 * @package application.lib.internship_organizer.internship_organizer_manager
 */

require_once dirname(__FILE__) . '/../internship_organizer_data_manager.class.php';
require_once dirname(__FILE__) . '/../internship_organizer_utilities.class.php';

require_once Path :: get_application_path() . 'lib/internship_organizer/organisation_manager/organisation_manager.class.php';

require_once Path :: get_application_path() . 'lib/internship_organizer/category_manager/category_manager.class.php';

require_once Path :: get_application_path() . 'lib/internship_organizer/agreement_manager/agreement_manager.class.php';

require_once Path :: get_application_path() . 'lib/internship_organizer/region_manager/region_manager.class.php';

require_once Path :: get_application_path() . 'lib/internship_organizer/mentor_manager/mentor_manager.class.php';

require_once Path :: get_application_path() . 'lib/internship_organizer/period_manager/period_manager.class.php';

class InternshipOrganizerManager extends WebApplication
{
    const APPLICATION_NAME = 'internship_organizer';
    
    const ACTION_ORGANISATION = 'organisation';
    const ACTION_AGREEMENT = 'agreement';
    const ACTION_CATEGORY = 'category';
    const ACTION_APPLICATION_CHOOSER = 'chooser';
    const ACTION_REGION = 'region';
    const ACTION_MENTOR = 'mentor';
    const ACTION_PERIOD = 'period';
    
    const PARAM_TARGET = 'target_users_and_groups';
    const PARAM_TARGET_ELEMENTS = 'target_users_and_groups_elements';
    const PARAM_TARGET_OPTION = 'target_users_and_groups_option';

    /**
     * Constructor
     * @param User $user The current user
     */
    function InternshipOrganizerManager($user = null)
    {
        parent :: __construct($user);
        $this->parse_input_from_table();
    }

    /**
     * Run this internship_organizer manager
     */
    function run()
    {
        $action = $this->get_action();
        $component = null;
        $trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb($this->get_url(array(self :: PARAM_APPLICATION => self :: APPLICATION_NAME, Translation :: get(self :: APPLICATION_NAME)))));
        
        switch ($action)
        {
            case self :: ACTION_ORGANISATION :
                $component = $this->create_component('Organisation');
                break;
            case self :: ACTION_AGREEMENT :
                $component = $this->create_component('Agreement');
                break;
            case self :: ACTION_CATEGORY :
                $component = $this->create_component('Category');
                break;
            case self :: ACTION_APPLICATION_CHOOSER :
                $component = $this->create_component('ApplicationChooser');
                break;
            case self :: ACTION_REGION :
                $component = $this->create_component('Region');
                break;
            case self :: ACTION_MENTOR :
                $component = $this->create_component('Mentor');
                break;
            case self :: ACTION_PERIOD :
                $component = $this->create_component('Period');
                break;
            default :
                $this->set_action(self :: ACTION_APPLICATION_CHOOSER);
                $trail = new BreadcrumbTrail();
                $trail->add(new Breadcrumb($this->get_url(array(self :: PARAM_APPLICATION => self :: APPLICATION_NAME, Translation :: get(self :: APPLICATION_NAME)))));
                $component = $this->create_component('ApplicationChooser');
        
        }
        
        $component->run();
    }

    function get_organisation_application_url()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_ORGANISATION));
    
    }

    function get_agreement_application_url()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_AGREEMENT));
    
    }

    function get_category_application_url()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_CATEGORY));
    
    }

    function get_application_chooser_url()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_APPLICATION_CHOOSER));
    
    }

    function get_region_application_url()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_REGION));
    
    }

    function get_mentor_application_url()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_MENTOR));
    
    }

    function get_period_application_url()
    {
        return $this->get_url(array(self :: PARAM_ACTION => self :: ACTION_PERIOD));
    
    }

    private function parse_input_from_table()
    {
        //not used jet
    }

    function get_application_name()
    {
        return self :: APPLICATION_NAME;
    }

    //publications
    

    function content_object_is_published($object_id)
    {
        return InternshipOrganizerDataManager :: get_instance()->content_object_is_published($object_id);
    }

    function any_content_object_is_published($object_ids)
    {
        return InternshipOrganizerDataManager :: get_instance()->any_content_object_is_published($object_ids);
    }

    function get_content_object_publication_attributes($object_id, $type = null, $offset = null, $count = null, $order_property = null)
    {
        return InternshipOrganizerDataManager :: get_instance()->get_content_object_publication_attributes($object_id, $type, $offset, $count, $order_property);
    }

    function get_content_object_publication_attribute($publication_id)
    {
        return InternshipOrganizerDataManager :: get_instance()->get_content_object_publication_attribute($publication_id);
    }

    function count_publication_attributes($type = null, $condition = null)
    {
        return InternshipOrganizerDataManager :: get_instance()->count_publication_attributes($type, $condition);
    }

    function delete_content_object_publications($object_id)
    {
        return InternshipOrganizerDataManager :: get_instance()->delete_content_object_publications($object_id);
    }

    function delete_content_object_publication($publication_id)
    {
        return InternshipOrganizerDataManager :: get_instance()->delete_content_object_publication($publication_id);
    }

    function update_content_object_publication_id($publication_attr)
    {
        return InternshipOrganizerDataManager :: get_instance()->update_content_object_publication_id($publication_attr);
    }

    function add_publication_attributes_elements($form)
    {
        $form->addElement('category', Translation :: get('PublicationDetails'));
        $form->addElement('checkbox', self :: APPLICATION_NAME . '_opt_' . SurveyPublication :: PROPERTY_HIDDEN, Translation :: get('Hidden'));
        $form->addElement('checkbox', self :: APPLICATION_NAME . '_opt_' . SurveyPublication :: PROPERTY_TEST, Translation :: get('TestCase'));
        $form->add_forever_or_timewindow('PublicationPeriod', self :: APPLICATION_NAME . '_opt_');
        
        $attributes = array();
        $attributes['search_url'] = Path :: get(WEB_PATH) . 'common/xml_feeds/xml_user_group_feed.php';
        $locale = array();
        $locale['Display'] = Translation :: get('ShareWith');
        $locale['Searching'] = Translation :: get('Searching');
        $locale['NoResults'] = Translation :: get('NoResults');
        $locale['Error'] = Translation :: get('Error');
        $attributes['locale'] = $locale;
        $attributes['defaults'] = array();
        $attributes['options'] = array('load_elements' => false);
        
        $form->add_receivers(self :: APPLICATION_NAME . '_opt_' . self :: PARAM_TARGET, Translation :: get('PublishFor'), $attributes);
        
        $form->addElement('category');
        $form->addElement('html', '<br />');
        $defaults[self :: APPLICATION_NAME . '_opt_forever'] = 1;
        $defaults[self :: APPLICATION_NAME . '_opt_' . self :: PARAM_TARGET_OPTION] = 0;
        $form->setDefaults($defaults);
    }

    function get_content_object_publication_locations($content_object)
    {
        $allowed_types = array(Announcement :: get_type_name());
        
        $type = $content_object->get_type();
        if (in_array($type, $allowed_types))
        {
            
            $locations = array();
            
            $locations[0] = Translation :: get('RootOrganiser');
            
            return $locations;
        }
        
        return array();
    }

    function publish_content_object($content_object, $location, $attributes)
    {
        
        //        if (! InternshipOrganizerRights :: is_allowed(InternshipOrganizerRights :: ADD_RIGHT, 'publication_browser', 'internship_organizer_component'))
        //        {
        //            return Translation :: get('NoRightsForInternshipOrganizerPublication');
        //        }
        

        //        $publication = new SurveyPublication();
        //        $publication->set_content_object($content_object->get_id());
        //        $publication->set_publisher(Session :: get_user_id());
        //        $publication->set_published(time());
        //        $publication->set_category($location);
        //        
        //        if ($attributes[SurveyPublication :: PROPERTY_HIDDEN] == 1)
        //        {
        //            $publication->set_hidden(1);
        //        }
        //        else
        //        {
        //            $publication->set_hidden(0);
        //        }
        //        
        //        if ($attributes['forever'] == 1)
        //        {
        //            $publication->set_from_date(0);
        //            $publication->set_to_date(0);
        //        }
        //        else
        //        {
        //            $publication->set_from_date(Utilities :: time_from_datepicker($attributes['from_date']));
        //            $publication->set_to_date(Utilities :: time_from_datepicker($attributes['to_date']));
        //        }
        //        
        //        if ($attributes[SurveyPublication :: PROPERTY_TEST] == 1)
        //        {
        //            $publication->set_test(1);
        //        }
        //        else
        //        {
        //            $publication->set_test(0);
        //        }
        //        
        //        if ($attributes[self :: PARAM_TARGET_OPTION] != 0)
        //        {
        //            $user_ids = $attributes[self :: PARAM_TARGET_ELEMENTS]['user'];
        //            $group_ids = $attributes[self :: PARAM_TARGET_ELEMENTS]['group'];
        //        }
        //        else
        //        {
        //            $users = UserDataManager :: get_instance()->retrieve_users();
        //            $user_ids = array();
        //            while ($user = $users->next_result())
        //            {
        //                $user_ids[] = $user->get_id();
        //            }
        //        }
        //        
        //        $publication->set_target_users($user_ids);
        //        $publication->set_target_groups($group_ids);
        //        
        //        $publication->create();
        return Translation :: get('PublicationCreated');
    }

}
?>