<?php
require_once dirname(__FILE__) . '/../peer_assessment_publication.class.php';

/**
 * This class describes the form for a PeerAssessmentPublication object.
 * @author Nick Van Loocke
 **/
class PeerAssessmentPublicationForm extends FormValidator
{
    const TYPE_CREATE = 1;
    const TYPE_EDIT = 2;
    
    private $peer_assessment_publication;
    private $categories;
    private $user;

    function PeerAssessmentPublicationForm($form_type, $peer_assessment_publication, $action, $user)
    {
        parent :: __construct('peer_assessment_publication_settings', 'post', $action);
        
        $this->peer_assessment_publication = $peer_assessment_publication;
        $this->user = $user;
        $this->form_type = $form_type;
        
        if ($this->form_type == self :: TYPE_EDIT)
        {
            $this->build_editing_form();
        }
        elseif ($this->form_type == self :: TYPE_CREATE)
        {
            $this->build_creation_form();
        }
        
        $this->setDefaults();
    }

    function build_basic_form()
    {
        $attributes = array();
        $attributes['search_url'] = Path :: get(WEB_PATH) . 'common/xml_feeds/xml_user_group_feed.php';
        $locale = array();
        $locale['Display'] = Translation :: get('SelectRecipients');
        $locale['Searching'] = Translation :: get('Searching');
        $locale['NoResults'] = Translation :: get('NoResults');
        $locale['Error'] = Translation :: get('Error');
        $attributes['locale'] = $locale;
        $attributes['exclude'] = array('user_' . $this->user->get_id());
        
        $pub = $this->peer_assessment_publication;
        $udm = UserDataManager :: get_instance();
        $gdm = GroupDataManager :: get_instance();

        
        $this->add_select(PeerAssessmentPublication :: PROPERTY_CATEGORY_ID, Translation :: get('Category'), $this->get_peer_assessment_publication_categories(), true);
        
        $this->add_receivers('target', Translation :: get('PublishFor'), $attributes);
        
        $this->add_forever_or_timewindow();
        $this->addElement('checkbox', PeerAssessmentPublication :: PROPERTY_HIDDEN, Translation :: get('Hidden'));
    }

    function build_editing_form()
    {
		$pub = $this->peer_assessment_publication;
        
        $this->build_basic_form();
        
        $this->addElement('hidden', PeerAssessmentPublication :: PROPERTY_ID);
        
        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Update'), array('class' => 'positive update'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));
        
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
        
        $defaults = array();
        
        if ($pub->get_from_date() == 0 && $pub->get_to_date() == 0)
        {
            $defaults['forever'] = 1;
        }
        else
        {
            $defaults['forever'] = 0;
        }
        
        if ($pub->get_target_groups() == 0 && $pub->get_target_users() == 0)
        {
            $defaults['target_option'] = 0;
        }
        else
        {
            $defaults['target_option'] = 1;
        }
        
        parent :: setDefaults($defaults);
    }

    function build_creation_form()
    {
		$this->build_basic_form();
        
        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Create'), array('class' => 'positive'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));
        
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
        
        $defaults = array();
        $defaults['target_option'] = 0;
        $defaults['forever'] = 1;
        parent :: setDefaults($defaults);
    }

    function update_peer_assessment_publication()
    {
		$peer_assessment_publication = $this->peer_assessment_publication;
        $values = $this->exportValues();
        
        if ($values['forever'] == 1)
        {
            $from = $to = 0;
        }
        else
        {
            $from = Utilities :: time_from_datepicker($values['from_date']);
            $to = Utilities :: time_from_datepicker($values['to_date']);
        }
        
        $peer_assessment_publication->set_hidden($values[PeerAssessmentPublication :: PROPERTY_HIDDEN]);
        $peer_assessment_publication->set_category_id($values[PeerAssessmentPublication :: PROPERTY_CATEGORY_ID]);
        
        return $peer_assessment_publication->update();
    }

    function create_peer_assessment_publications($objects)
    {
         $values = $this->exportValues();

        if ($values['forever'] == 1)
        {
            $from = $to = 0;
        }
        else
        {
            $from = Utilities :: time_from_datepicker($values['from_date']);
            $to = Utilities :: time_from_datepicker($values['to_date']);
        }
        
        $succes = true;
        
        foreach ($objects as $object)
        {
            $peer_assessment_publication = new PeerAssessmentPublication();
            $peer_assessment_publication->set_peer_assessment_id($object);
            $peer_assessment_publication->set_hidden($values[PeerAssessmentPublication :: PROPERTY_HIDDEN]);
            $peer_assessment_publication->set_author($this->user->get_id());
            $peer_assessment_publication->set_date(time());
            $peer_assessment_publication->set_category_id($values[PeerAssessmentPublication :: PROPERTY_CATEGORY_ID]);

            $succes &= $peer_assessment_publication->create();
        }
        
        return $succes;
    }

    /**
     * Sets default values.
     * @param array $defaults Default values for this form's parameters.
     */
    function setDefaults($defaults = array ())
    {
		$peer_assessment_publication = $this->peer_assessment_publication;    
        $defaults[PeerAssessmentPublication :: PROPERTY_HIDDEN] = $peer_assessment_publication->is_hidden();
        
        parent :: setDefaults($defaults);
    }
    

    function get_peer_assessment_publication_categories($parent = 0, $level = 1)
    {
        $fdm = PeerAssessmentDataManager :: get_instance();
        if ($parent == 0)
            $this->categories[0] = Translation :: get('Root');
        
        $condition = new EqualityCondition(PeerAssessmentPublicationCategory :: PROPERTY_PARENT, $parent);
        $categories = $fdm->retrieve_peer_assessment_publication_categories($condition);
        while ($category = $categories->next_result())
        {
            $this->categories[$category->get_id()] = str_repeat('__', $level) . ' ' . $category->get_name();
            $this->get_peer_assessment_publication_categories($category->get_id(), $level + 1);
        }
        
        return $this->categories;
    }
}
?>