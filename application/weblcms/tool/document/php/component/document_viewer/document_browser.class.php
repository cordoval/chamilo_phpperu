<?php
namespace application\weblcms\tool\document;

use common\libraries\Request;

/**
 * $Id: document_browser.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.document.component.document_viewer
 */
require_once dirname(__FILE__) . '/../../../../content_object_publication_browser.class.php';
require_once dirname(__FILE__) . '/../../../../browser/content_object_publication_category_tree.class.php';
require_once dirname(__FILE__) . '/../../../../browser/list_renderer/content_object_publication_details_renderer.class.php';

class DocumentBrowser extends ContentObjectPublicationBrowser
{

    function DocumentBrowser($parent, $types)
    {
        parent :: __construct($parent, Document :: get_type_name());

        $this->set_publication_id(Request :: get(Tool :: PARAM_PUBLICATION_ID));
        $renderer = new ContentObjectPublicationDetailsRenderer($this);
        $this->set_publication_list_renderer($renderer);

    }

    function get_publications($from, $count, $column, $direction)
    {

    }

    function get_publication_count()
    {

    }
}
?>