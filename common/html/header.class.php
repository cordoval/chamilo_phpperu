<?php
/**
 * $Id: header.class.php 128 2009-11-09 13:13:20Z vanpouckesven $
 * @package common.html
 */
/**
 * Class to display the header of a HTML-page
 */
class Header
{
    /**
     * The http headers which will be send to the browser using php's header
     * (...) function.
     */
    private $http_headers;
    /**
     * The html headers which will be added in the <head> tag of the html
     * document.
     */
    private $html_headers;
    /**
     * The language code
     */
    private $language_code;

    /**
     * Constructor
     */
    function Header($language_code = 'en')
    {
        $this->http_headers = array();
        $this->html_headers = array();
        $this->language_code = $language_code;
    }

    /**
     * Adds some default headers to the output
     */
    public function add_default_headers()
    {
        $this->add_http_header('Content-Type: text/html; charset=UTF-8');
        $this->add_css_file_header(Theme :: get_theme_path() . 'plugin/jquery/jquery.css');
        $this->add_css_file_header(Theme :: get_common_css_path());
        $this->add_css_file_header(Theme :: get_css_path());
        //$this->add_css_file_header($this->get_path(WEB_CSS_PATH) .'print.css','print');
        $this->add_javascript_file_header($this->get_path(WEB_PLUGIN_PATH) . 'jquery/jquery.min.js');
        $this->add_javascript_file_header($this->get_path(WEB_PLUGIN_PATH) . 'jquery/jquery.dimensions.min.js');
        $this->add_javascript_file_header($this->get_path(WEB_PLUGIN_PATH) . 'jquery/jquery.tabula.js');
        $this->add_javascript_file_header($this->get_path(WEB_PLUGIN_PATH) . 'jquery/jquery.tablednd.js');
        $this->add_javascript_file_header($this->get_path(WEB_PLUGIN_PATH) . 'jquery/jquery.ui.min.js');
        $this->add_javascript_file_header($this->get_path(WEB_PLUGIN_PATH) . 'jquery/jquery.ui.tabs.paging.js');
        $this->add_javascript_file_header($this->get_path(WEB_PLUGIN_PATH) . 'jquery/jquery.simplemodal.js');
        $this->add_javascript_file_header($this->get_path(WEB_PLUGIN_PATH) . 'jquery/jquery.treeview.pack.js');
        $this->add_javascript_file_header($this->get_path(WEB_PLUGIN_PATH) . 'jquery/jquery.treeview.async.js');
        $this->add_javascript_file_header($this->get_path(WEB_PLUGIN_PATH) . 'jquery/jquery.timeout.interval.idle.js');
        $this->add_javascript_file_header($this->get_path(WEB_PLUGIN_PATH) . 'jquery/jquery.mousewheel.min.js');
        $this->add_javascript_file_header($this->get_path(WEB_PLUGIN_PATH) . 'jquery/jquery.scrollable.pack.js');
        $this->add_javascript_file_header($this->get_path(WEB_PLUGIN_PATH) . 'jquery/jquery.xml2json.pack.js');
        $this->add_javascript_file_header($this->get_path(WEB_PLUGIN_PATH) . 'jquery/jquery.json.js');
        $this->add_javascript_file_header($this->get_path(WEB_PLUGIN_PATH) . 'jquery/jquery.iphone.checkboxes.js');
        $this->add_javascript_file_header($this->get_path(WEB_PLUGIN_PATH) . 'jquery/jquery.textarearesizer.js');
        $this->add_javascript_file_header($this->get_path(WEB_PLUGIN_PATH) . 'jquery/jquery.jsuggest.js');
        $this->add_javascript_file_header($this->get_path(WEB_PLUGIN_PATH) . 'jquery/jquery.jeditable.mini.js');
        $this->add_javascript_file_header($this->get_path(WEB_LIB_PATH) . 'javascript/utilities.js');
        $this->add_javascript_file_header($this->get_path(WEB_LIB_PATH) . 'javascript/notifications.js');
        $this->add_javascript_file_header($this->get_path(WEB_LIB_PATH) . 'javascript/help.js');
        $this->add_javascript_file_header($this->get_path(WEB_LIB_PATH) . 'javascript/visit.js');
        $this->add_link_header($this->get_path(WEB_PATH) . 'index.php', 'top');
        //$this->add_link_header($this->get_path(WEB_PATH). 'index_user.php?go=account','account',htmlentities(Translation :: get('ModifyProfile')));
        $this->add_link_header('http://www.chamilo.org/documentation.php', 'help');
        $this->add_html_header('<link rel="shortcut icon" href="' . Theme :: get_theme_path() . 'favicon.ico" type="image/x-icon" />');
        $this->add_html_header('<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />');
    }

    /**
     * Adds a http header
     */
    public function add_http_header($http_header)
    {
        $this->http_headers[] = $http_header;
    }

    /**
     * Adds a html header
     */
    public function add_html_header($html_header)
    {
        $this->html_headers[] = $html_header;
    }

    /**
     * Sets the page title
     */
    public function set_page_title($title)
    {
        $this->add_html_header('<title>' . $title . '</title>');
    }

    /**
     * Adds a css file
     */
    public function add_css_file_header($file, $media = 'screen,projection')
    {
        $header[] = '<style type="text/css" media="' . $media . '">';
        $header[] = '/*<![CDATA[*/';
        $header[] = '@import "' . $file . '";';
        $header[] = '/*]]>*/';
        $header[] = '</style>';
        $this->add_html_header(implode(' ', $header));
    }

    /**
     * Adds a javascript file
     */
    public function add_javascript_file_header($file)
    {
        $header[] = '<script type="text/javascript" src="' . $file . '"></script>';
        $this->add_html_header(implode(' ', $header));
    }

    /**
     * Adds a link
     */
    public function add_link_header($url, $rel = null, $title = null)
    {
        $header = '<link rel="' . $rel . '" href="' . $url . '" title="' . $title . '"/>';
        $this->add_html_header($header);
    }

    /**
     * Displays the header. This function will send all http headers to the
     * browser and display the head-tag of the html document.
     */
    public function display()
    {
        echo $this->toHtml();
    }

    /**
     * Creates the HTML output for the header. This function will send all http
     * headers to the browser and return the head-tag of the html document
     */
    public function toHtml()
    {
        foreach ($this->http_headers as $index => $http_header)
        {
            header($http_header);
        }
        $output[] = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
        $output[] = '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="' . $this->language_code . '" lang="' . $this->language_code . '">';
        $output[] = ' <head>';
        foreach ($this->html_headers as $index => $html_header)
        {
            $output[] = '  ' . $html_header;
        }
        $output[] = ' </head>';
        return implode("\n", $output);
    }

    function get_path($path_type)
    {
        return Path :: get($path_type);
    }

    static function get_section()
    {
        global $this_section;
        return $this_section;
    }

    static function set_section($section)
    {
        global $this_section;
        $this_section = $section;
    }

    function get_setting($variable, $application)
    {
        return PlatformSetting :: get($variable, $application);
    }
}
?>