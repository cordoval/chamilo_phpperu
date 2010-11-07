<?php
namespace application\wiki;

use common\libraries;

use repository\ComplexDisplay;
use repository\content_object\wiki\Wiki;
use repository\content_object\wiki\WikiComplexDisplaySupport;
use common\libraries\Path;
use common\libraries\Display;
use common\libraries\Breadcrumb;
use common\libraries\BreadcrumbTrail;
use common\libraries\Request;
use common\libraries\Translation;

/**
 * $Id: wiki_viewer.class.php 210 2009-11-13 13:18:50Z kariboe $
 * @package application.lib.wiki.wiki_manager.component
 */

require_once Path :: get_repository_content_object_path() . 'wiki/php/display/wiki_complex_display_support.class.php';

class WikiManagerWikiViewerComponent extends WikiManager implements WikiComplexDisplaySupport
{
    private $publication;

    function run()
    {
        if (! $this->is_allowed(VIEW_RIGHT))
        {
            Display :: not_allowed();
            return;
        }

        $trail = BreadcrumbTrail :: get_instance();
        $trail->add(new Breadcrumb($this->get_url(array(WikiManager :: PARAM_ACTION => WikiManager :: ACTION_BROWSE_WIKI_PUBLICATIONS)), Translation :: get('Wiki')));

        $this->set_parameter(WikiManager :: PARAM_WIKI_PUBLICATION, Request :: get(WikiManager :: PARAM_WIKI_PUBLICATION));

        $this->publication = WikiDataManager :: get_instance()->retrieve_wiki_publication(Request :: get(WikiManager :: PARAM_WIKI_PUBLICATION));

        ComplexDisplay :: launch(Wiki :: get_type_name(), $this);
    }

    function get_root_content_object()
    {
        return $this->publication->get_content_object();
    }

    function get_wiki_publication()
    {
        return $this->publication;
    }

	function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add_help('wiki_publication_viewer');
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(WikiManager :: PARAM_ACTION => WikiManager :: ACTION_BROWSE_WIKI_PUBLICATIONS)), Translation :: get('WikiManagerWikiPublicationsBrowserComponent')));
    }

    function get_additional_parameters()
    {
    	return array(self :: PARAM_WIKI_PUBLICATION);
    }

    function get_wiki_page_statistics_reporting_template_name()
    {
        return 'wiki_page_most_reporting_template';
    }

    function get_wiki_statistics_reporting_template_name()
    {
        return 'wiki_most_reporting_template';
    }

    /**
     * Unused for the wiki
     *
     * @param $right
     */
    function is_allowed($right)
    {
        return true;
    }

}
?>