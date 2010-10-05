<?php
require_once dirname(__FILE__) . '/../publication.class.php';
require_once dirname(__FILE__) . '/../user_type.class.php';
require_once dirname(__FILE__) . '/../publication_type.class.php';
require_once dirname(__FILE__) . '/../publication_place.class.php';

class InternshipOrganizerLocationPublicationForm extends FormValidator
{
    const TYPE_SINGLE = 1;
    const TYPE_MULTI = 2;
    
    const PARAM_TARGET = 'locations';
    
    private $form_type;
    private $type;
    private $publication;
    private $content_object;
    private $user;

    function InternshipOrganizerLocationPublicationForm($form_type, $content_object, $user, $action, $type)
    {
        parent :: __construct('location_publication_settings', 'post', $action);
        
        $this->content_object = $content_object;
        $this->user = $user;
        $this->form_type = $form_type;
        $this->type = $type;
        
        switch ($this->form_type)
        {
            case self :: TYPE_SINGLE :
                $this->build_single_form();
                break;
            case self :: TYPE_MULTI :
                $this->build_multi_form();
                break;
        }
        
        $this->add_footer();
        $this->setDefaults();
    }

    function build_single_form()
    {
        $this->build_form();
    }

    function build_multi_form()
    {
        $this->build_form();
        $this->addElement('hidden', 'ids', serialize($this->content_object));
    }

    /**
     * Builds the form by adding the necessary form elements.
     */
    function build_form()
    {
        
        $this->addElement('text', InternshipOrganizerPublication :: PROPERTY_NAME, Translation :: get('Name'), array("size" => "50"));
        $this->addRule(InternshipOrganizerPublication :: PROPERTY_NAME, Translation :: get('ThisFieldIsRequired'), 'required');
        
        $this->add_html_editor(InternshipOrganizerPublication :: PROPERTY_DESCRIPTION, Translation :: get('Description'), false);
        
        $this->addElement('select', InternshipOrganizerPublication :: PROPERTY_PUBLICATION_TYPE, Translation :: get('InternshipOrganizerTypeOfPublication'), $this->get_type_of_documents());
        $this->addRule(InternshipOrganizerPublication :: PROPERTY_PUBLICATION_TYPE, Translation :: get('ThisFieldIsRequired'), 'required');
        
        $this->add_timewindow(InternshipOrganizerPublication :: PROPERTY_FROM_DATE, InternshipOrganizerPublication :: PROPERTY_TO_DATE, Translation :: get('From'), Translation :: get('To'), false);
        
        if ($this->type == InternshipOrganizerLocationPublisher :: MULTIPLE_LOCATION_TYPE)
        {
            $organisation_id = Request :: get(InternshipOrganizerOrganisationManager :: PARAM_ORGANISATION_ID);
            $url = Path :: get(WEB_PATH) . 'application/internship_organizer/php/xml_feeds/xml_location_feed.php?' . InternshipOrganizerOrganisationManager :: PARAM_ORGANISATION_ID . '=' . $organisation_id;
            
            $locale = array();
            $locale['Display'] = Translation :: get('ChooseLocations');
            $locale['Searching'] = Translation :: get('Searching');
            $locale['NoResults'] = Translation :: get('NoResults');
            $locale['Error'] = Translation :: get('Error');
            $elem = $this->addElement('element_finder', self :: PARAM_TARGET, Translation :: get('Locations'), $url, $locale, array());
            $elem->setDefaults($defaults);
            $elem->setDefaultCollapsed(false);
        }
    
    }

    function add_footer()
    {
        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Publish'), array('class' => 'positive'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    
    }

    function create_content_object_publications()
    {
        $values = $this->exportValues();
        
        if ($this->type == InternshipOrganizerlocationPublisher :: MULTIPLE_LOCATION_TYPE)
        {
            $location_ids = $values[self :: PARAM_TARGET]['location'];
        }
        else
        {
            $location_ids = array($_GET[InternshipOrganizerOrganisationManager :: PARAM_LOCATION_ID]);
        }
        
        $ids = unserialize($values['ids']);
        $succes = false;
        
        if (count($location_ids))
        {
            
            foreach ($ids as $id)
            {
                $content_object = RepositoryDataManager :: get_instance()->retrieve_content_object($id);
                
                foreach ($location_ids as $location_id)
                {
                    $pub = new InternshipOrganizerPublication();
                    $pub->set_name($values[InternshipOrganizerPublication :: PROPERTY_NAME]);
                    $pub->set_description($values[InternshipOrganizerPublication :: PROPERTY_DESCRIPTION]);
                    $pub->set_content_object_id($id);
                    $pub->set_content_object_type($content_object->get_type());
                    $pub->set_publisher_id($this->user->get_id());
                    $pub->set_published(time());
                    $pub->set_from_date($values[InternshipOrganizerPublication :: PROPERTY_FROM_DATE]);
                    $pub->set_to_date($values[InternshipOrganizerPublication :: PROPERTY_TO_DATE]);
                    $pub->set_publication_place(InternshipOrganizerPublicationPlace :: LOCATION);
                    $pub->set_place_id($location_id);
                    $pub->set_publication_type($values[InternshipOrganizerPublication :: PROPERTY_PUBLICATION_TYPE]);
                    
                    if (! $pub->create())
                    {
                        $succes = false;
                    }
                    else
                    {
                        $succes = true;
                    }
                }
            }
        }
        
        return $succes;
    }

    private function get_type_of_documents()
    {
        $type_of_publications = array();
        $type_of_publications[InternshipOrganizerPublicationType :: CONTRACT] = InternshipOrganizerPublicationType :: get_publication_type_name(InternshipOrganizerPublicationType :: CONTRACT);
        $type_of_publications[InternshipOrganizerPublicationType :: GENERAL] = InternshipOrganizerPublicationType :: get_publication_type_name(InternshipOrganizerPublicationType :: GENERAL);
        $type_of_publications[InternshipOrganizerPublicationType :: INFO] = InternshipOrganizerPublicationType :: get_publication_type_name(InternshipOrganizerPublicationType :: INFO);
        return $type_of_publications;
    }

    /**
     * Sets the default values of the form.
     *
     * By default the publication is for everybody who has access to the tool
     * and the publication will be available forever.
     */
    function setDefaults()
    {
        $defaults = array();
        $defaults[InternshipOrganizerPublication :: PROPERTY_PUBLICATION_TYPE] = InternshipOrganizerPublicationType :: GENERAL;
        parent :: setDefaults($defaults);
    }
}
?>