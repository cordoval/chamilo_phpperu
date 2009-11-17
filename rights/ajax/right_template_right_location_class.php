<?php
/**
 * $Id: right_template_right_location_class.php 214 2009-11-13 13:57:37Z vanpouckesven $
 * @package rights.ajax
 */
$this_section = 'rights';

require_once dirname(__FILE__) . '/../../common/global.inc.php';

Translation :: set_application($this_section);
Theme :: set_application($this_section);

if (! Authentication :: is_valid())
{
    return 0;
}

$user = UserDataManager :: get_instance()->retrieve_user(Session :: get_user_id());
// TODO: User real rights_templates'n'rights here
if (! $user->is_platform_admin())
{
    return 0;
}

$rights = $_POST['rights'];
$rights = explode('_', $rights);

$right = $rights['1'];
$rights_template = $rights['2'];
$location = $rights['3'];

if (isset($rights_template) && isset($right) && isset($location))
{
    $rdm = RightsDataManager :: get_instance();
    $location = $rdm->retrieve_location($location);
    $locked_parent = $location->get_locked_parent();
    
    if (isset($locked_parent))
    {
        // TODO: In theory this shouldn't happen, but what if someone else does lock a parent at the same time ? This affects the entire page ... not limited to this functionality.
    //$value = $this->is_allowed($id, $rights_template->get_id(), $locked_parent->get_id());
    //$html[] = '<a href="'. $this->get_url(array('application' => $this->application, 'location' => $locked_parent->get_id())) .'">' . ($value == 1 ? '<img src="'. Theme :: get_common_image_path() .'action_setting_true_locked.png" title="'. Translation :: get('LockedTrue') .'" />' : '<img src="'. Theme :: get_common_image_path() .'action_setting_false_locked.png" title="'. Translation :: get('LockedFalse') .'" />') . '</a>';
    }
    else
    {
        $value = RightsUtilities :: get_rights_template_right_location($right, $rights_template, $location->get_id());
        
        if (! $value)
        {
            if ($location->inherits())
            {
                $inherited_value = RightsUtilities :: is_allowed_for_rights_template($rights_template, $right, $location);
                
                if ($inherited_value)
                {
                    echo 'rightInheritTrue';
                }
                else
                {
                    echo 'rightFalse';
                }
            }
            else
            {
                echo 'rightFalse';
            }
        }
        else
        {
            echo 'rightTrue';
        }
    }
}
?>
