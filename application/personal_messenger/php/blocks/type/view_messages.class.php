<?php
/**
 * $Id: view_messages.class.php 203 2009-11-13 12:46:38Z chellee $
 * @package application.personal_messenger.block
 */
require_once WebApplication :: get_application_class_path('personal_messenger') . 'blocks/personal_messenger_block.class.php';
require_once Path :: get_library_path() . 'utilities.class.php';
/**
 * This class represents a calendar publisher component which can be used
 * to browse through the possible learning objects to publish.
 */
class PersonalMessengerViewMessages extends PersonalMessengerBlock
{

    /*
	 * Inherited
	 */
    function as_html()
    {
        $nrOfMessages = 5;
        $nrOfNewMessages = 0;
        $html = array();

        $personal_messenger = $this->get_parent();

        $html[] = $this->display_header();

        /*
		$publications = $personal_messenger->retrieve_personal_message_publications($this->get_condition(), array (), array (), $nrOfMessages);
		if($publications->size() > 0)
		{
			while($publication = $publications->next_result())
			{
				if($publication->get_status() == 1)
				{
					$this->show_publication($publication,$personal_messenger,$html,true);
				}else
				{
					$this->show_publication($publication,$personal_messenger,$html,false);
				}
			}
		}
		*/

        $publications_new = $personal_messenger->retrieve_personal_message_publications($this->get_condition("new"), array(), array(), $nrOfMessages);
        $publications_recent = $personal_messenger->retrieve_personal_message_publications($this->get_condition(), array(), array(), $nrOfMessages);

        $arr_pub_new = array();
        while ($publication = $publications_new->next_result())
        {
            $arr_pub_new[] = $publication;
        }

        $arr_pub = array();
        while ($publication = $publications_recent->next_result())
        {
            $arr_pub[] = $publication;
        }

        if ($publications_new->size() > 0)
        {
            foreach ($arr_pub_new as $publication)
            {
                $this->show_publication($publication, $personal_messenger, $html, true);
                $nrOfNewMessages = $nrOfNewMessages + 1;
            }
        }
        if ($publications_recent->size() > 0 && $nrOfNewMessages < $nrOfMessages)
        {
            foreach ($arr_pub as $publication)
            {
                if (! $this->is_new($publication, $arr_pub_new))
                {
                    $this->show_publication($publication, $personal_messenger, $html, false);
                }
            }
        }
        else
        {
            $html[] = Translation :: get('NoMessages');
        }
        //*/


        $html[] = $this->display_footer();

        return implode("\n", $html);
    }

    function show_publication(&$publication, &$personal_messenger, &$html, $new)
    {
        $separator = ' - ';
        $html[] = $new ? '<img width="15" height="15" src="' . Theme :: get_common_image_path() . 'content_object/personal_message_new.png" />' : '<img width="15" height="15" src="' . Theme :: get_common_image_path() . 'content_object/personal_message_na.png" />';

        $html[] = '<a href="' . $personal_messenger->get_publication_viewing_link($publication) . '">';
        //$html[] = $this->str_trim($publication->get_publication_sender()->get_fullname()) . $separator;
        //$html[] = $this->str_trim($publication->get_publication_object()->get_title());
        $html[] = Utilities :: truncate_string($publication->get_publication_sender()->get_fullname(), 32) . $separator;
        $html[] = Utilities :: truncate_string($publication->get_publication_object()->get_title(), 32);
        $html[] = '</a><br /><i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
        //$html[] = $this->str_trim(strip_tags($publication->get_publication_object()->get_description()),50);
        $html[] = Utilities :: truncate_string($publication->get_publication_object()->get_description(), 50);
        $html[] = '</i>';
        $html[] = '<br />	';
    }

    function is_new(&$publication, &$arr_pub_new)
    {
        $result = false;
        foreach ($arr_pub_new as $publication_new)
        {
            if ($publication->get_publication_object()->get_id() == $publication_new->get_publication_object()->get_id())
            {
                $result = true;
            }
        }
        return $result;
    }

    function get_condition($condition)
    {

        $conditions = array();
        if ($condition == "new")
            $conditions[] = new EqualityCondition(PersonalMessagePublication :: PROPERTY_STATUS, '1');
        $conditions[] = new EqualityCondition(PersonalMessagePublication :: PROPERTY_RECIPIENT, $this->get_user_id());
        $conditions[] = new EqualityCondition(PersonalMessagePublication :: PROPERTY_USER, $this->get_user_id());
        return new AndCondition($conditions);
    }

// Trim a string to specified length and append an end character (default = ...)
// 	function str_trim($str, $lim = 32, $chr = '&#8230;')
// 	{
//     // If length of string is less than $lim, return string
//     	if (strlen($str = html_entity_decode($str)) <= $lim) return $str;
//
//     // Else, cut string down to size
//     	return htmlentities(substr($str, 0, $lim - 3)).$chr;
// 	}
}
?>