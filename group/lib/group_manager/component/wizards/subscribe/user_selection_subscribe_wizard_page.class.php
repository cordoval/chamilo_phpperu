<?php
/**
 * $Id: user_selection_subscribe_wizard_page.class.php 141 2009-11-10 07:44:45Z kariboe $
 * @package groups.lib.group_manager.component.wizards.subscribe
 */
require_once dirname(__FILE__) . '/subscribe_wizard_page.class.php';
/**
 * This form can be used to let the user select publications in the course.
 */
class UserSelectionSubscribeWizardPage extends SubscribeWizardPage
{

    function buildForm()
    {
        $url = Path :: get(WEB_PATH) . 'user/xml_feeds/xml_user_feed.php';
        
        $locale = array();
        $locale['Display'] = Translation :: get('SelectUsers');
        $locale['Searching'] = Translation :: get('Searching');
        $locale['NoResults'] = Translation :: get('NoResults');
        $locale['Error'] = Translation :: get('Error');
        $hidden = false;
        
        $elem = $this->addElement('element_finder', 'users', Translation :: get('Users'), $url, $locale, null);
        //$elem->excludeElements(array($this->form_user->get_id()));
        //$elem->setDefaultCollapsed(false);
        //$this->addFormRule(array('UserSelectionSubscribeWizardPage','count_selected_users'));
        $prevnext[] = & $this->createElement('submit', $this->getButtonName('back'), '<< ' . Translation :: get('Previous'));
        $prevnext[] = & $this->createElement('submit', $this->getButtonName('next'), Translation :: get('Next') . ' >>');
        $this->addGroup($prevnext, 'buttons', '', '&nbsp;', false);
        $this->setDefaultAction('next');
        $this->_formBuilt = true;
    }

    /**
     * Returns the number of selected publications
     * @param array $values
     */
    function count_selected_users($values)
    {
        if (isset($values['publications']))
        {
            return true;
        }
        return array('buttons' => Translation :: get('SelectPublications'));
    }
}
?>