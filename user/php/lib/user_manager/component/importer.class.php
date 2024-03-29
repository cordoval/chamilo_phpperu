<?php
namespace user;

use common\libraries\Translation;
use common\libraries\AdministrationComponent;
use common\libraries\BreadcrumbTrail;
use common\libraries\PlatformSetting;
use common\libraries\Display;
use common\libraries\Utilities;
use common\libraries\Application;
/**
 * $Id: importer.class.php 211 2009-11-13 13:28:39Z vanpouckesven $
 * @package user.lib.user_manager.component
 */

class UserManagerImporterComponent extends UserManager implements AdministrationComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        if (!UserRights :: is_allowed_in_users_subtree(UserRights :: EDIT_RIGHT, 0))
        {
            $this->display_header();
            Display :: error_message(Translation :: get("NotAllowed", null, Utilities :: COMMON_LIBRARIES));
            $this->display_footer();
            exit();
        }

        $form = new UserImportForm(UserImportForm :: TYPE_IMPORT, $this->get_url(), $this->get_user());

        if ($form->validate())
        {
            $success = $form->import_users();
            $message = Translation :: get(($success ? 'CsvUsersProcessed' : 'CsvUsersNotProcessed'), array('COUNT' => $form->count_failed_items()));
            $this->redirect($message . '<br />' . $form->get_failed_csv(), ($success ? false : true), array(Application :: PARAM_ACTION => UserManager :: ACTION_IMPORT_USERS));
        }
        else
        {
            $this->display_header();
            $form->display();
            $this->display_extra_information();
            $this->display_footer();
        }
    }

    function display_extra_information()
    {
        $html = array();
        $html[] = '<p>' . Translation :: get('CSVMustLookLike') . ' (' . Translation :: get('MandatoryFields') . ')</p>';
        $html[] = '<blockquote>';
        $html[] = '<pre>';
        $text = '<b>action</b>;<b>lastname</b>;<b>firstname</b>;';

        if (PlatformSetting :: get('require_email', UserManager :: APPLICATION_NAME))
        {
        	$text .= '<b>email</b>;';
        }
        else
        {
        	$text .= 'email;';
        }

        $text .= '<b>username</b>;password;auth_source;<b>official_code</b>;phone;status;language;active;activation_date;expiration_date';
        $html[] = $text;

        $text = '<b>xxx</b>;<b>xxx</b>;<b>xxx</b>;';

    	if (PlatformSetting :: get('require_email', UserManager :: APPLICATION_NAME))
        {
        	$text .= '<b>xxx</b>;';
        }
        else
        {
        	$text .= 'xxx;';
        }

        $text .= '<b>xxx</b>;xxx;platform/ldap;<b>xxx</b>;xxx;1/5;xxx;1/0;date/0;date/0';
        $html[] = $text;
        $html[] = '</pre>';
        $html[] = '</blockquote>';

        $html[] = '<p>' . Translation :: get('XMLMustLookLike') . ' (' . Translation :: get('MandatoryFields') . ')</p>';
        $html[] = '<blockquote>';
        $html[] = '<pre>';
        $html[] = '&lt;?xml version=&quot;1.0&quot; encoding=&quot;ISO-8859-1&quot;?&gt;';
        $html[] = '';
        $html[] = '&lt;Contacts&gt;';
        $html[] = '    &lt;Contact&gt;';
        $html[] = '        <b>&lt;action&gt;A/U/D&lt;/action&gt;</b>';
        $html[] = '        <b>&lt;lastname&gt;xxx&lt;/lastname&gt;</b>';
        $html[] = '        <b>&lt;firstname&gt;xxx&lt;/firstname&gt;</b>';
        $html[] = '        <b>&lt;username&gt;xxx&lt;/username&gt;</b>';
        $html[] = '';
        $html[] = '        &lt;password&gt;xxx&lt;/password&gt;';

   		if (PlatformSetting :: get('require_email', UserManager :: APPLICATION_NAME))
        {
        	 $html[] = '        <b>&lt;email&gt;xxx&lt;/email&gt;</b>';
        }
        else
        {
        	 $html[] = '        &lt;email&gt;xxx&lt;/email&gt;';
        }

        $html[] = '        &lt;language&gt;xxx&lt;/language&gt;';
        $html[] = '';
        $html[] = '        &lt;status&gt;1/5&lt;/status&gt;';
        $html[] = '        &lt;active&gt;1/0&lt;/active&gt;';
        $html[] = '';
        $html[] = '        <b>&lt;official_code&gt;xxx&lt;/official_code&gt;</b>';
        $html[] = '        &lt;phone&gt;xxx&lt;/phone&gt;';
        $html[] = '';
        $html[] = '        &lt;activation_date&gt;YYYY-MM-DD HH:MM:SS/0&lt;/activation_date&gt;';
        $html[] = '        &lt;expiration_date&gt;YYYY-MM-DD HH:MM:SS/0&lt;/expiration_date&gt;';
        $html[] = '';
        $html[] = '        &lt;auth_source&gt;platform/ldap&lt;/auth_source&gt;';
        $html[] = '';
        $html[] = '    &lt;/Contact&gt;';
        $html[] = '&lt;/Contacts&gt;';
        $html[] = '</pre>';
        $html[] = '</blockquote>';

        $html[] = '<p>' . Translation :: get('Details') . '</p>';
        $html[] = '<blockquote>';
        $html[] = '<u><b>' . Translation :: get('Action') . '</u></b>';
        $html[] = '<br />A: ' . Translation :: get('Add', null, Utilities :: COMMON_LIBRARIES);
        $html[] = '<br />U: ' . Translation :: get('Update', null, Utilities :: COMMON_LIBRARIES);
        $html[] = '<br />D: ' . Translation :: get('Delete', null, Utilities :: COMMON_LIBRARIES);
        $html[] = '<br /><br />';
        $html[] = '<u><b>' . Translation :: get('Status') . '</u></b>';
        $html[] = '<br />1: ' . Translation :: get('Teacher');
        $html[] = '<br />5: ' . Translation :: get('Student');
        $html[] = '<br /><br />';
        $html[] = '<u><b>' . Translation :: get('Date', null, Utilities :: COMMON_LIBRARIES) . '</u></b>';
        $html[] = '<br />0 ' . Translation :: get('NotTakenIntoAccount');
        $html[] = '<br />YYYY-MM-DD HH:MM:SS';
        $html[] = '</blockquote>';

        echo implode($html, "\n");
    }

	function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add_help('user_importer');
    }
}
?>