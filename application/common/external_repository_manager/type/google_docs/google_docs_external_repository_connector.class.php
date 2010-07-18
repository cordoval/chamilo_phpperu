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
        
        $session_token = $this->manager->get_user_setting('session_token');
        
        Zend_Loader :: loadClass('Zend_Gdata_Docs');
        Zend_Loader :: loadClass('Zend_Gdata_AuthSub');
        
        if (! $session_token)
        {
            if (! isset($_GET['token']))
            {
                if ($manager->is_stand_alone())
                {
                    $next_url = PATH :: get(WEB_PATH) . 'common/launcher/index.php?application=external_repository&external_repository=' . $this->manager->get_parameter(ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY);
                }
                else
                {
                    $next_url = PATH :: get(WEB_PATH) . 'core.php?go=external_repository&application=repository&external_repository=' . $this->manager->get_parameter(ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY);
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
                    $setting = RepositoryDataManager :: get_instance()->retrieve_external_repository_setting_from_variable_name('session_token', $this->manager->get_parameter(ExternalRepositoryManager :: PARAM_EXTERNAL_REPOSITORY));
                    $user_setting = new ExternalRepositoryUserSetting();
                    $user_setting->set_setting_id($setting->get_id());
                    $user_setting->set_user_id($this->manager->get_user_id());
                    $user_setting->set_value($session_token);
                    $user_setting->create();
                }
            }
        }
        
        $httpClient = Zend_Gdata_AuthSub :: getHttpClient($session_token);
        $application = PlatformSetting :: get('site_name');
        $this->google_docs = new Zend_Gdata_Docs($httpClient, $application);
    }

    static function get_sort_properties()
    {
        return array(self :: RELEVANCE, self :: PUBLISHED, self :: VIEW_COUNT, self :: RATING);
    }

    static function translate_search_query($query)
    {
        return $query;
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
            $resource_id = $document->getResourceId();
            $resource_id = explode(':', $resource_id->getText());
            
            if ($document->getLastViewed())
            {
                $last_viewed = $document->getLastViewed()->getText();
                $last_viewed_timestamp = strtotime($last_viewed);
            }
            else
            {
                $last_viewed_timestamp = 0;
            }
            
            $published = $document->getPublished()->getText();
            $published_timestamp = strtotime($published);
            
            $modified = $document->getUpdated()->getText();
            $modified_timestamp = strtotime($modified);
            
            $author = $document->getAuthor();
            $author = $author[0];
            
            $modifier = $document->getLastModifiedBy();
            
            $object = new GoogleDocsExternalRepositoryObject();
            $object->set_id($resource_id[1]);
            $object->set_title($document->getTitle()->getText());
            $object->set_created($published_timestamp);
            $object->set_type($resource_id[0]);
            $object->set_viewed($last_viewed_timestamp);
            $object->set_modified($modified_timestamp);
            $object->set_owner_id($author->getEmail()->getText());
            $object->set_modifier_id($modifier->getEmail()->getText());
            $object->set_content($document->getContent()->getSrc());
            
            $objects[] = $object;
        }
        
        return new ArrayResultSet($objects);
    }

    function retrieve_folders()
    {
        $folders_feed = $this->google_docs->getFolderListFeed();
        
        $objects = array();
        foreach ($folders_feed->entries as $folder)
        {
//            dump($folder->getTitle()->getText());
//            if ($folder->getLink('http://schemas.google.com/docs/2007#parent') instanceof Zend_Gdata_App_Extension_Link)
//            {
//                dump($folder->getLink('http://schemas.google.com/docs/2007#parent')->getTitle());
//            }
//            else
//            {
//                dump('Root element !');
//            }
//            echo '<hr />';
            
            $resource_id = $folder->getResourceId();
            $resource_id = explode(':', $resource_id->getText());
            
            $author = $folder->getAuthor();
            $author = $author[0];
            
            $object = new GoogleDocsExternalRepositoryObject();
            $object->set_id($resource_id[1]);
            $object->set_title($folder->getTitle()->getText());
            $object->set_type($resource_id[0]);
            $object->set_owner_id($author->getEmail()->getText());
            $object->set_content($folder->getContent()->getSrc());
            
            $objects[] = $object;
        }
        
        return new ArrayResultSet($objects);
    }
}
?>