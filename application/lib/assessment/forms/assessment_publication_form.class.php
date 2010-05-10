<?php
require_once dirname(__FILE__) . '/../assessment_publication.class.php';
/**
 * $Id: assessment_publication_form.class.php 193 2009-11-13 11:53:37Z chellee $
 * @package application.lib.assessment.forms
 */
/**
 * This class describes the form for a AssessmentPublication object.
 * @author Sven Vanpoucke
 * @author 
 **/
class AssessmentPublicationForm extends FormValidator
{
    const TYPE_SINGLE = 1;
    const TYPE_MULTI = 2;

    const PARAM_TARGET = 'target_users_and_groups';
    const PARAM_TARGET_ELEMENTS = 'target_users_and_groups_elements';
    const PARAM_TARGET_OPTION = 'target_users_and_groups_option';
    const PARAM_FOREVER = 'forever';
    const PARAM_FROM_DATE = 'from_date';
    const PARAM_TO_DATE = 'to_date';

    private $publication;
    private $content_object;
    private $user;

    function AssessmentPublicationForm($form_type, $content_object, $user, $action)
    {
        parent :: __construct('assessment_publication_settings', 'post', $action);
        $this->content_object = $content_object;
        $this->user = $user;
        $this->form_type = $form_type;

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

    /**
     * Sets the publication. Use this function if you're using this form to
     * change the settings of a learning object publication.
     * @param ContentObjectPublication $publication
     */
    function set_publication($publication)
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
        $targets = array();

        $attributes = array();
        $attributes['search_url'] = Path :: get(WEB_PATH) . 'common/xml_feeds/xml_user_group_feed.php';
        $locale = array();
        $locale['Display'] = Translation :: get('ShareWith');
        $locale['Searching'] = Translation :: get('Searching');
        $locale['NoResults'] = Translation :: get('NoResults');
        $locale['Error'] = Translation :: get('Error');
        $attributes['locale'] = $locale;
        $attributes['exclude'] = array('user_' . $this->user->get_id());
        $attributes['defaults'] = array();
    	if(WebApplication :: is_active('gradebook'))
        {
        	if(PlatformSetting :: get_instance()->get('allow_evaluate_application_assessment', 'gradebook'))
        	{
	        	require_once dirname (__FILE__) . '/../../gradebook/forms/gradebook_internal_item_form.class.php';
	        	$gradebook_internal_item_form = new GradebookInternalItemForm();
	        	$gradebook_internal_item_form->build_evaluation_question($this);
        	}
        }
        $this->add_receivers(self :: PARAM_TARGET, Translation :: get('PublishFor'), $attributes);
        $this->add_forever_or_timewindow();
        $this->addElement('checkbox', AssessmentPublication :: PROPERTY_HIDDEN, Translation :: get('Hidden'));

    }

    function add_footer()
    {
        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Publish'), array('class' => 'positive publish'));
        $buttons[] = $this->createElement('style_submit_button', 'publish_and_build', Translation :: get('PublishAndBuild'), array('class' => 'positive build'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
        //$this->addElement('submit', 'submit', Translation :: get('Ok'));
    }

    /**
     * Creates a learning object publication using the values from the form.
     * @return ContentObjectPublication The new publication
     */
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
        $hidden = ($values[AssessmentPublication :: PROPERTY_HIDDEN] ? 1 : 0);

        $users = $values[self :: PARAM_TARGET_ELEMENTS]['user'];
        $groups = $values[self :: PARAM_TARGET_ELEMENTS]['group'];

        $pub = new AssessmentPublication();
        $pub->set_content_object($this->content_object->get_id());
        $pub->set_publisher($this->form_user->get_id());
        $pub->set_published(time());
        $pub->set_from_date($from);
        $pub->set_to_date($to);
        $pub->set_hidden($hidden);
        $pub->set_target_users($users);
        $pub->set_target_groups($groups);

        if ($pub->create())
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    function create_content_object_publications()
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
        $hidden = ($values[AssessmentPublication :: PROPERTY_HIDDEN] ? 1 : 0);

        $users = $values[self :: PARAM_TARGET_ELEMENTS]['user'];
        $groups = $values[self :: PARAM_TARGET_ELEMENTS]['group'];


        $ids = unserialize($values['ids']);

        foreach ($ids as $id)
        {
            $pub = new AssessmentPublication();
            $pub->set_content_object($id);
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
            }
			if(Request :: post('evaluation'))
			{
				
		        require_once dirname (__FILE__) . '/../../gradebook/forms/gradebook_internal_item_form.class.php';
		        $gradebook_internal_item_form = new GradebookInternalItemForm();
		        if($pub->get_publication_object()->get_type() == 'survey')
		        	$gradebook_internal_item_form->create_internal_item($pub->get_id());
		       	else
		        	$gradebook_internal_item_form->create_internal_item($pub->get_id(), true, 'C' . 0);
			}
        }
        return true;
    }

    function update_content_object_publication()
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
        $hidden = ($values[AssessmentPublication :: PROPERTY_HIDDEN] ? 1 : 0);

        $users = $values[self :: PARAM_TARGET_ELEMENTS]['user'];
        $groups = $values[self :: PARAM_TARGET_ELEMENTS]['group'];

        $pub = $this->publication;
        $pub->set_from_date($from);
        $pub->set_to_date($to);
        $pub->set_hidden($hidden);
        $pub->set_target_users($users);
        $pub->set_target_groups($groups);
        return $pub->update();
    }
    function get_publication()
    {
    	return $this->publication;
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
        $defaults[self :: PARAM_TARGET_OPTION] = 0;
        $defaults[self :: PARAM_FOREVER] = 1;
        parent :: setDefaults($defaults);
    }
}
?>