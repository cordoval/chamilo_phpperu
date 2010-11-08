<?php
namespace application\wiki;

use common\libraries\Translation;
use common\libraries\Utilities;

/**
 * $Id: wiki_publication_publisher.class.php 210 2009-11-13 13:18:50Z kariboe $
 * @package application.lib.wiki.publisher
 */

class WikiPublicationPublisher
{
    private $parent;

    function WikiPublicationPublisher($parent)
    {
        $this->parent = $parent;
    }

    function publish($object)
    {
        $author = $this->parent->get_user_id();
        $date = mktime(date());
        
        $pb = new WikiPublication();
        $pb->set_content_object_id($object);
        $pb->set_parent_id($author);
        $pb->set_category(0);
        $pb->set_published($date);
        $pb->set_modified($date);
        
        if (! $pb->create())
        {
            $error = true;
        }
        
        if ($error)
        {
            $message = Translation :: get('ObjectNotPublished', array('OBJECT' => Translation :: get('Wiki')) , Utilities :: COMMON_LIBRARIES);
        }
        else
        {
            $message = Translation :: get('ObjectPublished', array('OBJECT' => Translation :: get('Wiki')) , Utilities :: COMMON_LIBRARIES);
        }
        
        $this->parent->redirect($message, false);
    }
}
?>