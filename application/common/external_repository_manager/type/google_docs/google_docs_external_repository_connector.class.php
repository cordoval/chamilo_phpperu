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
    
    const FOLDERS_MINE = 1;
    const FOLDERS_SHARED = 2;
    
    const DOCUMENTS_OWNED = 'mine';
    const DOCUMENTS_VIEWED = 'viewed';
    const DOCUMENTS_SHARED = 'shared-with-me';
    const DOCUMENTS_STARRED = 'starred';
    const DOCUMENTS_HIDDEN = 'hidden';
    const DOCUMENTS_TRASH = 'trashed';
    const DOCUMENTS_FILES = 'pdf';
    const DOCUMENTS_DOCUMENTS = 'document';
    const DOCUMENTS_PRESENTATIONS = 'presentation';
    const DOCUMENTS_SPREADSHEETS = 'spreadsheet';
    const DOCUMENTS_DRAWINGS = 'drawings';

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
                $scope = 'http://docs.google.com/feeds/ http://spreadsheets.google.com/feeds/ http://docs.googleusercontent.com';
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
        $document = $this->google_docs->getDoc($id, '');
        
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
        $object->set_content($this->determine_content_url($object));
        $object->set_rights($this->determine_rights());
        
        return $object;
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

    /* (non-PHPdoc)
     * @see application/common/external_repository_manager/ExternalRepositoryConnector#count_external_repository_objects()
     */
    function count_external_repository_objects($condition)
    {
        return $this->get_documents_feed($condition)->getTotalResults()->getText();
    }

    private function get_special_folder_names()
    {
        return array(self :: DOCUMENTS_DOCUMENTS, self :: DOCUMENTS_DRAWINGS, self :: DOCUMENTS_FILES, self :: DOCUMENTS_HIDDEN, self :: DOCUMENTS_VIEWED, self :: DOCUMENTS_OWNED, self :: DOCUMENTS_PRESENTATIONS, self :: DOCUMENTS_SHARED, self :: DOCUMENTS_SPREADSHEETS, self :: DOCUMENTS_STARRED, self :: DOCUMENTS_TRASH);
    }

    private function get_documents_feed($condition)
    {
        $folder = Request :: get(GoogleDocsExternalRepositoryManager :: PARAM_FOLDER);
        
        if (isset($condition))
        {
            $query = new Zend_Gdata_Docs_Query();
            $query->setQuery($condition);
            return $this->google_docs->getDocumentListFeed($query);
        }
        elseif (isset($folder))
        {
            if (in_array($folder, $this->get_special_folder_names()))
            {
                return $this->google_docs->getNamedListFeed($folder);
            }
            else
            {
                return $this->google_docs->getFolderListFeed($folder);
            }
        }
        else
        {
            $query = null;
            return $this->google_docs->getDocumentListFeed($query);
        }
        
        return $this->google_docs->getDocumentListFeed($query);
    }

    /* (non-PHPdoc)
     * @see application/common/external_repository_manager/ExternalRepositoryConnector#retrieve_external_repository_objects()
     */
    function retrieve_external_repository_objects($condition, $order_property, $offset, $count)
    {
        $documents_feed = $this->get_documents_feed($condition);
        
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
            $object->set_content($this->determine_content_url($object));
            $object->set_rights($this->determine_rights());
            
            $objects[] = $object;
        }
        
        return new ArrayResultSet($objects);
    }

    function determine_content_url($object)
    {
        switch ($object->get_type())
        {
            case 'document' :
                $url = 'http://docs.google.com/feeds/download/' . $object->get_type() . 's/Export?docID=' . $object->get_id();
                break;
            case 'presentation' :
                $url = 'http://docs.google.com/feeds/download/' . $object->get_type() . 's/Export?docID=' . $object->get_id();
                break;
            case 'spreadsheet' :
                $url = 'http://spreadsheets.google.com/feeds/download/' . $object->get_type() . 's/Export?key=' . $object->get_id();
                break;
            default :
                // Get the document's content link entry.
                //return array('pdf');
                break;
        }
        
        return $url;
    }

    /**
     * @param string $folder_url
     * @return array
     */
    function retrieve_folders($folder_url)
    {
        $folder_root = array();
        $folders_feed = $this->google_docs->getFoldersListFeed();
        
        $my_folders = array();
        $my_folders['title'] = Translation :: get('MyFolders');
        $my_folders['url'] = '#';
        $my_folders['class'] = 'category';
        
        $shared_folders = array();
        $shared_folders['title'] = Translation :: get('SharedFolders');
        //$shared_folders['url'] = str_replace('__PLACEHOLDER__', null, $folder_url);
        $shared_folders['url'] = '#';
        $shared_folders['class'] = 'shared_objects';
        
        $objects = array();
        foreach ($folders_feed->entries as $folder)
        {
            $parent_link = $folder->getLink('http://schemas.google.com/docs/2007#parent');
            if ($parent_link instanceof Zend_Gdata_App_Extension_Link)
            {
                $parent_url = $parent_link->getHref();
                $parent_id = explode(':', urldecode(str_replace('http://docs.google.com/feeds/documents/private/full/', '', $parent_url)));
                $parent = $parent_id[1];
            }
            else
            {
                if ($folder->getEditLink())
                {
                    $parent = self :: FOLDERS_MINE;
                }
                else
                {
                    $parent = self :: FOLDERS_SHARED;
                }
            }
            
            if (! is_array($objects[$parent]))
            {
                $objects[$parent] = array();
            }
            
            if (! isset($objects[$parent][$folder->getResourceId()->getId()]))
            {
                $objects[$parent][$folder->getResourceId()->getId()] = $folder;
            }
        }
        
        $my_folders['sub'] = $this->get_folder_tree(self :: FOLDERS_MINE, $objects, $folder_url);
        $shared_folders['sub'] = $this->get_folder_tree(self :: FOLDERS_SHARED, $objects, $folder_url);
        
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
            $sub_folder['url'] = str_replace('__PLACEHOLDER__', $child->getResourceId()->getId(), $folder_url);
            $sub_folder['class'] = 'category';
            
            $children = $this->get_folder_tree($child->getResourceId()->getId(), $folders, $folder_url);
            
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

    function download_external_repository_object($url)
    {
        $session_token = $this->google_docs->getHttpClient()->getAuthSubToken();
        $opts = array('http' => array('method' => 'GET', 'header' => "GData-Version: 3.0\r\n" . "Authorization: AuthSub token=\"$session_token\"\r\n"));
        
        return file_get_contents($url, false, stream_context_create($opts));
    }
}
?>