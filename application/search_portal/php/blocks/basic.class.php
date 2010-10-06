<?php
/**
 * $Id: basic.class.php 222 2009-11-13 14:39:28Z chellee $
 * @package application.search_portal.block
 */
require_once dirname(__FILE__) . '/../search_portal_block.class.php';

/**
 * This class represents a calendar publisher component which can be used
 * to browse through the possible learning objects to publish.
 */
class SearchPortalBasic extends SearchPortalBlock
{

    function run()
    {
        return $this->as_html();
    }

    /*
	 * Inherited
	 */
    function as_html()
    {
        $html = array();
        
        $html[] = $this->display_header();
        //$html[] = 'Search Portal test block ...';
        $form = new FormValidator('search_simple', 'get', 'run.php', '', null, false);
        $form->addElement('text', 'query', '', 'style="width:80%;" id="inputString" onkeyup="lookup(this.value);"');
        $form->addElement('submit', 'submit', Translation :: get('Search'));
        $form->addElement('hidden', 'application', 'search_portal');
        $html[] = '<div style="text-align: center; margin: 0 0 2em 0;">';
        $renderer = clone $form->defaultRenderer();
        $renderer->setElementTemplate('{label} {element} ');
        $form->accept($renderer);
        $form->setDefaults(array('application' => 'search_portal'));
        $html[] = $renderer->toHTML();
        $html[] = '</div>';
        $html[] = $this->display_footer();
        
        return implode("\n", $html);
    }
}
?>