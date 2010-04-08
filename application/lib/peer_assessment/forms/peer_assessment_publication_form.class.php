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

    const PARAM_CATEGORY_ID = 'category';
    const PARAM_TARGET = 'target_users_and_groups';
    const PARAM_TARGET_ELEMENTS = 'target_users_and_groups_elements';
    const PARAM_TARGET_OPTION = 'target_users_and_groups_option';
    const PARAM_FOREVER = 'forever';
    const PARAM_FROM_DATE = 'from_date';
    const PARAM_TO_DATE = 'to_date';
    const PARAM_HIDDEN = 'hidden';

    private $peer_assessment_publication;
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
        //$attributes['search_url'] = Path :: get(WEB_PATH).'application/lib/weblcms/xml_feeds/xml_course_user_group_feed.php?course=' . $this->course->get_id();
        $locale = array();
        $locale['Display'] = Translation :: get('SelectRecipients');
        $locale['Searching'] = Translation :: get('Searching');
        $locale['NoResults'] = Translation :: get('NoResults');
        $locale['Error'] = Translation :: get('Error');
        $attributes['locale'] = $locale;
        $attributes['exclude'] = array('user_' . $this->user->get_id());
        $attributes['defaults'] = array();
        
        // Gradebook
        if(WebApplication :: is_active('gradebook'))
        {
        	require_once dirname (__FILE__) . '/../../gradebook/forms/gradebook_internal_item_form.class.php';
        	$gradebook_internal_item_form = new GradebookInternalItemForm();
        	$gradebook_internal_item_form->build_evaluation_question($this);
        }
        
        $this->add_receivers(self :: PARAM_TARGET, Translation :: get('PublishFor'), $attributes);

        $this->add_forever_or_timewindow();
        $this->addElement('checkbox', self :: PARAM_HIDDEN, Translation :: get('Hidden'));
    }

    function build_editing_form()
    {
        $this->build_basic_form();

        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Update'), array('class' => 'positive update'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    function build_creation_form()
    {
        $this->build_basic_form();

        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Create'), array('class' => 'positive'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    function update_peer_assessment_publication()
    {
        $peer_assessment_publication = $this->peer_assessment_publication;
        $peer_assessment_publication->set_content_object($peer_assessment_publication->get_content_object()->get_id());

        $values = $this->exportValues();

        if ($values[self :: PARAM_FOREVER] != 0)
        {
            $peer_assessment_publication->set_from_date(0);
            $peer_assessment_publication->set_to_date(0);
        }
        else
        {
            $peer_assessment_publication->set_from_date(Utilities :: time_from_datepicker($values[self :: PARAM_FROM_DATE]));
            $peer_assessment_publication->set_to_date(Utilities :: time_from_datepicker($values[self :: PARAM_TO_DATE]));
        }
        $peer_assessment_publication->set_hidden($values[self :: PARAM_HIDDEN] ? 1 : 0);
        $peer_assessment_publication->set_publisher($this->user->get_id());
        $peer_assessment_publication->set_published(time());
        $peer_assessment_publication->set_modified(time());
        $peer_assessment_publication->set_display_order(0);

        return $peer_assessment_publication->update();
    }
    
    function create_peer_assessment_publication($object, $values)
    {
    	$peer_assessment_publication = new PeerAssessmentPublication();
		$peer_assessment_publication->set_content_object($object);
		
        if ($values['forever'] != 0)
        {
            $peer_assessment_publication->set_from_date(0);
            $peer_assessment_publication->set_to_date(0);
        }
        else
        {
            $peer_assessment_publication->set_from_date(Utilities :: time_from_datepicker($values['from_date']));
            $peer_assessment_publication->set_to_date(Utilities :: time_from_datepicker($values['to_date']));
        }
        $peer_assessment_publication->set_hidden($values['hidden'] ? 1 : 0);
        $peer_assessment_publication->set_publisher(Session :: get_user_id());
        $peer_assessment_publication->set_published(time());
        $peer_assessment_publication->set_modified(time());
        $peer_assessment_publication->set_display_order(0);
        $peer_assessment_publication->create();
        
        // Gradebook
		if($values['evaluation'] == true)
		{
        	$gradebook_internal_item_form = new GradebookInternalItemForm();
        	$gradebook_internal_item_form->create_internal_item($peer_assessment_publication->get_id(), false);
		} 
		
        return $peer_assessment_publication;
    }

    /**
     * Sets default values.
     * @param array $defaults Default values for this form's parameters.
     */
    function setDefaults()
    {
        $defaults = array();
        $defaults[self :: PARAM_TARGET_OPTION] = 0;
        $defaults[self :: PARAM_FOREVER] = 1;
        parent :: setDefaults($defaults);
    }
}
?>