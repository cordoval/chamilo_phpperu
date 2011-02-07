<?php

namespace repository\content_object\rss_feed;

use common\libraries\Translation;
use common\libraries\Path;
use common\libraries\Theme;
use repository\ContentObjectDisplay;
use LastRss;
use repository\ContentObject;
use Zend_Gdata_Calendar;
use Zend_Gdata_AuthSub;
use Zend_Loader;
use common\libraries\PlatformSetting;

/**
 * $Id: rss_feed_display.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.rss_feed
 */
require_once Path :: get_plugin_path() . 'lastrss/lastrss.class.php';

class RssFeedDisplay extends ContentObjectDisplay {

    private $current_tag;
    private $current_value;
    private $xml;
    private $item;
    private $items;

    function get_full_html() {
        $object = $this->get_content_object();
        $html = array();

        $html[] = '<div class="content_object" style="background-image: url(' . Theme :: get_image_path(ContentObject :: get_content_object_type_namespace($object->get_type())) . 'logo/' . $object->get_icon_name() . ($object->is_latest_version() ? '' : '_na') . '.png);">';
        $html[] = '<div class="title">' . Translation :: get('Description') . '</div>';
        $html[] = $this->get_description();
        $html[] = '<div class="link_url" style="margin-top: 1em;"><a href="' . htmlentities($object->get_url()) . '">' . htmlentities($object->get_url()) . '</a></div>';
        $html[] = '</div>';

        $feed = $this->parse_file($object->get_url());

        foreach ($feed['items'] as $item) {
            $html[] = '<div class="content_object" style="background-image: url(' . Theme :: get_common_image_path() . 'content_object/rss_feed_item.png);">';
            $html[] = '<div class="title">' . $item['title'] . '</div>';
            $html[] = html_entity_decode($item['description']);
            $html[] = '<div class="link_url" style="margin-top: 1em;"><a href="' . htmlentities($item['link']) . '">' . htmlentities($item['link']) . '</a></div>';
            $html[] = '</div>';
        }

        return implode("\n", $html);
    }

    //Inherited
    function get_list_html() {
        $object = $this->get_content_object();
        $html = array();

        $html[] = '<h4 class="table"><a href="' . htmlentities($object->get_url()) . '">' . $object->get_title() . '</a></h4>';
        $html[] = $object->get_description();
        return implode("\n", $html);
    }

    function get_short_html() {
        $object = $this->get_content_object();
        return '<span class="content_object"><a href="' . htmlentities($object->get_url()) . '">' . htmlentities($object->get_title()) . '</a></span>';
    }

    function parse_file($url) {

//        Zend_Loader :: loadClass('Zend_Gdata_AuthSub');
//        Zend_Loader :: loadClass('Zend_Gdata_App');
//
//        $my_calendar = 'https://mail.google.com/mail/feed/atom';
//
//        $sess_name = 'dddsdfsdfds';
//
//        if (!isset($_SESSION[$sess_name])) {
//            if (isset($_GET['token'])) {
//                // You can convert the single-use token to a session token.
//                $session_token =
//                        Zend_Gdata_AuthSub::getAuthSubSessionToken($_GET['token']);
//                // Store the session token in our session.
//                $_SESSION[$sess_name] = $session_token;
//            } else {
//                // Display link to generate single-use token
//                $googleUri = Zend_Gdata_AuthSub::getAuthSubTokenUri(
//                                'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'],
//                                $my_calendar, 0, 1);
//                echo "Click <a href='$googleUri'>here</a> " .
//                "to authorize this application.";
//                exit();
//            }
//        }
//
//       // Authorization: AuthSub token="token"
//        // create curl resource
//        $ch = curl_init();
//
//        // set url
//        curl_setopt($ch, CURLOPT_URL, 'https://mail.google.com/mail/feed/atom');
//         $headers = array(
//            'Authorization: AuthSub token="' . ($_SESSION[$sess_name]) .'"'
//        );
//
//        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
//        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
//
//
//        //return the transfer as a string
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//
//        // $output contains the output string
//        $output = curl_exec($ch);
//        debug(\curl_error($ch));
//        debug(\curl_getinfo($ch));
//        debug($output);
//
//        // close curl resource to free up system resources
//        curl_close($ch);
//        exit;


        $rss = new LastRss($url);
        // TODO: Make items limit configurable.
        $rss->set_items_limit(5);
        $rss->set_cache_dir(Path :: get(SYS_TEMP_PATH));

        if ($rs = $rss->get_feed_content()) {
            return $rs;
        } else {
            return false;
            //die ('Error: RSS file not found...');
        }
    }

}

?>