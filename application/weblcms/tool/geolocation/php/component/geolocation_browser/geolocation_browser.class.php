<?php
namespace application\weblcms\tool\geolocation;

use application\weblcms\Tool;
use common\libraries\Request;
use application\weblcms\ContentObjectPublicationBrowser;
use application\weblcms\ContentObjectPublicationCategoryTree;
use application\weblcms\ContentObjectPublicationDetailsRenderer;


/**
 * $Id: geolocation_browser.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.geolocation.component.geolocation_browser
 */

class GeolocationBrowser extends ContentObjectPublicationBrowser
{

    function __construct($parent, $types)
    {
        parent :: __construct($parent, 'geolocation');

        $this->set_publication_id(Request :: get(Tool :: PARAM_PUBLICATION_ID));
        $renderer = new GeolocationDetailsRenderer($this);

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