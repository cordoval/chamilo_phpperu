<?php
/**
 * @package common.html.formvalidator.Element
 */
// $Id: timepicker.php 128 2009-11-09 13:13:20Z vanpouckesven $
require_once ('HTML/QuickForm/date.php');
/**
 * Form element to select a date and hour (with popup datepicker)
 */
class HTML_QuickForm_timepicker extends HTML_QuickForm_date
{
    private $include_minutes_picker;

    /**
     * Constructor
     */
    function HTML_QuickForm_timepicker($elementName = null, $elementLabel = null, $attributes = null, $include_minutes_picker = true)
    {
        global $language_interface;
        if (! isset($attributes['form_name']))
        {
            return;
        }
        
        $js_form_name = $attributes['form_name'];
        //unset($attributes['form_name']);
        HTML_QuickForm_element :: HTML_QuickForm_element($elementName, $elementLabel, $attributes);
        $this->_persistantFreeze = true;
        $this->_appendName = true;
        $this->_type = 'timepicker';
        $special_chars = array('D', 'l', 'd', 'M', 'F', 'm', 'y', 'H', 'a', 'A', 's', 'i', 'h', 'g', ' ');
        $hour_minute_devider = Translation :: get("HourMinuteDivider");
        foreach ($special_chars as $index => $char)
        {
            $popup_link = str_replace($char, "\\" . $char, $popup_link);
            $hour_minute_devider = str_replace($char, "\\" . $char, $hour_minute_devider);
        }
        $adm = AdminDataManager :: get_instance();
        $editor_lang = $adm->retrieve_language_from_english_name($language_interface)->get_isocode();
        if (empty($editor_lang))
        {
            //if there was no valid iso-code, use the english one
            $editor_lang = 'en';
        }
        // If translation not available in PEAR::HTML_QuickForm_date, add the Chamilo-translation
        if (! array_key_exists($editor_lang, $this->_locale))
        {
            $this->_locale[$editor_lang]['months_long'] = array(Translation :: get("JanuaryLong"), Translation :: get("FebruaryLong"), Translation :: get("MarchLong"), Translation :: get("AprilLong"), Translation :: get("MayLong"), Translation :: get("JuneLong"), Translation :: get("JulyLong"), Translation :: get("AugustLong"), Translation :: get("SeptemberLong"), Translation :: get("OctoberLong"), Translation :: get("NovemberLong"), Translation :: get("DecemberLong"));
        }
        
        if ($include_minutes_picker)
        {
            $this->_options['format'] = 'H ' . $hour_minute_devider . ' i';
        }
        else
        {
            $this->_options['format'] = 'H' . $hour_minute_devider;
        }
        
        $this->_options['language'] = $editor_lang;
        $this->setValue(date('H'));
    }

    /**
     * HTML code to display this datepicker
     */
    function toHtml()
    {
        $js = $this->getElementJS();
        return $js . parent :: toHtml();
    }

    /**
     * Get the necessary javascript for this datepicker
     */
    function getElementJS()
    {
        /*$js = '';
        //if(!defined('DATEPICKER_JAVASCRIPT_INCLUDED'))
        {
            //define('DATEPICKER_JAVASCRIPT_INCLUDED',1);
            $js = "\n";
            $js .= '<script src="';
            $js .= Path :: get(WEB_LIB_PATH) . 'html/formvalidator/Element/';
            $js .= 'tbl_change.js" type="text/javascript"></script>';
            $js .= "\n";
        }*/
        return ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_LIB_PATH) . 'html/formvalidator/Element/tbl_change.js');
    }

    /**
     * Export the date value in MySQL format
     * @return string YYYY-MM-DD HH:II:SS
     */
    function exportValue()
    {
        $values = parent :: getValue();
        $h = $values['H'][0];
        $i = $values['i'][0];
        $h = $h < 10 ? '0' . $h : $h;
        $i = $i < 10 ? '0' . $i : $i;
        
        if ($this->include_minutes_picker)
        {
            $datetime = $h . ':' . $i . ':00';
        }
        else
        {
            $datetime = $h;
        }
        
        $datetime = $h;
        
        $result[$this->getName()] = $datetime;
        return $result;
    }
}
?>