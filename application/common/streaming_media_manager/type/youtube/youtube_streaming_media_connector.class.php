<?php
require_once 'Zend/Loader.php';
require_once dirname (__FILE__) . '/../../streaming_media_object.class.php';

class YoutubeStreamingMediaConnector
{
    private static $instance;
    private $manager;
    private $youtube;

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
            	$session_token = Zend_Gdata_AuthSub::getAuthSubSessionToken($_GET['token']);
				if ($session_token)
				{
					LocalSetting::create_local_setting('youtube_session_token', $session_token, UserManager :: APPLICATION_NAME, $this->manager->get_user_id());
				}
            }
        }
       
        $config = array('adapter' => 'Zend_Http_Client_Adapter_Proxy', 'proxy_host' => '192.168.0.202', 'proxy_port' => 8080);
        $httpClient = Zend_Gdata_AuthSub::getHttpClient($session_token);
        $httpClient->setConfig($config);

        $client = '';
        $application = PlatformSetting :: get('site_name');
        $key = PlatformSetting :: get('youtube_key', RepositoryManager::APPLICATION_NAME);
        
        $this->youtube = new Zend_Gdata_YouTube($httpClient, $application, $client, $key);
		$this->youtube->setMajorProtocolVersion(2);
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

    function get_youtube_video()
    {
    	$query = $this->youtube->newVideoQuery();
		$query->setOrderBy('viewCount');
		$query->setVideoQuery('weezer island in the sun');
		$query->setMaxResults($limit);
		$query->setStartIndex(($page * $limit) + 1);
		
		$videoFeed = @ $this->youtube->getVideoFeed($query->getQueryUrl(2));
		
		
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
		    
			$objects[] = new StreamingMediaObject(
			    		$videoEntry->getVideoId(), 
			    		$videoEntry->getVideoTitle(), 
			    		$videoEntry->getVideoDescription(),
			    		$videoEntry->getFlashPlayerUrl(),
			    		$videoEntry->getVideoDuration(),
			    		$thumbnail);
		}
		return $objects;
    }
}

?>