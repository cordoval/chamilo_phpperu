<?php
/**
 * $Id: user_view_form.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.forms
 * @author Sven Vanpoucke
 */
class ContentObjectShareForm extends FormValidator
{
	const PARAM_RIGHTS = 'rights';
	const PARAM_TARGET = 'target_users_and_groups';
    const PARAM_TARGET_ELEMENTS = 'target_users_and_groups_elements';
    const PARAM_TARGET_OPTION = 'target_users_and_groups_option';
	
    const TYPE_CREATE = 1;
    const TYPE_EDIT = 2;

    private $content_objects;
    private $form_type;
    private $user;

    function ContentObjectShareForm($form_type, $content_objects = array(), $user, $action)
    {
        parent :: __construct('content_object_share_form', 'post', $action);

        $this->content_objects = $content_objects;
        $this->form_type = $form_type;
        $this->user = $user;
        
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
        $this->addElement('select', self :: PARAM_RIGHTS, Translation :: get('Rights'), ContentObjectShare :: get_rights());
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

        $attributes = array();
        $attributes['search_url'] = Path :: get(WEB_PATH) . 'common/xml_feeds/xml_user_group_feed.php';
        $locale = array();
        $locale['Display'] = Translation :: get('SelectUsersGroups');
        $locale['Searching'] = Translation :: get('Searching');
        $locale['NoResults'] = Translation :: get('NoResults');
        $locale['Error'] = Translation :: get('Error');
        $attributes['locale'] = $locale;
        $attributes['exclude'] = array('user_' . $this->user->get_id());
        $attributes['defaults'] = array();
        $attributes['nodesSelectable'] = true;

        $legend_items = array();
        $legend_items[] = new ToolbarItem(Translation :: get('User'), Theme :: get_common_image_path() . 'treemenu/user.png', null, ToolbarItem :: DISPLAY_ICON_AND_LABEL, false, 'legend');
        $legend_items[] = new ToolbarItem(Translation :: get('Group'), Theme :: get_common_image_path() . 'treemenu/group.png', null, ToolbarItem :: DISPLAY_ICON_AND_LABEL, false, 'legend');

        $legend = new Toolbar();
        $legend->set_items($legend_items);
        $legend->set_type(Toolbar :: TYPE_HORIZONTAL);

        $this->add_receivers(self :: PARAM_TARGET, Translation :: get('PublishFor'), $attributes, 'Everybody', $legend);
        
        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Create'), array('class' => 'positive'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    function create_content_object_share()
    {
        
    }
    
    function update_content_object_share()
    {
    	
    }

    /**
     * Sets default values.
     * @param array $defaults Default values for this form's parameters.
     */
    function setDefaults($defaults = array ())
    {
        parent :: setDefaults($defaults);
    }
}
?>