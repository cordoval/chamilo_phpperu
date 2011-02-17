<?php
namespace application\search_portal;

use common\libraries\Translation;
use common\libraries\Utilities;
use common\libraries\FormValidator;

/**
 * $Id: basic.class.php 222 2009-11-13 14:39:28Z chellee $
 * @package application.search_portal.block
 */

/**
 * Search Portal block
 */
class SearchPortalBasic extends SearchPortalBlock
{
    function display_content()
    {
        $html = array();

        //$html[] = 'Search Portal test block ...';
        $form = new FormValidator('search_simple', 'get', 'run.php', $this->get_link_target(), null, false);
        $form->addElement('text', 'query', '', 'style="width:80%;" id="inputString" onkeyup="lookup(this.value);"');
        $form->addElement('submit', 'submit', Translation :: get('Search', null , Utilities :: COMMON_LIBRARIES));
        $form->addElement('hidden', 'application', 'search_portal');
        $html[] = '<div style="text-align: center; margin: 0 0 2em 0;">';
        $renderer = clone $form->defaultRenderer();
        $renderer->setElementTemplate('{label} {element} ');
        $form->accept($renderer);
        $form->setDefaults(array('application' => 'search_portal'));
        $html[] = $renderer->toHTML();
        $html[] = '</div>';

        return implode("\n", $html);
    }
}
?>