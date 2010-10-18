<?php
/**
 * $Id: wiki_publication_deleter.class.php 210 2009-11-13 13:18:50Z kariboe $
 * @package application.lib.wiki.wiki_manager.component
 */

/**
 * Component to delete wiki_publications objects
 * @author Sven Vanpoucke & Stefan Billiet
 */
class WikiManagerWikiPublicationDeleterComponent extends WikiManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $ids = $_GET[WikiManager :: PARAM_WIKI_PUBLICATION];
        $failures = 0;
        
        if (! empty($ids))
        {
            if (! is_array($ids))
            {
                $ids = array($ids);
            }
            
//            if (retrieve_evaluation_ids_by_publication($this->get_parent()->APPLICATION_NAME))
//            {
//            	$button = '<a ' . $id . $class . 'href="' . htmlentities($elmt['href']) . '" title="' . $label . '"' . ($elmt['confirm'] ? ' onclick="javascript: return confirm(\'' . addslashes(htmlentities(Translation :: get('ConfirmYourChoice'), ENT_QUOTES, 'UTF-8')) . '\');"' : '') . '>' . $button . '</a>';
//            }
            
            foreach ($ids as $id)
            {
            	$wiki_publication = $this->retrieve_wiki_publication($id);
            	if(WebApplication :: is_active('gradebook'))
       			{
       				require_once dirname(__FILE__) . '/../../../gradebook/gradebook_utilities.class.php';
			    	if(!GradebookUtilities :: move_internal_item_to_external_item(WikiManager :: APPLICATION_NAME, $wiki_publication->get_id()))
			    		$message = 'failed to move internal evaluation to external evaluation';
       			}
                
                if (! $wiki_publication->delete())
                {
                    $failures ++;
                }
            }
            
            if ($failures)
            {
                if (count($ids) == 1)
                {
                    $message = 'SelectedWikiPublicationDeleted';
                }
                else
                {
                    $message = 'SelectedWikiPublicationDeleted';
                }
            }
            else
            {
                if (count($ids) == 1)
                {
                    $message = 'SelectedWikiPublicationsDeleted';
                }
                else
                {
                    $message = 'SelectedWikiPublicationsDeleted';
                }
            }
            
            $this->redirect(Translation :: get($message), ($failures ? true : false), array(WikiManager :: PARAM_ACTION => WikiManager :: ACTION_BROWSE_WIKI_PUBLICATIONS));
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoWikiPublicationsSelected')));
        }
    }
    
	function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add_help('wiki_publications_browser');
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(WikiManager :: PARAM_ACTION => WikiManager :: ACTION_BROWSE_WIKI_PUBLICATIONS)), Translation :: get('WikiManagerWikiPublicationsBrowserComponent')));
    }
    
    function get_additional_parameters()
    {
    	return array(self :: PARAM_WIKI_PUBLICATION);
    }
}
?>