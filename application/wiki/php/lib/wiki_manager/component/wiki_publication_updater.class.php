<?php
namespace application\wiki;

use common\libraries\Request;
use common\libraries\Translation;
use common\libraries\Utilities;
use common\libraries\Breadcrumb;

use repository\ContentObjectForm;

/**
 * $Id: wiki_publication_updater.class.php 210 2009-11-13 13:18:50Z kariboe $
 * @package application.lib.wiki.wiki_manager.component
 */
/**
 * Component to edit an existing wiki_publication object
 * @author Sven Vanpoucke & Stefan Billiet
 */
class WikiManagerWikiPublicationUpdaterComponent extends WikiManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $wiki_publication = $this->retrieve_wiki_publication(Request :: get(WikiManager :: PARAM_WIKI_PUBLICATION));
        
        $form = ContentObjectForm :: factory(ContentObjectForm :: TYPE_EDIT, $wiki_publication->get_content_object(), 'edit', 'post', $this->get_url(array(WikiManager :: PARAM_WIKI_PUBLICATION => $wiki_publication->get_id())));
        
        if ($form->validate() || Request :: get('validated'))
        {
            if (! Request :: get('validated'))
                $success = $form->update_content_object();
            
            $pub_form = new WikiPublicationForm(WikiPublicationForm :: TYPE_EDIT, $wiki_publication, $this->get_url(array(WikiManager :: PARAM_WIKI_PUBLICATION => $wiki_publication->get_id(), 'validated' => 1)), $this->get_user());
            if ($pub_form->validate())
            {
                $success = $pub_form->update_wiki_publication();
                $message = $success ? Translation :: get('ObjectUpdated', array('OBJECT' => Translation :: get('WikiPublication')), Utilities :: COMMON_LIBRARIES) : Translation :: get('ObjectNotUpdated', array('OBJECT' => Translation :: get('WikiPublication')), Utilities :: COMMON_LIBRARIES);
                $this->redirect($message , ! $success, array(WikiManager :: PARAM_ACTION => WikiManager :: ACTION_BROWSE_WIKI_PUBLICATIONS));
            }
            else
            {
            	$this->display_header();
                $pub_form->display();
            }
        
        }
        else
        {
            $this->display_header();
            $form->display();
        }
        $this->display_footer();
    }
    
	function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add_help('wiki_publication_updater');
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(WikiManager :: PARAM_ACTION => WikiManager :: ACTION_BROWSE_WIKI_PUBLICATIONS)), Translation :: get('WikiManagerWikiPublicationsBrowserComponent')));
    }
    
    function get_additional_parameters()
    {
    	return array(self :: PARAM_WIKI_PUBLICATION);
    }
}
?>