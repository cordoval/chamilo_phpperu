<?php
/**
 * $Id: group_move_form.class.php 224 2009-11-13 14:40:30Z kariboe $
 * @package groups.lib.forms
 */

class GroupMoveForm extends FormValidator
{
    const PROPERTY_LOCATION = 'location';
    
    private $group;
    private $locations = array();
    private $level = 1;
    private $gdm;

    function GroupMoveForm($group, $action, $user)
    {
        parent :: __construct('group_move', 'post', $action);
        $this->group = $group;
        
        $this->gdm = GroupDataManager :: get_instance();
        
        $this->build_form();
        
        $this->setDefaults();
    }

    function build_form()
    {
        $this->addElement('select', self :: PROPERTY_LOCATION, Translation :: get('NewLocation'), $this->get_groups());
        //$this->addElement('submit', 'group_export', Translation :: get('Ok'));
        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Move'), array('class' => 'positive move'));
        //$buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));
        

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    function move_group()
    {
        $new_parent = $this->exportValue(self :: PROPERTY_LOCATION);
        $this->group->set_parent($new_parent);
        return $this->group->update();
    }

    function get_new_parent()
    {
        return $this->exportValue(self :: PROPERTY_LOCATION);
    }

    function get_groups()
    {
        $group = $this->group;
        
        $group_menu = new GroupMenu($group->get_id(), null, true, true);
        $renderer = new OptionsMenuRenderer();
        $group_menu->render($renderer, 'sitemap');
        return $renderer->toArray();
    }

    /**
     * Sets default values. 
     * @param array $defaults Default values for this form's parameters.
     */
    function setDefaults($defaults = array ())
    {
        $group = $this->group;
        $defaults[self :: PROPERTY_LOCATION] = $group->get_parent();
        parent :: setDefaults($defaults);
    }
}
?>