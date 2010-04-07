<?php
/**
 * $Id: reporting_formatter_form.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.forms
 */

class ReportingFormatterForm extends FormValidator
{
    const FORMATTER_TYPE = 'formatter';

    private $manager;
    private $renderer;

    /**
     * Creates a new search form
     * @param RepositoryManager $manager The repository manager in which this
     * search form will be displayed
     * @param string $url The location to which the search request should be
     * posted.
     */
    function ReportingFormatterForm($manager, $url)
    {
        parent :: __construct('repository_filter_form', 'post', $url);

        $this->renderer = $this->defaultRenderer();
        $this->manager = $manager;

        $this->build_form();

        $this->accept($this->renderer);
    }

    /**
     * Build the simple search form.
     */
    private function build_form()
    {
        $this->renderer->setFormTemplate('<form {attributes}><div class="filter_form">{content}</div><div class="clear">&nbsp;</div></form>');
        $this->renderer->setElementTemplate('<div class="row"><div class="formw">{element}</div></div>');

        $this->addElement('select', self :: FORMATTER_TYPE, null, $this->manager->get_displaymodes(), array('class' => 'postback'));
        $this->addElement('style_submit_button', 'submit', Translation :: get('Formatter'), array('class' => 'normal filter'));
        
   	    $display = Request::post(self::FORMATTER_TYPE);
    	$display_get = Request::get(self::FORMATTER_TYPE);
        if (isset($display))
        {
        	$session_filter = $display;
        }
        elseif (isset($display_get))
        {
        	$session_filter = $display_get;
        }
        
        $this->setDefaults(array(self :: FORMATTER_TYPE => $session_filter));

        $this->addElement('html', ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PATH) . 'common/javascript/postback.js'));
    }

    /**
     * Display the form
     */
    function display()
    {
        $html = array();
        $html[] = '<div style="text-align: right;">';
        $html[] = $this->renderer->toHTML();
        $html[] = '</div>';
        return implode('', $html);
    }
}
?>