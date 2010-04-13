<?php
/**
 * $Id: wiki_publication_deleter.class.php 210 2009-11-13 13:18:50Z kariboe $
 * @package application.lib.wiki.wiki_manager.component
 */
require_once dirname(__FILE__) . '/../wiki_manager.class.php';
require_once dirname(__FILE__) . '/../wiki_manager_component.class.php';

/**
 * Component to delete wiki_publications objects
 * @author Sven Vanpoucke & Stefan Billiet
 */
class WikiManagerWikiPublicationDeleterComponent extends WikiManagerComponent
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
            	if($this->get_parent()->retrieve_evaluation_ids_by_publication($id))
            	{
            		if(!$this->get_parent()->move_internal_to_external($wiki_publication))
            			$message = 'internal database error';
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
}
?>