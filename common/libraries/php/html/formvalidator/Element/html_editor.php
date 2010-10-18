<?php
/**
 * @package common.html.formvalidator.Element
 */
// $Id: html_editor.php 168 2009-11-12 11:53:23Z vanpouckesven $
require_once ('HTML/QuickForm/textarea.php');
/**
 * A html editor field to use with QuickForm
 */
abstract class HTML_QuickForm_html_editor extends HTML_QuickForm_textarea
{
    var $options;

    /**
     * Class constructor
     * @param   string  HTML editor name/id
     * @param   string  HTML editor  label
     * @param   string  Attributes for the textarea
     */
    function HTML_QuickForm_html_editor($elementName = null, $elementLabel = null, $attributes = null, $options = array())
    {
        $this->options['width'] = (isset($options['width']) ? $options['width'] : '650');
        $this->options['height'] = (isset($options['height']) ? $options['height'] : '150');
        $this->options['show_toolbar'] = (isset($options['show_toolbar']) ? $options['show_toolbar'] : true);
        $this->options['show_tags'] = (isset($options['show_tags']) ? $options['show_tags'] : true);
        $this->options['full_page'] = (isset($options['full_page']) ? $options['full_page'] : false);
        $this->options['toolbar_set'] = (isset($options['toolbar_set']) ? $options['toolbar_set'] : 'Basic');
        
        $this->_persistantFreeze = true;
        $this->set_type();
        
        HTML_QuickForm_element :: HTML_QuickForm_element($elementName, $elementLabel, $attributes);
    }

    function get_options()
    {
        return $this->options;
    }

    function get_option($name)
    {
        if (isset($this->options[$name]))
        {
            return $this->options[$name];
        }
        else
        {
            return null;
        }
    }

    abstract function set_type();

    /**
     * Check if the browser supports th editor
     *
     * @access public
     * @return boolean
     */
    abstract function browserSupported();

    /**
     * Return the HTML editor in HTML
     * @return string
     */
    function toHtml()
    {
        $value = $this->getValue();
        if ($this->fullPage)
        {
            if (strlen(trim($value)) == 0)
            {
                $value = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
							<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
							<head>
							<title></title>
							<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
							<style type="text/css" media="screen, projection">/*<![CDATA[*/body{font-family: arial, verdana, helvetica, sans-serif;font-size: 12px;}/*]]>*/</style>
							</head>
							<body>
							</body>
							</html>';
                $this->setValue($value);
            }
        }
        if ($this->_flagFrozen)
        {
            return $this->getFrozenHtml();
        }
        else
        {
            return $this->build_editor();
        }
    }

    function render_textarea()
    {
        $html = parent :: toHTML();
        
        $width = $this->options['width'];
        if (strpos($width, '%') === false)
        {
            $width .= 'px';
        }
        
        $height = $this->options['height'];
        if (strpos($height, '%') === false)
        {
            $height .= 'px';
        }
        
        $string = '<textarea style="width: ' . $width . '; height: ' . $height . ';"';
        $html = str_replace('<textarea', $string, $html);
        return $html;
    }

    /**
     * Returns the frozen content in HTML
     *@return string
     */
    function getFrozenHtml()
    {
        $val = $this->getValue();
        return $val . '<input type="hidden" name="' . htmlspecialchars($this->getName()) . '"' . ' value="' . htmlspecialchars($val) . '"/>';
    }

    /**
     * Build this element using the editor
     */
    abstract function build_editor();
}
?>