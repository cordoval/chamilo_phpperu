<?php
require_once 'Zend/Loader.php';
require_once dirname(__FILE__) . '/google_docs_external_repository_object.class.php';

//YoutubeKey : AI39si4OLUsiI2mK0_k8HxqOtv0ctON-PzekhP_56JDkdph6wZ9tW2XqzDD7iVYY0GXKdMKlPSJyYZotNQGleVfRPDZih41Tug
class GoogleDocsExternalRepositoryConnector
{
    private static $instance;
    private $manager;
    private $google_docs;
    
    const RELEVANCE = 'relevance';
    const PUBLISHED = 'published';
    const VIEW_COUNT = 'viewCount';
    const RATING = 'rating';

    function GoogleDocsExternalRepositoryConnector($manager)
    {
        $this->manager = $manager;
        
        $session_token = LocalSetting :: get('google_docs_session_token', UserManager :: APPLICATION_NAME);
        
        Zend_Loader :: loadClass('Zend_Gdata_Docs');
        Zend_Loader :: loadClass('Zend_Gdata_AuthSub');
        
        if (! $session_token)
        {
            if (! isset($_GET['token']))
            {
                if ($manager->is_stand_alone())
                {
                    $next_url = PATH :: get(WEB_PATH) . 'common/launcher/index.php?type=google_docs&application=external_repository';
                }
                else
                {
                    $next_url = PATH :: get(WEB_PATH) . 'core.php?go=external_repository&application=repository&category=0&type=google_docs';
                }
                
                $scope = 'http://docs.google.com/feeds/';
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
                    LocalSetting :: create_local_setting('google_docs_session_token', $session_token, UserManager :: APPLICATION_NAME, $this->manager->get_user_id());
                }
            }
        }
        
        //$config = array('adapter' => 'Zend_Http_Client_Adapter_Proxy', 'proxy_host' => '192.168.0.202', 'proxy_port' => 8080);
        $httpClient = Zend_Gdata_AuthSub :: getHttpClient($session_token);
        //$httpClient->setConfig($config);
        

        $application = PlatformSetting :: get('site_name');
        
        $this->google_docs = new Zend_Gdata_Docs($httpClient, $application);
    }

    static function get_sort_properties()
    {
        return array(self :: RELEVANCE, self :: PUBLISHED, self :: VIEW_COUNT, self :: RATING);
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
        $filesource = $this->google_docs->newMediaFileSource($_FILES['upload']['tmp_name']);
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
            $new_entry = $this->google_docs->insertEntry($video_entry, $upload_url, 'Zend_Gdata_YouTube_VideoEntry');
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
        $token_array = $this->google_docs->getFormUploadToken($video_entry, $token_handler_url);
        $token_value = $token_array['token'];
        $post_url = $token_array['url'];
        
        return $token_array;
    }

    //
    // USER'S OWN UPLOADS
    //		$query = $yt->newVideoQuery();
    //		$query->setOrderBy('viewCount');
    //		$query->setMaxResults(1);
    //		$query->setStartIndex(($page * $limit) + 1);
    //
    //		echo '<br /><br />' . "\n";
    //		echo '<b>User\'s own uploads</b><br /><br />' . "\n";
    //
    //		$videoFeed = @ $yt->getuserUploads('default', $query);
    //
    //		foreach ($videoFeed as $videoEntry)
    //		{
    //		    echo $videoEntry->getVideoTitle() . '<br />' . "\n";
    //		}
    

    static function get_instance($manager)
    {
        if (! isset(self :: $instance))
        {
            self :: $instance = new GoogleDocsExternalRepositoryConnector($manager);
        }
        return self :: $instance;
    }

    function get_video_feed($query)
    {
        $feed = Request :: get(YoutubeExternalRepositoryManager :: PARAM_FEED_TYPE);
        switch ($feed)
        {
            case YoutubeExternalRepositoryManager :: FEED_TYPE_GENERAL :
                return @ $this->google_docs->getVideoFeed($query->getQueryUrl(2));
                break;
            case YoutubeExternalRepositoryManager :: FEED_TYPE_MYVIDEOS :
                return $this->google_docs->getUserUploads('default', $query->getQueryUrl(2));
                break;
            case YoutubeExternalRepositoryManager :: FEED_STANDARD_TYPE :
                $identifier = Request :: get(YoutubeExternalRepositoryManager :: PARAM_FEED_IDENTIFIER);
                if (! $identifier || ! in_array($identifier, $this->get_standard_feeds()))
                {
                    $identifier = 'most_viewed';
                }
                $new_query = $this->google_docs->newVideoQuery('http://gdata.youtube.com/feeds/api/standardfeeds/' . $identifier);
                $new_query->setOrderBy($query->getOrderBy());
                $new_query->setVideoQuery($query->getVideoQuery());
                $new_query->setStartIndex($query->getStartIndex());
                $new_query->setMaxResults($query->getMaxResults());
                return @ $this->google_docs->getVideoFeed($new_query->getQueryUrl(2));
            default :
                return @ $this->google_docs->getVideoFeed($query->getQueryUrl(2));
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

    function count_external_repository_objects($condition)
    {
        $documents_feed = $this->google_docs->getDocumentListFeed();
        return $documents_feed->getTotalResults()->getText();
    }

    function retrieve_external_repository_objects($condition, $order_property, $offset, $count)
    {
        $documents_feed = $this->google_docs->getDocumentListFeed();
        
        $objects = array();
        foreach ($documents_feed->entries as $document)
        {
            $object = new GoogleDocsExternalRepositoryObject();
            $object->set_id(1);
            $object->set_title($document->getTitle()->getText());
            $object->set_description();
            
            $objects[] = $object;
        }
        
        return $objects;
    
    }

    function get_youtube_videos($condition, $order_property, $offset, $count)
    {
        $query = $this->google_docs->newVideoQuery();
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
            
            $object = new YoutubeExternalRepositoryObject();
            $object->set_id($videoEntry->getVideoId());
            $object->set_title($videoEntry->getVideoTitle());
            $object->set_description($videoEntry->getVideoDescription());
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
            
            $objects[] = $object;
        }
        
        return $objects;
    }

    function get_youtube_video_entry($id)
    {
        $parameter = Request :: get(YoutubeExternalRepositoryManager :: PARAM_FEED_TYPE);
        if ($parameter == YoutubeExternalRepositoryManager :: FEED_TYPE_MYVIDEOS)
        {
            return $this->google_docs->getFullVideoEntry($id);
        }
        else
        {
            return $this->google_docs->getVideoEntry($id);
        }
    }

    function get_youtube_video($id)
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
        
        $object = new YoutubeExternalRepositoryObject();
        $object->set_id($videoEntry->getVideoId());
        $object->set_title($videoEntry->getVideoTitle());
        $object->set_description($videoEntry->getVideoDescription());
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
        return $object;
    }

    function update_youtube_video($values)
    {
        $video_entry = $this->google_docs->getFullVideoEntry($values[ExternalRepositoryObject :: PROPERTY_ID]);
        $video_entry->setVideoTitle($values[YoutubeExternalRepositoryObject :: PROPERTY_TITLE]);
        $video_entry->setVideoCategory($values[YoutubeExternalRepositoryObject :: PROPERTY_CATEGORY]);
        $video_entry->setVideoTags($values[YoutubeExternalRepositoryObject :: PROPERTY_TAGS]);
        $video_entry->setVideoDescription($values[YoutubeExternalRepositoryObject :: PROPERTY_DESCRIPTION]);
        
        $edit_link = $video_entry->getEditLink()->getHref();
        $this->google_docs->updateEntry($video_entry, $edit_link);
        return true;
    }

    function count_youtube_video($condition)
    {
        $query = $this->google_docs->newVideoQuery();
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

    function delete_youtube_video($id)
    {
        $video_entry = $this->google_docs->getFullVideoEntry($id);
        
        return $this->google_docs->delete($video_entry);
    }

    function is_usable($id)
    {
        return true;
    }

    function export_youtube_video($object)
    {
        $video_entry = new Zend_Gdata_YouTube_VideoEntry();
        $file_source = $this->google_docs->newMediaFileSource($object->get_full_path());
        $file_source->setContentType($object->get_mime_type());
        $file_source->setSlug($object->get_filename());
        $video_entry->setMediaSource($file_source);
        $video_entry->setVideoTitle($object->get_title());
        $video_entry->setVideoDescription(strip_tags($object->get_description()));
        $video_entry->setVideoCategory('Education');
        
        $upload_url = 'http://uploads.gdata.youtube.com/feeds/api/users/default/uploads';
        try
        {
            $new_entry = $this->google_docs->insertEntry($video_entry, $upload_url, 'Zend_Gdata_YouTube_VideoEntry');
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
}

?>