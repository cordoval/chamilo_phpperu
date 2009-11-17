<?php
/**
 * $Id: link_publication_list_renderer.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.link.component.link_viewer
 */
require_once dirname(__FILE__) . '/../../../../browser/list_renderer/list_content_object_publication_list_renderer.class.php';
class LinkPublicationListRenderer extends ListContentObjectPublicationListRenderer
{

    function LinkPublicationListRenderer($browser)
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