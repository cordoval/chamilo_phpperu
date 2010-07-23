<?php
require_once 'Zend/Loader.php';
require_once dirname(__FILE__) . '/google_docs_external_repository_object.class.php';

class GoogleDocsExternalRepositoryConnector extends ExternalRepositoryConnector
{
    /**
     * @var Zend_Gdata_Docs
     */
    private $google_docs;
    
    const RELEVANCE = 'relevance';
    const PUBLISHED = 'published';
    const VIEW_COUNT = 'viewCount';
    const RATING = 'rating';

    /**
     * @param ExternalRepository $external_repository_instance
     */
    function GoogleDocsExternalRepositoryConnector($external_repository_instance)
    {
        parent :: __construct($external_repository_instance);
        
        $session_token = ExternalRepositoryUserSetting :: get('session_token', $this->get_external_repository_instance_id());
        
        Zend_Loader :: loadClass('Zend_Gdata_Docs');
        Zend_Loader :: loadClass('Zend_Gdata_Docs_Query');
        Zend_Loader :: loadClass('Zend_Gdata_AuthSub');
        
        if (! $session_token)
        {
            if (! isset($_GET['token']))
            {
                $next_url = Redirect :: current_url();
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
        $application = PlatformSetting :: get('site_name');
        $this->google_docs = new Zend_Gdata_Docs($httpClient, $application);
    }

    /**
     * @param string $id
     */
    function retrieve_external_repository_object($id)
    {
    
    }

    /**
     * @param string $id
     */
    function delete_external_repository_object($id)
    {
    
    }

    /**
     * @param ContentObject $content_object
     */
    function export_external_repository_object($content_object)
    {
    
    }

    /**
     * @return array 
     */
    static function get_sort_properties()
    {
        return array(self :: RELEVANCE, self :: PUBLISHED, self :: VIEW_COUNT, self :: RATING);
    }

    /**
     * @param mixed $query
     * @return mixed
     */
    static function translate_search_query($query)
    {
        return $query;
    }

    /**
     * @param array $values
     * @return array
     */
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

    /* (non-PHPdoc)
     * @see application/common/external_repository_manager/ExternalRepositoryConnector#count_external_repository_objects()
     */
    function count_external_repository_objects($condition)
    {
        if (isset($condition))
        {
            $query = new Zend_Gdata_Docs_Query();
            $query->setQuery($condition);
        }
        else
        {
            $query = null;
        }
        
        $documents_feed = $this->google_docs->getDocumentListFeed($query);
        return $documents_feed->getTotalResults()->getText();
    }

    /* (non-PHPdoc)
     * @see application/common/external_repository_manager/ExternalRepositoryConnector#retrieve_external_repository_objects()
     */
    function retrieve_external_repository_objects($condition, $order_property, $offset, $count)
    {
        if (isset($condition))
        {
            $query = new Zend_Gdata_Docs_Query();
            $query->setQuery($condition);
        }
        else
        {
            $query = null;
        }
        
        $documents_feed = $this->google_docs->getDocumentListFeed($query);
        
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
            $object->set_external_repository_id($this->get_external_repository_instance_id());
            $object->set_title($document->getTitle()->getText());
            $object->set_created($published_timestamp);
            $object->set_type($resource_id[0]);
            $object->set_viewed($last_viewed_timestamp);
            $object->set_modified($modified_timestamp);
            $object->set_owner_id($author->getEmail()->getText());
            $object->set_modifier_id($modifier->getEmail()->getText());
            $object->set_content($document->getContent()->getSrc());
            $object->set_rights($this->determine_rights());
            
            $objects[] = $object;
        }
        
        return new ArrayResultSet($objects);
    }

    /**
     * @param string $folder_url
     * @return array
     */
    function retrieve_folders($folder_url)
    {
        $folder_root = array();
        $folders_feed = $this->google_docs->getFolderListFeed();
        
        $my_folders = array();
        $my_folders['title'] = Translation :: get('MyFolders');
        $my_folders['url'] = str_replace('__PLACEHOLDER__', '', $folder_url);
        $my_folders['class'] = 'category';
        
        $shared_folders = array();
        $shared_folders['title'] = Translation :: get('SharedFolders');
        $shared_folders['url'] = str_replace('__PLACEHOLDER__', '', $folder_url);
        $shared_folders['class'] = 'shared_objects';
        
        $objects = array();
        foreach ($folders_feed->entries as $folder)
        {
            if ($folder->getLink('http://schemas.google.com/docs/2007#parent') instanceof Zend_Gdata_App_Extension_Link)
            {
                $parent = $folder->getLink('http://schemas.google.com/docs/2007#parent')->getTitle();
            }
            else
            {
                if ($folder->getEditLink())
                {
                    $parent = '--my--';
                }
                else
                {
                    $parent = '--shared--';
                }
            }
            
            if (! is_array($objects[$parent]))
            {
                $objects[$parent] = array();
            }
            
            if (! isset($objects[$parent][$folder->getTitle()->getText()]))
            {
                $objects[$parent][$folder->getTitle()->getText()] = $folder;
            }
        }
        
        $my_folders['sub'] = $this->get_folder_tree('--my--', $objects, $folder_url);
        $shared_folders['sub'] = $this->get_folder_tree('--shared--', $objects, $folder_url);
        
        $folder_root[] = $my_folders;
        $folder_root[] = $shared_folders;
        
        return $folder_root;
    }

    /**
     * @param string $index
     * @param array $folders
     * @param string $folder_url
     * @return array
     */
    function get_folder_tree($index, $folders, $folder_url)
    {
        $items = array();
        foreach ($folders[$index] as $child)
        {
            $sub_folder = array();
            $sub_folder['title'] = $child->getTitle()->getText();
            $sub_folder['url'] = str_replace('__PLACEHOLDER__', urlencode($child->getTitle()->getText()), $folder_url);
            $sub_folder['class'] = 'category';
            
            $children = $this->get_folder_tree($child->getTitle()->getText(), $folders, $folder_url);
            
            if (count($children) > 0)
            {
                $sub_folder['sub'] = $children;
            }
            
            $items[] = $sub_folder;
        }
        return $items;
    }
    
    function determine_rights()
    {
        $rights = array();
        $rights[ExternalRepositoryObject :: RIGHT_USE] = true;
        $rights[ExternalRepositoryObject :: RIGHT_EDIT] = false;
        $rights[ExternalRepositoryObject :: RIGHT_DELETE] = false;
        $rights[ExternalRepositoryObject :: RIGHT_DOWNLOAD] = false;
        return $rights;
    }
}
?>