<?php
/**
 * $Id: group_selection_subscribe_wizard_page.class.php 141 2009-11-10 07:44:45Z kariboe $
 * @package groups.lib.group_manager.component.wizards.subscribe
 */

require_once dirname(__FILE__) . '/subscribe_wizard_page.class.php';
/**
 * This form can be used to let the user select publications in the course.
 */
class GroupSelectionSubscribeWizardPage extends SubscribeWizardPage
{
    private $group;

    public function GroupSelectionSubscribeWizardPage($name, $parent, $group)
    {
        parent :: SubscribeWizardPage($name, $parent);
        $this->group = $group;
    }

    function buildForm()
    {
        $datamanager = UserDataManager :: get_instance();
        $groups = $this->get_parent()->retrieve_groups(null, null, null, new ObjectTableOrder(Group :: PROPERTY_NAME));
        $group_options = array();
        
        while ($group = $groups->next_result())
        {
            $group_options[$group->get_id()] = $group->get_name();
        }
        
        $this->addElement('select', 'Group', Translation :: get('Group'), $group_options);
        $this->addRule('Group', Translation :: get('ThisFieldIsRequired'), 'required');
        //$prevnext[] = & $this->createElement('submit', $this->getButtonName('back'), '<< '.Translation :: get('Previous'));
        $prevnext[] = & $this->createElement('submit', $this->getButtonName('next'), Translation :: get('Next') . ' >>');
        $this->addGroup($prevnext, 'buttons', '', '&nbsp;', false);
        $this->setDefaultAction('next');
        $this->set_defaults();
        $this->_formBuilt = true;
    }

    function set_defaults()
    {
        $defaults = array();
        $defaults['Group'] = Request :: get(GroupManager :: PARAM_GROUP_ID);
        $this->setDefaults($defaults);
    }
}
?>