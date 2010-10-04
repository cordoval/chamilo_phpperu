<?php
/**
 * $Id: settings_archive_wizard_page.class.php 213 2009-11-13 13:38:50Z vanpouckesven $
 * @package tracking.lib.tracking_manager.component.wizards.archive
 */
require_once dirname(__FILE__) . '/archive_wizard_page.class.php';
/**
 * Page in the archive wizard in which some config settings are asked to the
 * user.
 */
class SettingsArchiveWizardPage extends ArchiveWizardPage
{

    /**
     * Returns the title of this page
     * @return string the title
     */
    function get_title()
    {
        return Translation :: get('ArchiveSettingsTitle');
    }

    /**
     * Returns the info of this page
     * @return string the info
     */
    function get_info()
    {
        return Translation :: get('ArchiveSettingsInfo');
    }

    /**
     * Builds the form that must be visible on this page
     */
    function buildForm()
    {
        $this->_formBuilt = true;
        
        $exports = $this->exportValues();
        
        $this->addElement('html', '<div style="margin-top: 10px;"></div>');
        
        $this->addElement('datepicker', 'start_date', Translation :: get('Start_date') . ' (00:00:00)', array('form_name' => $this->getAttribute('name')), false);
        $this->addRule(array('start_date'), Translation :: get('Start_date_must_be_larger_then_last_archive_date'), new ValidateSettings($exports['start_date']));
        $this->addElement('datepicker', 'end_date', Translation :: get('End_date') . ' (23:59:59)', array('form_name' => $this->getAttribute('name')), false);
        $this->addRule(array('end_date'), Translation :: get('End_date_must_be_larger_then_start_date'), new ValidateSettings());
        
        $numbers = array();
        for($i = 1; $i < 1001; $i ++)
        {
            $numbers[$i] = $i;
        }
        
        $this->addElement('select', 'period', Translation :: get('Period') . ' (' . Translation :: get('Days') . ')', $numbers);
        
        $prevnext[] = $this->createElement('style_submit_button', $this->getButtonName('back'), '<< ' . Translation :: get('Previous'), array('class' => 'previous'));
        $prevnext[] = $this->createElement('style_submit_button', $this->getButtonName('next'), Translation :: get('Next') . ' >>', array('class' => 'next'));
        $this->addGroup($prevnext, 'buttons', '', '&nbsp;', false);
        $this->setDefaultAction('next');
        $this->set_form_defaults();
    }

    /**
     * Sets the defaults for this form
     */
    function set_form_defaults()
    {
        $defaults = array();
        
        $adm = AdminDataManager :: get_instance();
        $setting = $adm->retrieve_setting_from_variable_name('last_time_archived', 'tracking');
        
        $defaults['start_date'] = $setting ? $setting->get_value() : date('d-F-Y');
        $defaults['end_date'] = date('d-F-Y');
        $this->setDefaults($defaults);
    }
}

/**
 * Validator class for dates
 * @author Sven Vanpoucke
 */
class ValidateSettings extends HTML_QuickForm_Rule
{

    /**
     * Constructor
     * @param int $start_date The start date used for validation of end_date
     */
    function ValidateSettings($start_date)
    {
        $this->start_date = $start_date;
    }

    /**
     * Validate the old directory
     * @param array $parameters
     */
    public function validate($parameters)
    {
        $sd = $parameters[0];
        $date = $sd['Y'] . '-' . $sd['F'] . '-' . $sd['d'];
        $date = Utilities :: time_from_datepicker_without_timepicker($date);
        
        if ($start_date == 0)
        {
            $adm = AdminDataManager :: get_instance();
            $setting = $adm->retrieve_setting_from_variable_name('last_time_archived', 'tracking');
            
            $setting_date = Utilities :: time_from_datepicker_without_timepicker($setting->get_value());
            return $date >= $setting_date;
        }
        else
        {
            $startdate = Utilities :: time_from_datepicker_without_timepicker($startdate);
            return $start_date < $date;
        }
    }
}
?>