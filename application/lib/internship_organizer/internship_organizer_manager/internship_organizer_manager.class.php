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
require_once Path :: get_application_path() . 'lib/internship_organizer/period_manager/period_manager.class.php';

class InternshipOrganizerManager extends WebApplication
{
    const APPLICATION_NAME = 'internship_organizer';

    const ACTION_ORGANISATION = 'organisation';
    const ACTION_AGREEMENT = 'agreement';
    const ACTION_CATEGORY = 'category';
    const ACTION_APPLICATION_CHOOSER = 'application_chooser';
    const ACTION_REGION = 'region';
    const ACTION_PERIOD = 'period';

    const DEFAULT_ACTION = self :: ACTION_APPLICATION_CHOOSER;

    /**
     * Constructor
     * @param User $user The current user
     */
    function InternshipOrganizerManager($user = null)
    {
        parent :: __construct($user);
        $this->parse_input_from_table();
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

    function add_publication_attributes_elements($form)
    {
        //        $form->addElement('category', Translation :: get('PublicationDetails'));
        //        $form->addElement('checkbox', self :: APPLICATION_NAME . '_opt_' . SurveyPublication :: PROPERTY_HIDDEN, Translation :: get('Hidden'));
        //        $form->addElement('checkbox', self :: APPLICATION_NAME . '_opt_' . SurveyPublication :: PROPERTY_TEST, Translation :: get('TestCase'));
        //        $form->add_forever_or_timewindow('PublicationPeriod', self :: APPLICATION_NAME . '_opt_');


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

    /**
     * Helper function for the Application class,
     * pending access to class constants via variables in PHP 5.3
     * e.g. $name = $class :: DEFAULT_ACTION
     *
     * DO NOT USE IN THIS APPLICATION'S CONTEXT
     * Instead use:
     * - self :: DEFAULT_ACTION in the context of this class
     * - YourApplicationManager :: DEFAULT_ACTION in all other application classes
     */
    function get_default_action()
    {
        return self :: DEFAULT_ACTION;
    }

}
?>