<?php
/**
 * $Id: settings_migration_wizard_page.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.lib.migration_manager.component.inc.wizard
 */
require_once dirname(__FILE__) . '/migration_wizard_page.class.php';
require_once dirname(__FILE__) . '/../../../../migration_data_manager.class.php';
/**
 * Class for database settings page
 * Displays a form where the user can enter the installation settings
 * regarding the databases - login and password, names, prefixes
 * 
 */
class SettingsMigrationWizardPage extends MigrationWizardPage
{

    /**
     * @return string Title of the page
     */
    function get_title()
    {
        return Translation :: get('Setting_title');
    }

    /**
     * @return string Info of the page
     */
    function get_info()
    {
        return Translation :: get('Setting_info') . ':';
    }

    /**
     * Retrieves the next step info
     * @return string Info about the next step
     */
    function next_step_info()
    {
        return Translation :: get('Users_info');
    }

    /**
     * Builds the settings form
     */
    function buildForm()
    {
        $exports = $this->controller->exportValues();
        $this->_formBuilt = true;
        $this->addElement('text', 'old_directory', Translation :: get('old_directory'), array('size' => '40'));
        $this->addRule('old_directory', 'ThisFieldIsRequired', 'required');
        $this->addElement('hidden', 'settings', 'settings');
        
        $this->addElement('checkbox', 'migrate_users', '', Translation :: get('migrate_users'), 'onclick=\'users_clicked()\' style=\'margin-top: 20px;\'');
        $this->addElement('checkbox', 'migrate_personal_agendas', '', Translation :: get('migrate_personal_agendas'), 'onclick=\'personal_agendas_clicked()\' style=\'margin-left: 20px;\'');
        $this->addElement('checkbox', 'migrate_settings', '', Translation :: get('migrate_settings'), 'onclick=\'settings_clicked()\' style=\'margin-left: 20px;\'');
        $this->addElement('checkbox', 'migrate_classes', '', Translation :: get('migrate_classes'), 'onclick=\'classes_clicked()\' style=\'margin-left: 20px;\'');
        
        $this->addElement('checkbox', 'migrate_courses', '', Translation :: get('migrate_courses'), 'onclick=\'courses_clicked()\' style=\'margin-left: 20px;\'');
        $this->addElement('checkbox', 'migrate_metadata', '', Translation :: get('migrate_metadata'), 'onclick=\'metadata_clicked()\' style=\'margin-left: 40px;\'');
        $this->addElement('checkbox', 'migrate_groups', '', Translation :: get('migrate_groups'), 'onclick=\'groups_clicked()\' style=\'margin-left: 40px;\'');
        $this->addElement('checkbox', 'migrate_announcements', '', Translation :: get('migrate_announcements'), 'onclick=\'announcements_clicked()\' style=\'margin-left: 40px;\'');
        $this->addElement('checkbox', 'migrate_calendar_events', '', Translation :: get('migrate_calendar_events'), 'onclick=\'calendar_events_clicked()\' style=\'margin-left: 40px;\'');
        $this->addElement('checkbox', 'migrate_documents', '', Translation :: get('migrate_documents'), 'onclick=\'documents_clicked()\' style=\'margin-left: 40px;\'');
        $this->addElement('checkbox', 'migrate_links', '', Translation :: get('migrate_links'), 'onclick=\'links_clicked()\' style=\'margin-left: 40px;\'');
        $this->addElement('checkbox', 'migrate_dropboxes', '', Translation :: get('migrate_dropboxes'), 'onclick=\'dropboxes_clicked()\' style=\'margin-left: 40px;\'');
        $this->addElement('checkbox', 'migrate_forums', '', Translation :: get('migrate_forums'), 'onclick=\'forums_clicked()\' style=\'margin-left: 40px;\'');
        $this->addElement('checkbox', 'migrate_learning_paths', '', Translation :: get('migrate_learning_paths'), 'onclick=\'learning_paths_clicked()\' style=\'margin-left: 40px;\'');
        $this->addElement('checkbox', 'migrate_quizzes', '', Translation :: get('migrate_quizzes'), 'onclick=\'quizzes_clicked()\' style=\'margin-left: 40px;\'');
        $this->addElement('checkbox', 'migrate_student_publications', '', Translation :: get('migrate_student_publications'), 'onclick=\'student_publications_clicked()\' style=\'margin-left: 40px;\'');
        $this->addElement('checkbox', 'migrate_surveys', '', Translation :: get('migrate_surveys'), 'onclick=\'surveys_clicked()\' style=\'margin-left: 40px;\'');
        $this->addElement('checkbox', 'migrate_scorms', '', Translation :: get('migrate_scorms'), 'onclick=\'scorms_clicked()\' style=\'margin-left: 40px;\'');
        $this->addElement('checkbox', 'migrate_assignments', '', Translation :: get('migrate_assignments'), 'onclick=\'assignments_clicked()\' style=\'margin-left: 40px;\'');
        $this->addElement('checkbox', 'migrate_userinfos', '', Translation :: get('migrate_userinfos'), 'onclick=\'userinfos_clicked()\' style=\'margin-left: 40px;\'');
        $this->addElement('checkbox', 'migrate_trackers', '', Translation :: get('migrate_trackers'), 'onclick=\'trackers_clicked()\' style=\'margin-left: 40px;\'');
        
        $this->addElement('checkbox', 'migrate_deleted_files', '', Translation :: get('migrate_deleted_files'), 'onclick=\'deleted_files_clicked("' . Translation :: get('confirm_deleted_files') . '")\' style=\'margin-top: 20px;\'');
        
        $this->addElement('checkbox', 'move_files', '', Translation :: get('move_files'), 'onclick=\'move_files_clicked("' . Translation :: get('confirm_move_files') . '")\'');
        
        ValidateSettings :: set_old_system($exports['old_system']);
        $this->addRule(array('old_directory'), Translation :: get('CouldNotVerifySettings'), new ValidateSettings());
        
        $prevnext[] = $this->createElement('submit', $this->getButtonName('back'), '<< ' . Translation :: get('Previous'));
        $prevnext[] = $this->createElement('submit', $this->getButtonName('next'), Translation :: get('Next') . ' >>');
        $this->addGroup($prevnext, 'buttons', '', '&nbsp;', false);
        $this->setDefaultAction('next');
        $this->set_form_defaults();
    }

    /**
     * Set the form default values
     */
    function set_form_defaults()
    {
        $defaults = array();
        $defaults['old_directory'] = '/var/www/html/bron/';
        $defaults['migrate_users'] = '1';
        $defaults['migrate_personal_agendas'] = '1';
        $defaults['migrate_settings'] = '1';
        $defaults['migrate_classes'] = '1';
        $defaults['migrate_courses'] = '1';
        $defaults['migrate_groups'] = '1';
        $defaults['migrate_metadata'] = 1;
        $defaults['migrate_announcements'] = '1';
        $defaults['migrate_calendar_events'] = '1';
        $defaults['migrate_documents'] = '1';
        $defaults['migrate_links'] = '1';
        $defaults['migrate_dropboxes'] = '1';
        $defaults['migrate_forums'] = '1';
        $defaults['migrate_learning_paths'] = '1';
        $defaults['migrate_quizzes'] = '1';
        $defaults['migrate_student_publications'] = '1';
        $defaults['migrate_surveys'] = '1';
        $defaults['migrate_scorms'] = '1';
        $defaults['migrate_assignments'] = '1';
        $defaults['migrate_userinfos'] = '1';
        $defaults['migrate_trackers'] = '1';
        $defaults['migrate_deleted_files'] = '0';
        $defaults['move_files'] = '0';
        $this->setDefaults($defaults);
    }

}

/**
 * Validator class for old directory
 * @author Sven Vanpoucke
 */
class ValidateSettings extends HTML_QuickForm_Rule
{
    /**
     * Old system name for retrieval of datamanager
     */
    private static $old_system;

    /**
     * Validate the old directory
     * @param array $parameters of parameters with old directory at parameter[0]
     */
    public function validate($parameters)
    {
        $dmgr = OldMigrationDataManager :: getInstance(self :: $old_system, $parameters[0]);
        return $dmgr->validate_settings();
    }

    public static function set_old_system($old_system)
    {
        self :: $old_system = $old_system;
    }
}
?>