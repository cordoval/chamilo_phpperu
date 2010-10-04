<?php
/**
 * $Id: forum_publication_form.class.php 195 2009-11-13 12:02:41Z chellee $
 * @package application.lib.forum.forms
 */
require_once dirname(__FILE__) . '/../forum_publication.class.php';

/**
 * This class describes the form for a ForumPublication object.
 * @author Sven Vanpoucke
 **/
class ForumPublicationForm extends FormValidator
{
    const TYPE_CREATE = 1;
    const TYPE_EDIT = 2;
    
    private $forum_publication;
    private $user;

    function ForumPublicationForm($form_type, $forum_publication, $action, $user)
    {
        parent :: __construct('forum_publication_settings', 'post', $action);
        
        $this->forum_publication = $forum_publication;
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
        $attributes['defaults'] = array();
        
        $pub = $this->forum_publication;
        $udm = UserDataManager :: get_instance();
        $gdm = GroupDataManager :: get_instance();
        
        /*foreach($pub->get_target_users() as $user_id)
		{
			$user = $udm->retrieve_user($user_id);
			$default = array(); 
			$default['id'] = 'user_' . $user_id;
        	$default['classes'] = 'type type_user';
        	$default['title'] = $user->get_fullname();
        	$default['description'] = $user->get_fullname();
        
			$attributes['defaults'][] = $default;
		}
		
   		foreach($pub->get_target_groups() as $group_id)
		{
			$group = $gdm->retrieve_group($group_id);
			$default = array(); 
			$default['id'] = 'group_' . $group_id;
        	$default['classes'] = 'type type_group';
        	$default['title'] = $group->get_name();
        	$default['description'] = $group->get_name();
        
			$attributes['defaults'][] = $default;
		}*/
    
        if(WebApplication :: is_active('gradebook'))
        {
        	if(PlatformSetting :: get_instance()->get('allow_evaluate_application_forum', 'gradebook'))
        	{
	        	require_once dirname (__FILE__) . '/../../gradebook/forms/gradebook_internal_item_form.class.php';
	        	$gradebook_internal_item_form = new GradebookInternalItemForm();
	        	$gradebook_internal_item_form->build_evaluation_question($this);
        	}
        }
        
        $this->add_select(ForumPublication :: PROPERTY_CATEGORY_ID, Translation :: get('Category'), $this->get_forum_publication_categories(), true);
        
        $this->add_receivers('target', Translation :: get('PublishFor'), $attributes);
        
        $this->add_forever_or_timewindow();
        $this->addElement('checkbox', ForumPublication :: PROPERTY_HIDDEN, Translation :: get('Hidden'));
    }

    function build_editing_form()
    {
        $pub = $this->forum_publication;
        
        $this->build_basic_form();
        
        $this->addElement('hidden', ForumPublication :: PROPERTY_ID);
        
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

    function update_forum_publication()
    {
        $forum_publication = $this->forum_publication;
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
        
        /*$forum_publication->set_from_date($from);
	    $forum_publication->set_to_date($to);*/
        $forum_publication->set_hidden($values[ForumPublication :: PROPERTY_HIDDEN]);
        $forum_publication->set_category_id($values[ForumPublication :: PROPERTY_CATEGORY_ID]);
        /*$forum_publication->set_target_groups($values['target_elements']['group']);
	    $forum_publication->set_target_users($values['target_elements']['user']);*/
        
        return $forum_publication->update();
    }

    function create_forum_publications($objects)
    {
        $values = $this->exportValues();
        
        //dump($values); exit();
        

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
            $forum_publication = new ForumPublication();
            $forum_publication->set_forum_id($object);
            /*$forum_publication->set_from_date($from);
	    	$forum_publication->set_to_date($to);*/
            $forum_publication->set_hidden($values[ForumPublication :: PROPERTY_HIDDEN]);
            $forum_publication->set_author($this->user->get_id());
            $forum_publication->set_date(time());
            $forum_publication->set_category_id($values[ForumPublication :: PROPERTY_CATEGORY_ID]);
            /*$forum_publication->set_target_groups($values['target_elements']['group']);
	    	$forum_publication->set_target_users($values['target_elements']['user']);*/
            $succes &= $forum_publication->create();
        }
    
		if($values['evaluation'] == true)
		{
        	$gradebook_internal_item_form = new GradebookInternalItemForm();
        	$gradebook_internal_item_form->create_internal_item($forum_publication->get_id(), false, 'C' . 0);
		}
        return $succes;
    }

    /**
     * Sets default values.
     * @param array $defaults Default values for this form's parameters.
     */
    function setDefaults($defaults = array ())
    {
        $forum_publication = $this->forum_publication;
        
        /*$defaults[ForumPublication :: PROPERTY_FROM_DATE] = $forum_publication->get_from_date();
    	$defaults[ForumPublication :: PROPERTY_TO_DATE] = $forum_publication->get_to_date();*/
        $defaults[ForumPublication :: PROPERTY_HIDDEN] = $forum_publication->is_hidden();
        
        parent :: setDefaults($defaults);
    }
    
    private $categories;

    function get_forum_publication_categories($parent = 0, $level = 1)
    {
        $fdm = ForumDataManager :: get_instance();
        if ($parent == 0)
            $this->categories[0] = Translation :: get('Root');
        
        $condition = new EqualityCondition(ForumPublicationCategory :: PROPERTY_PARENT, $parent);
        $categories = $fdm->retrieve_forum_publication_categories($condition);
        while ($category = $categories->next_result())
        {
            if(!ForumRights :: is_allowed_in_forums_subtree(ForumRights :: PUBLISH_RIGHT, $category->get_id()))
            {
				continue;            	
            }
        	$this->categories[$category->get_id()] = str_repeat('__', $level) . ' ' . $category->get_name();
            $this->get_forum_publication_categories($category->get_id(), $level + 1);
        }
        
        return $this->categories;
    }
}
?>