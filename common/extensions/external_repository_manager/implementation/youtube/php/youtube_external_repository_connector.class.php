<?php
namespace common\extensions\external_repository_manager\implementation\youtube;

use common\libraries\Path;
use common\libraries\Request;
use common\libraries\Utilities;
use common\libraries\Redirect;
use common\libraries\Session;
use common\libraries\PlatformSetting;
use common\libraries\ArrayResultSet;
use common\libraries\Translation;

use common\extensions\external_repository_manager\ExternalRepositoryConnector;
use common\extensions\external_repository_manager\ExternalRepositoryObject;

use repository\ExternalRepositoryUserSetting;
use repository\RepositoryDataManager;
use repository\ExternalRepositorySetting;

use \Zend_Loader;
use \Zend_Gdata_AuthSub;
use \Zend_Gdata_YouTube;
use \Zend_Gdata_YouTube_VideoEntry;


require_once dirname(__FILE__) . '/youtube_external_repository_object.class.php';
require_once Path :: get_plugin_path() . 'getid3/getid3.php';
require_once dirname(__FILE__) . '/youtube_external_repository_manager.class.php';

//YoutubeKey : AI39si4OLUsiI2mK0_k8HxqOtv0ctON-PzekhP_56JDkdph6wZ9tW2XqzDD7iVYY0GXKdMKlPSJyYZotNQGleVfRPDZih41Tug
class YoutubeExternalRepositoryConnector extends ExternalRepositoryConnector
{
    private $youtube;

    const RELEVANCE = 'relevance';
    const PUBLISHED = 'published';
    const VIEW_COUNT = 'viewCount';
    const RATING = 'rating';

    function __construct($external_repository_instance)
    {
        parent :: __construct($external_repository_instance);

        $session_token = ExternalRepositoryUserSetting :: get('session_token', $this->get_external_repository_instance_id());

        Zend_Loader :: loadClass('Zend_Gdata_YouTube');
        Zend_Loader :: loadClass('Zend_Gdata_AuthSub');

        if (! $session_token)
        {
            if (! isset($_GET['token']))
            {
                $next_url = Redirect :: current_url();
                $scope = 'http://gdata.youtube.com';
                $secure = false;
                $session = true;
                $redirect_url = Zend_Gdata_AuthSub :: getAuthSubTokenUri($next_url, $scope, $secure, $session);

                header('Location: ' . $redirect_url);
            }
            else
            {
                $session_token = Zend_Gdata_AuthSub :: getAuthSubSessionToken($_GET['token']);
                if ($session_token)
                {
                    $setting = RepositoryDataManager :: get_instance()->retrieve_external_repository_setting_from_variable_name('session_token', $this->get_external_repository_instance_id());
                    $user_setting = new ExternalRepositoryUserSetting();
                    $user_setting->set_setting_id($setting->get_id());
                    $user_setting->set_user_id(Session :: get_user_id());
                    $user_setting->set_value($session_token);
                    $user_setting->create();
                }
            }
        }

        $httpClient = Zend_Gdata_AuthSub :: getHttpClient($session_token);

        $client = '';
        $application = PlatformSetting :: get('site_name');
        $key = ExternalRepositorySetting :: get('developer_key', $this->get_external_repository_instance_id());

        $this->youtube = new Zend_Gdata_YouTube($httpClient, $application, $client, $key);
        $this->youtube->setMajorProtocolVersion(2);
    }

    static function get_sort_properties()
    {
        return array(self :: RELEVANCE,
                self :: PUBLISHED,
                self :: VIEW_COUNT,
                self :: RATING);
    }

    function is_editable($id)
    {
        $videoEntry = $this->get_youtube_video_entry($id);
        if ($videoEntry->getEditLink() !== null)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    static function translate_search_query($query)
    {
        return $query;
    }

    function create_youtube_video()
    {
        $video_entry = new Zend_Gdata_YouTube_VideoEntry();
        $filesource = $this->youtube->newMediaFileSource($_FILES['upload']['tmp_name']);
        $video_getid3 = new getID3();
        $video_info = $video_getid3->analyze($_FILES['upload']['tmp_name']);
        $filesource->setContentType($_FILES['upload']['type']);
        $filesource->setSlug($_FILES['upload']['name']);
        $video_entry->setMediaSource($filesource);

        $video_entry->setVideoTitle($values[YoutubeExternalRepositoryManagerForm :: VIDEO_TITLE]);
        $video_entry->setVideoCategory($values[YoutubeExternalRepositoryManagerForm :: VIDEO_CATEGORY]);
        $video_entry->setVideoTags($values[YoutubeExternalRepositoryManagerForm :: VIDEO_TAGS]);
        $video_entry->setVideoDescription($values[YoutubeExternalRepositoryManagerForm :: VIDEO_DESCRIPTION]);

        $upload_url = 'http://uploads.gdata.youtube.com/feeds/api/users/default/uploads';

        try
        {
            $new_entry = $this->youtube->insertEntry($video_entry, $upload_url, 'Zend_Gdata_YouTube_VideoEntry');
        }
        catch (Zend_Gdata_App_HttpException $http_exception)
        {
            echo ($http_exception->getRawResponseBody());
        }
        catch (Zend_Gdata_App_Exception $e)
        {
            echo ($e->getMessage());
        }
    }

    function retrieve_categories()
    {
        $properties = array();

        //$options[] = array(XML_UNSERIALIZER_OPTION_FORCE_ENUM => array('atom:category'));
        //$array = Utilities :: extract_xml_file(Zend_Gdata_YouTube_VideoEntry::YOUTUBE_CATEGORY_SCHEMA, $options);
        $array = Utilities :: extract_xml_file(PATH :: get_plugin_path() . 'google/categories.cat');

        $categories = array();
        foreach ($array['atom:category'] as $category)
        {
            $categories[$category['term']] = Translation :: get($category['term']);
        }

        return $categories;
    }

    function get_upload_token($values)
    {
        $video_entry = new Zend_Gdata_YouTube_VideoEntry();

        $video_entry->setVideoTitle($values[YoutubeExternalRepositoryManagerForm :: VIDEO_TITLE]);
        $video_entry->setVideoCategory($values[YoutubeExternalRepositoryManagerForm :: VIDEO_CATEGORY]);
        $video_entry->setVideoTags($values[YoutubeExternalRepositoryManagerForm :: VIDEO_TAGS]);
        $video_entry->setVideoDescription($values[YoutubeExternalRepositoryManagerForm :: VIDEO_DESCRIPTION]);

        $token_handler_url = 'http://gdata.youtube.com/action/GetUploadToken';
        $token_array = $this->youtube->getFormUploadToken($video_entry, $token_handler_url);
        $token_value = $token_array['token'];
        $post_url = $token_array['url'];

        return $token_array;
    }

    function get_video_feed($query)
    {
        $feed = Request :: get(YoutubeExternalRepositoryManager :: PARAM_FEED_TYPE);
        switch ($feed)
        {
            case YoutubeExternalRepositoryManager :: FEED_TYPE_GENERAL :
                return @ $this->youtube->getVideoFeed($query->getQueryUrl(2));
                break;
            case YoutubeExternalRepositoryManager :: FEED_TYPE_MYVIDEOS :
                return $this->youtube->getUserUploads('default', $query->getQueryUrl(2));
                break;
            case YoutubeExternalRepositoryManager :: FEED_STANDARD_TYPE :
                $identifier = Request :: get(YoutubeExternalRepositoryManager :: PARAM_FEED_IDENTIFIER);
                if (! $identifier || ! in_array($identifier, $this->get_standard_feeds()))
                {
                    $identifier = 'most_viewed';
                }
                $new_query = $this->youtube->newVideoQuery('http://gdata.youtube.com/feeds/api/standardfeeds/' . $identifier);
                $new_query->setOrderBy($query->getOrderBy());
                $new_query->setVideoQuery($query->getVideoQuery());
                $new_query->setStartIndex($query->getStartIndex());
                $new_query->setMaxResults($query->getMaxResults());
                return @ $this->youtube->getVideoFeed($new_query->getQueryUrl(2));
            default :
                return $this->youtube->getUserUploads('default', $query->getQueryUrl(2));
        }
    }

    function get_standard_feeds()
    {
        $standard_feeds = array();
        $standard_feeds[] = 'most_viewed';
        $standard_feeds[] = 'top_rated';
        $standard_feeds[] = 'recently_featured';
        $standard_feeds[] = 'watch_on_mobile';
        $standard_feeds[] = 'most_discussed';
        $standard_feeds[] = 'top_favorite';
        $standard_feeds[] = 'most_responded';
        $standard_feeds[] = 'most_recent';
        return $standard_feeds;
    }

    function retrieve_external_repository_objects($condition, $order_property, $offset, $count)
    {
        $query = $this->youtube->newVideoQuery();
        if (count($order_property) > 0)
        {
            $query->setOrderBy($order_property[0]->get_property());
        }
        $query->setVideoQuery($condition);

        $query->setStartIndex($offset + 1);

        if (($count + $offset) >= 900)
        {
            $temp = ($offset + $count) - 900;
            $query->setMaxResults($count - $temp);
        }
        else
        {
            $query->setMaxResults($count);
        }

        $videoFeed = $this->get_video_feed($query);

        $objects = array();
        foreach ($videoFeed as $videoEntry)
        {
            $video_thumbnails = $videoEntry->getVideoThumbnails();
            if (count($video_thumbnails) > 0)
            {
                $thumbnail = $video_thumbnails[0]['url'];
            }
            else
            {
                $thumbnail = null;
            }

            $published = $videoEntry->getPublished()->getText();
            $published_timestamp = strtotime($published);

            $modified = $videoEntry->getUpdated()->getText();
            $modified_timestamp = strtotime($modified);

            $uploader = $videoEntry->getAuthor();
            $uploader = $uploader[0];

            $object = new YoutubeExternalRepositoryObject();
            $object->set_id($videoEntry->getVideoId());
            $object->set_external_repository_id($this->get_external_repository_instance_id());
            $object->set_title($videoEntry->getVideoTitle());
            $object->set_description(nl2br($videoEntry->getVideoDescription()));
            $object->set_created($published_timestamp);
            $object->set_modified($modified_timestamp);
            $object->set_owner_id($uploader->getName()->getText());
            $object->set_url($videoEntry->getFlashPlayerUrl());
            $object->set_duration($videoEntry->getVideoDuration());
            $object->set_thumbnail($thumbnail);

            $object->set_category($videoEntry->getVideoCategory());
            $object->set_tags($videoEntry->getVideoTags());

            $control = $videoEntry->getControl();
            if (isset($control))
            {
                $object->set_status($control->getState()->getName());
            }
            else
            {
                $object->set_status(YoutubeExternalRepositoryObject :: STATUS_AVAILABLE);
            }

            $object->set_rights($this->determine_rights($videoEntry));

            $objects[] = $object;
        }

        return new ArrayResultSet($objects);
    }

    function get_youtube_video_entry($id)
    {
        $parameter = Request :: get(YoutubeExternalRepositoryManager :: PARAM_FEED_TYPE);
        if ($parameter == YoutubeExternalRepositoryManager :: FEED_TYPE_MYVIDEOS)
        {
            return $this->youtube->getFullVideoEntry($id);
        }
        else
        {
            return $this->youtube->getVideoEntry($id);
        }
    }

    function retrieve_external_repository_object($id)
    {
        $videoEntry = $this->get_youtube_video_entry($id);

        $video_thumbnails = $videoEntry->getVideoThumbnails();

        if (count($video_thumbnails) > 0)
        {
            $thumbnail = $video_thumbnails[0]['url'];
        }
        else
        {
            $thumbnail = null;
        }

        $author = $videoEntry->getAuthor();
        $author = $author[0];

        $published = $videoEntry->getPublished()->getText();
        $published_timestamp = strtotime($published);

        $modified = $videoEntry->getUpdated()->getText();
        $modified_timestamp = strtotime($modified);

        $object = new YoutubeExternalRepositoryObject();
        $object->set_id($videoEntry->getVideoId());
        $object->set_external_repository_id($this->get_external_repository_instance_id());
        $object->set_title($videoEntry->getVideoTitle());
        $object->set_description(nl2br($videoEntry->getVideoDescription()));
        $object->set_owner_id($author->getName()->getText());
        $object->set_created($published_timestamp);
        $object->set_modified($modified_timestamp);
        $object->set_url($videoEntry->getFlashPlayerUrl());
        $object->set_duration($videoEntry->getVideoDuration());
        $object->set_thumbnail($thumbnail);

        $object->set_category($videoEntry->getVideoCategory());
        $object->set_tags($videoEntry->getVideoTags());

        $control = $videoEntry->getControl();
        if (isset($control))
        {
            $object->set_status($control->getState()->getName());
        }
        else
        {
            $object->set_status(YoutubeExternalRepositoryObject :: STATUS_AVAILABLE);
        }

        $object->set_rights($this->determine_rights($videoEntry));

        return $object;
    }

    function update_youtube_video($values)
    {
        $video_entry = $this->youtube->getFullVideoEntry($values[ExternalRepositoryObject :: PROPERTY_ID]);
        $video_entry->setVideoTitle($values[YoutubeExternalRepositoryObject :: PROPERTY_TITLE]);
        $video_entry->setVideoCategory($values[YoutubeExternalRepositoryObject :: PROPERTY_CATEGORY]);
        $video_entry->setVideoTags($values[YoutubeExternalRepositoryObject :: PROPERTY_TAGS]);
        $video_entry->setVideoDescription($values[YoutubeExternalRepositoryObject :: PROPERTY_DESCRIPTION]);

        $edit_link = $video_entry->getEditLink()->getHref();
        $this->youtube->updateEntry($video_entry, $edit_link);
        return true;
    }

    function count_external_repository_objects($condition)
    {
        $query = $this->youtube->newVideoQuery();
        $query->setVideoQuery($condition);

        $videoFeed = $this->get_video_feed($query);
        if ($videoFeed->getTotalResults()->getText() >= 900)
        {
            return 900;
        }
        else
        {
            return $videoFeed->getTotalResults()->getText();
        }
    }

    function delete_external_repository_object($id)
    {
        $video_entry = $this->youtube->getFullVideoEntry($id);

        return $this->youtube->delete($video_entry);
    }

    function export_external_repository_object($object)
    {
        $video_entry = new Zend_Gdata_YouTube_VideoEntry();
        $file_source = $this->youtube->newMediaFileSource($object->get_full_path());
        $file_source->setContentType($object->get_mime_type());
        $file_source->setSlug($object->get_filename());
        $video_entry->setMediaSource($file_source);
        $video_entry->setVideoTitle($object->get_title());
        $video_entry->setVideoDescription(strip_tags($object->get_description()));
        $video_entry->setVideoCategory('Education');

        $upload_url = 'http://uploads.gdata.youtube.com/feeds/api/users/default/uploads';
        try
        {
            $new_entry = $this->youtube->insertEntry($video_entry, $upload_url, 'Zend_Gdata_YouTube_VideoEntry');
        }
        catch (Zend_Gdata_App_HttpException $httpException)
        {
            echo ($httpException->getRawResponseBody());
        }
        catch (Zend_Gdata_App_Exception $e)
        {
            echo $e->getMessage();
        }
        return true;
    }

    function determine_rights($video_entry)
    {
        $rights = array();
        $rights[ExternalRepositoryObject :: RIGHT_USE] = true;
        $rights[ExternalRepositoryObject :: RIGHT_EDIT] = ($video_entry->getEditLink() !== null ? true : false);
        $rights[ExternalRepositoryObject :: RIGHT_DELETE] = ($video_entry->getEditLink() !== null ? true : false);
        $rights[ExternalRepositoryObject :: RIGHT_DOWNLOAD] = false;
        return $rights;
    }
}
?>