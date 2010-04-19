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

    private $content_object;
    private $publication;
    private $user;

    
    function PeerAssessmentPublicationForm($form_type, $content_object, $user, $action)
    {
        parent :: __construct('peer_assessment_publication_settings', 'post', $action);

        $this->content_object = $content_object;
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
		$attributes['search_url'] = Path :: get(WEB_PATH).'common/xml_feeds/xml_user_group_feed.php';
      
        $locale = array();
        $locale['Display'] = Translation :: get('SelectRecipients');
        $locale['Searching'] = Translation :: get('Searching');
        $locale['NoResults'] = Translation :: get('NoResults');
        $locale['Error'] = Translation :: get('Error');
        $attributes['locale'] = $locale;
        $attributes['exclude'] = array('user_200');// . $this->user->get_id());
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
    
    
	function create_content_object_publication()
    {
    	$values = $this->exportValues();
        if ($values[self :: PARAM_FOREVER] != 0)
        {
            $from = $to = 0;
        }
        else
        {
            $from = Utilities :: time_from_datepicker($values[self :: PARAM_FROM_DATE]);
            $to = Utilities :: time_from_datepicker($values[self :: PARAM_TO_DATE]);
        }
        $hidden = ($values[PeerAssessmentPublication :: PROPERTY_HIDDEN] ? 1 : 0);

        $users = $values[self :: PARAM_TARGET_ELEMENTS]['user'];
        $groups = $values[self :: PARAM_TARGET_ELEMENTS]['group'];

        $pub = new PeerAssessmentPublication();
        $pub->set_content_object($this->content_object);
        $pub->set_publisher($this->user->get_id());
        $pub->set_published(time());
        $pub->set_from_date($from);
        $pub->set_to_date($to);
        $pub->set_hidden($hidden);
        $pub->set_target_users($users);
        $pub->set_target_groups($groups);
        

    	if (! $pub->create())
        {
            return false;
        }
        else
        {
            $this->publication = $pub;
            return true;
        }
        
		if(Request :: post('evaluation'))
		{
        	require_once dirname (__FILE__) . '/../../gradebook/forms/gradebook_internal_item_form.class.php';
        	$gradebook_internal_item_form = new GradebookInternalItemForm();
        	$gradebook_internal_item_form->create_internal_item($pub->get_id(), true);
		}
    }
  

    function update_content_object()
    {
        $content_object = $this->content_object;
        $content_object->set_content_object($content_object->get_content_object()->get_id());

        $values = $this->exportValues();

        if ($values[self :: PARAM_FOREVER] != 0)
        {
            $content_object->set_from_date(0);
            $content_object->set_to_date(0);
        }
        else
        {
            $content_object->set_from_date(Utilities :: time_from_datepicker($values[self :: PARAM_FROM_DATE]));
            $content_object->set_to_date(Utilities :: time_from_datepicker($values[self :: PARAM_TO_DATE]));
        }
        $content_object->set_hidden($values[self :: PARAM_HIDDEN] ? 1 : 0);
        $content_object->set_publisher(0);//$this->user->get_id());
        $content_object->set_published(time());
        $content_object->set_modified(time());
        $content_object->set_display_order(0);

        return $content_object->update();
    }
    
    
    
	function set_publication_values($publication)
    {
    	
        $this->publication = $publication;
        $this->addElement('hidden', 'pid');
        $this->addElement('hidden', 'action');
        $defaults['action'] = 'edit';
        $defaults['pid'] = $publication->get_id();
        $defaults['from_date'] = $publication->get_from_date();
        $defaults['to_date'] = $publication->get_to_date();
        if ($defaults['from_date'] != 0)
        {
            $defaults['forever'] = 0;
        }
        $defaults['hidden'] = $publication->is_hidden();

        $udm = UserDataManager :: get_instance();
        $gdm = GroupDataManager :: get_instance();

        $target_groups = $this->publication->get_target_groups();
        $target_users = $this->publication->get_target_users();

        $defaults[self :: PARAM_TARGET_ELEMENTS] = array();
        foreach ($target_groups as $target_group)
        {
            $group = $gdm->retrieve_group($target_group);

            $selected_group = array();
            $selected_group['id'] = 'group_' . $group->get_id();
            $selected_group['classes'] = 'type type_group';
            $selected_group['title'] = $group->get_name();
            $selected_group['description'] = $group->get_description();

            $defaults[self :: PARAM_TARGET_ELEMENTS][$selected_group['id']] = $selected_group;
        }
        foreach ($target_users as $target_user)
        {
            $user = $udm->retrieve_user($target_user);

            $selected_user = array();
            $selected_user['id'] = 'user_' . $user->get_id();
            $selected_user['classes'] = 'type type_user';
            $selected_user['title'] = $user->get_fullname();
            $selected_user['description'] = $user->get_username();

            $defaults[self :: PARAM_TARGET_ELEMENTS][$selected_user['id']] = $selected_user;
        }

        if (count($defaults[self :: PARAM_TARGET_ELEMENTS]) > 0)
        {
            $defaults[self :: PARAM_TARGET_OPTION] = '1';
        }

        $active = $this->getElement(self :: PARAM_TARGET_ELEMENTS);
        $active->_elements[0]->setValue(serialize($defaults[self :: PARAM_TARGET_ELEMENTS]));

        parent :: setDefaults($defaults);
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