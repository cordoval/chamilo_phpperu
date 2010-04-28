<?php
require_once 'Zend/Loader.php';
require_once dirname(__FILE__) . '/../../streaming_media_object.class.php';
require_once Path :: get_plugin_path() . 'getid3/getid3.php';

//YoutubeKey : AI39si4OLUsiI2mK0_k8HxqOtv0ctON-PzekhP_56JDkdph6wZ9tW2XqzDD7iVYY0GXKdMKlPSJyYZotNQGleVfRPDZih41Tug
class YoutubeStreamingMediaConnector
{
    private static $instance;
    private $manager;
    private $youtube;
    
    const RELEVANCE = 'relevance';
    const PUBLISHED = 'published';
    const VIEW_COUNT = 'viewCount';
    const RATING = 'rating';

    function YoutubeStreamingMediaConnector($manager)
    {
        $this->manager = $manager;
        
        $session_token = LocalSetting :: get('youtube_session_token', UserManager :: APPLICATION_NAME);
        
        Zend_Loader :: loadClass('Zend_Gdata_YouTube');
        Zend_Loader :: loadClass('Zend_Gdata_AuthSub');
        
        if (! $session_token)
        {
            if (! isset($_GET['token']))
            {
                $next_url = PATH :: get(WEB_PATH) . 'application/common/streaming_media_manager/index.php?type=youtube';
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
                    LocalSetting :: create_local_setting('youtube_session_token', $session_token, UserManager :: APPLICATION_NAME, $this->manager->get_user_id());
                }
            }
        }
        
        $config = array('adapter' => 'Zend_Http_Client_Adapter_Proxy', 'proxy_host' => '192.168.0.202', 'proxy_port' => 8080);
        $httpClient = Zend_Gdata_AuthSub :: getHttpClient($session_token);
        $httpClient->setConfig($config);
        
        $client = '';
        $application = PlatformSetting :: get('site_name');
        $key = PlatformSetting :: get('youtube_key', RepositoryManager :: APPLICATION_NAME);
        
        $this->youtube = new Zend_Gdata_YouTube($httpClient, $application, $client, $key);
        $this->youtube->setMajorProtocolVersion(2);
    }

    static function get_sort_properties()
    {
        return array(self :: RELEVANCE, self :: PUBLISHED, self :: VIEW_COUNT, self :: RATING);
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
        
        $video_entry->setVideoTitle($values[YoutubeStreamingMediaManagerForm :: VIDEO_TITLE]);
        $video_entry->setVideoCategory($values[YoutubeStreamingMediaManagerForm :: VIDEO_CATEGORY]);
        $video_entry->setVideoTags($values[YoutubeStreamingMediaManagerForm :: VIDEO_TAGS]);
        $video_entry->setVideoDescription($values[YoutubeStreamingMediaManagerForm :: VIDEO_DESCRIPTION]);
        
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
        $array = Utilities :: extract_xml_file(PATH :: get_plugin_path() . 'gdata/categories.cat');
        
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
        
        $video_entry->setVideoTitle($values[YoutubeStreamingMediaManagerForm :: VIDEO_TITLE]);
        $video_entry->setVideoCategory($values[YoutubeStreamingMediaManagerForm :: VIDEO_CATEGORY]);
        $video_entry->setVideoTags($values[YoutubeStreamingMediaManagerForm :: VIDEO_TAGS]);
        $video_entry->setVideoDescription($values[YoutubeStreamingMediaManagerForm :: VIDEO_DESCRIPTION]);
        
        $token_handler_url = 'http://gdata.youtube.com/action/GetUploadToken';
        $token_array = $this->youtube->getFormUploadToken($video_entry, $token_handler_url);
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
            self :: $instance = new YoutubeStreamingMediaConnector($manager);
        }
        return self :: $instance;
    }

    function get_video_feed($query)
    {
        $feed = Request :: get(YoutubeStreamingMediaManager :: PARAM_FEED_TYPE);
        switch ($feed)
        {
            case YoutubeStreamingMediaManager :: FEED_TYPE_GENERAL :
                return @ $this->youtube->getVideoFeed($query->getQueryUrl(2));
                break;
            case YoutubeStreamingMediaManager :: FEED_TYPE_MYVIDEOS :
                return $this->youtube->getuserUploads('default', $query->getQueryUrl(2));
                break;
            default :
                return @ $this->youtube->getVideoFeed($query->getQueryUrl(2));
        }
    }

    function get_youtube_videos($condition, $order_property, $offset, $count)
    {
        $query = $this->youtube->newVideoQuery();
        if (count($order_property) > 0)
        {
            $query->setOrderBy($order_property[0]);
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
        
        //		$query = $this->youtube->newVideoQuery();
        //		$query->setOrderBy('viewCount');
        //		$query->setMaxResults(1);
        //		$query->setStartIndex(($page * $limit) + 1);
        

        //		echo '<br /><br />' . "\n";
        //		echo '<b>User\'s own uploads</b><br /><br />' . "\n";
        

        //		$videoFeed = @ $this->youtube->getuserUploads('default', $query);
        

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
            
            $objects[] = new StreamingMediaObject($videoEntry->getVideoId(), $videoEntry->getVideoTitle(), $videoEntry->getVideoDescription(), $videoEntry->getFlashPlayerUrl(), $videoEntry->getVideoDuration(), $thumbnail);
        }
        return $objects;
    }

    function get_youtube_video($id)
    {
        $videoEntry = $this->youtube->getVideoEntry($id);
        $video_thumbnails = $videoEntry->getVideoThumbnails();
        if (count($video_thumbnails) > 0)
        {
            $thumbnail = $video_thumbnails[0]['url'];
        }
        else
        {
            $thumbnail = null;
        }
        return new StreamingMediaObject($videoEntry->getVideoId(), $videoEntry->getVideoTitle(), $videoEntry->getVideoDescription(), $videoEntry->getFlashPlayerUrl(), $videoEntry->getVideoDuration(), $thumbnail);
        //return $object;
    }

    function count_youtube_video($condition)
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
}

?>