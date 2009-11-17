<?php
/**
 * $Id: wiki_publication_publisher.class.php 210 2009-11-13 13:18:50Z kariboe $
 * @package application.lib.wiki.publisher
 */

require_once dirname(__FILE__) . '/../forms/wiki_publication_form.class.php';

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
        $pb->set_content_object($object);
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
            $message = Translation :: get('ObjectNotPublished');
        }
        else
        {
            $message = Translation :: get('ObjectPublished');
        }
        
        $this->parent->redirect($message, false);
    }
}
?>