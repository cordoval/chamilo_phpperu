<?php
namespace application\weblcms\tool\link;

use application\weblcms\ContentObjectPublicationDetailsRenderer;
/**
 * $Id: link_details_renderer.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.link.component.link_viewer
 */
class LinkDetailsRenderer extends ContentObjectPublicationDetailsRenderer
{

    function LinkDetailsRenderer($browser)
    {
        parent :: __construct($browser);
    }

    function render_title($publication)
    {
        $url = $publication->get_content_object()->get_url();
        return '<a target="about:blank" href="' . htmlentities($url) . '">' . parent :: render_title($publication) . '</a>';
    }
}
?>