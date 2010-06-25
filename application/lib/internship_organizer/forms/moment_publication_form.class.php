<?php
require_once dirname(__FILE__) . '/../publication.class.php';
require_once dirname(__FILE__) . '/../user_type.class.php';
require_once dirname(__FILE__) . '/../publication_type.class.php';
require_once dirname(__FILE__) . '/../publication_place.class.php';

class InternshipOrganizerMomentPublicationForm extends FormValidator
{
    const TYPE_SINGLE = 1;
    const TYPE_MULTI = 2;
    
    const PARAM_TARGET = 'moment_rel_users';
    
    private $publication;
    private $content_object;
    private $agreement_id;
    private $user;

    function InternshipOrganizerMomentPublicationForm($form_type, $content_object, $user, $action)
    {
        parent :: __construct('agreement_publication_settings', 'post', $action);
        
        $this->content_object = $content_object;
        $this->user = $user;
        $this->form_type = $form_type;
        
        $this->agreement_id = $_GET[InternshipOrganizerAgreementManager :: PARAM_AGREEMENT_ID];
        
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
        
        $this->addElement('select', InternshipOrganizerPublication :: PROPERTY_PUBLICATION_TYPE, Translation :: get('InternshipOrganizerTypeOfPublication'), $this->get_type_of_documents());
        $this->addRule(InternshipOrganizerPublication :: PROPERTY_PUBLICATION_TYPE, Translation :: get('ThisFieldIsRequired'), 'required');
        
        $url = Path :: get(WEB_PATH) . 'application/lib/internship_organizer/xml_feeds/xml_moment_feed.php?agreement_id='.$this->agreement_id;
            
        $locale = array();
        $locale['Display'] = Translation :: get('ChoosePeriods');
        $locale['Searching'] = Translation :: get('Searching');
        $locale['NoResults'] = Translation :: get('NoResults');
        $locale['Error'] = Translation :: get('Error');
        $elem = $this->addElement('element_finder', self :: PARAM_TARGET, Translation :: get('Periods'), $url, $locale, array());
        $elem->setDefaults($defaults);
        $elem->setDefaultCollapsed(false);
    
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
        
        $moment_rel_user_ids = $values[self :: PARAM_TARGET];
        $ids = unserialize($values['ids']);
        $succes = false;
        
        if (count($moment_rel_user_ids))
        {
            
            foreach ($moment_rel_user_ids as $moment_rel_user_id)
            {
                $mu_ids = explode( '|', $moment_rel_user_id);
            	
            	$moment = InternshipOrganizerDataManager :: get_instance()->retrieve_moment($mu_ids[0]);
                
                $target_users = array();
				$target_users[] = $mu_ids[1];
                
                foreach ($ids as $id)
                {
                    $pub = new InternshipOrganizerPublication();
                    $pub->set_content_object($id);
                    $pub->set_publisher_id($this->user->get_id());
                    $pub->set_published(time());
                    $pub->set_from_date($moment->get_begin());
                    $pub->set_to_date($moment->get_end());
                    $pub->set_publication_place(InternshipOrganizerPublicationPlace :: MOMENT);
                    $pub->set_place_id($moment->get_id());
                    $pub->set_publication_type($values[InternshipOrganizerPublication :: PROPERTY_PUBLICATION_TYPE]);
                    $pub->set_target_users($target_users);
                    
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