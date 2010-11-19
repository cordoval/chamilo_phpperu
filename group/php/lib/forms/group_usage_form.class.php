<?php
namespace group;

use common\libraries\Translation;
use common\libraries\Utilities;
use common\libraries\FormValidator;

/**
 * @author Hans De Bisschop
 *
 */
class GroupUsageForm extends FormValidator
{
    const GROUP_USAGE = 'group_usage';

    /**
     * @var Group
     */
    private $group;

    /**
     * @var User
     */
    private $user;

    /**
     * @var string|null
     */
    private $application;

    /**
     * @param string $action
     * @param Group $group
     * @param User $user
     * @param string|null $application
     */
    function __construct($action, Group $group, User $user, $application = null)
    {
        parent :: __construct('group_usage_settings', 'post', $action);

        $this->group = $group;
        $this->user = $user;
        $this->application = $application;

        $this->build_form();
        $this->setDefaults();
    }

    function build_form()
    {
        // RightsTemplates element finder
        $group = $this->group;
        $group_rights_templates = array();

        //        $linked_rights_templates = $group->get_rights_templates();
        //        $group_rights_templates = RightsUtilities :: rights_templates_for_element_finder($linked_rights_templates);
        //
        //        $rights_templates = RightsDataManager :: get_instance()->retrieve_rights_templates();
        //        while ($rights_template = $rights_templates->next_result())
        //        {
        //            $defaults[$rights_template->get_id()] = array('title' => $rights_template->get_name(), 'description', $rights_template->get_description(), 'class' => 'rights_template');
        //        }


        $url = Path :: get(WEB_PATH) . 'group/xml_feeds/xml_group_feed.php';
        $locale = array();
        $locale['Display'] = Translation :: get('SelectUsableGroup');
        $locale['Searching'] = Translation :: get('Searching', null , Utilities :: COMMON_LIBRARIES);
        $locale['NoResults'] = Translation :: get('NoResults', null , Utilities :: COMMON_LIBRARIES);
        $locale['Error'] = Translation :: get('Error', null , Utilities :: COMMON_LIBRARIES);
        $hidden = true;

        $elem = $this->addElement('element_finder', self :: GROUP_USAGE, null, $url, $locale, $group_rights_templates);
        $elem->setDefaults($defaults);
        //        $elem->setDefaultCollapsed(count($group_rights_templates) == 0);


        $elem = $this->addElement('hidden', GroupUseGroup :: PROPERTY_REQUEST_GROUP_ID, $this->group);
        $elem = $this->addElement('hidden', GroupUseGroup :: PROPERTY_APPLICATION, $this->application);

        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Create', null , Utilities :: COMMON_LIBRARIES), array('class' => 'positive'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset', null , Utilities :: COMMON_LIBRARIES), array('class' => 'normal empty'));

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    function update_group_usage()
    {
        $values = $this->exportValues();

        if (isset($values[self :: GROUP_USAGE]) && isset($values[self :: GROUP_USAGE]['group']))
        {
            foreach ($values[self :: GROUP_USAGE]['group'] as $group_id)
            {
                $group_use_group = new GroupUseGroup();
                $group_use_group->set_request_group_id($values[GroupUseGroup :: PROPERTY_REQUEST_GROUP_ID]);
                $group_use_group->set_application($values[GroupUseGroup :: PROPERTY_APPLICATION]);
                $group_use_group->set_use_group_id($group_id);
                if (! $group_use_group->create())
                {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Sets default values.
     * @param array $defaults Default values for this form's parameters.
     */
    function setDefaults($defaults = array ())
    {
        parent :: setDefaults($defaults);
    }

    /**
     * @return Group
     */
    function get_group()
    {
        return $this->group;
    }

    /**
     * @return User
     */
    function get_user()
    {
        return $this->user;
    }
}
?>