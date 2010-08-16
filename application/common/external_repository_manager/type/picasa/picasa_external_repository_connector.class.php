<?php
require_once 'Zend/Loader.php';
require_once dirname(__FILE__) . '/picasa_external_repository_object.class.php';

class PicasaExternalRepositoryConnector extends ExternalRepositoryConnector
{
    /**
     * @var Zend_Gdata_Photos
     */
    private $picasa;
    
    const FOLDERS_MINE = 1;
    const FOLDERS_SHARED = 2;
    
    const DOCUMENTS_OWNED = 'mine';
    const DOCUMENTS_VIEWED = 'viewed';
    const DOCUMENTS_SHARED = '-mine';
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
    function PicasaExternalRepositoryConnector($external_repository_instance)
    {
        parent :: __construct($external_repository_instance);
        
        $session_token = ExternalRepositoryUserSetting :: get('session_token', $this->get_external_repository_instance_id());
        
        Zend_Loader :: loadClass('Zend_Gdata_Photos');
        Zend_Loader :: loadClass('Zend_Gdata_Photos_PhotoQuery');
        Zend_Loader :: loadClass('Zend_Gdata_AuthSub');
        
        if (! $session_token)
        {
            if (! isset($_GET['token']))
            {
                $next_url = Redirect :: current_url();
                $scope = 'http://picasaweb.google.com/data';
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
        $this->picasa = new Zend_Gdata_Photos($httpClient, $application);
    }

    /**
     * @param string $id
     */
    function retrieve_external_repository_object($id)
    {
        $id = explode(':', $id);
        
        $query = new Zend_Gdata_Photos_PhotoQuery();
        $query->setUser($id[0]);
        $query->setAlbumId($id[1]);
        $query->setPhotoId($id[2]);
        $query->setType("entry");
        
        $photoEntry = $this->picasa->getPhotoEntry($query);
        
        $published = $photoEntry->getPublished()->getText();
        $published_timestamp = strtotime($published);
        
        $modified = $photoEntry->getUpdated()->getText();
        $modified_timestamp = strtotime($modified);
        
        $object = new PicasaExternalRepositoryObject();
        $object->set_id($photoEntry->getGphotoId()->getText());
        $object->set_external_repository_id($this->get_external_repository_instance_id());
        $object->set_title($photoEntry->getTitle()->getText());
        $object->set_description($photoEntry->getSummary()->getText());
        
        $author = $photoEntry->getAuthor();
        if (count($author) > 0)
        {
            $author = $author[0];
            $object->set_owner_id($author->getEmail()->getText());
        }
        
        $object->set_created($published_timestamp);
        $object->set_type('image');
        $object->set_modified($modified_timestamp);
        $object->set_rights($this->determine_rights());
        
        $originals = $photoEntry->getMediaGroup()->getContent();
        $thumbnails = $photoEntry->getMediaGroup()->getThumbnail();
        
        $photo_urls = array();
        $photo_urls[PicasaExternalRepositoryObject :: SIZE_THUMBNAIL_SMALL] = array('source' => $thumbnails[0]->getUrl(), 'width' => $thumbnails[0]->getWidth(), 'height' => $thumbnails[0]->getHeight());
        $photo_urls[PicasaExternalRepositoryObject :: SIZE_THUMBNAIL_MEDIUM] = array('source' => $thumbnails[1]->getUrl(), 'width' => $thumbnails[1]->getWidth(), 'height' => $thumbnails[1]->getHeight());
        $photo_urls[PicasaExternalRepositoryObject :: SIZE_THUMBNAIL_LARGE] = array('source' => $thumbnails[2]->getUrl(), 'width' => $thumbnails[2]->getWidth(), 'height' => $thumbnails[2]->getHeight());
        $photo_urls[PicasaExternalRepositoryObject :: SIZE_ORIGINAL] = array('source' => $originals[0]->getUrl(), 'width' => $originals[0]->getWidth(), 'height' => $originals[0]->getHeight());
        $object->set_urls($photo_urls);
        
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
        return array();
    }

    /**
     * @param mixed $query
     * @return mixed
     */
    static function translate_search_query($query)
    {
        return $query;
    }

    /* (non-PHPdoc)
     * @see application/common/external_repository_manager/ExternalRepositoryConnector#count_external_repository_objects()
     */
    function count_external_repository_objects($condition)
    {
        $query = $this->picasa->newQuery("http://picasaweb.google.com/data/feed/api/all");
        $query->setParam("kind", "photo");
        $query->setMaxResults(1);
        $query->setStartIndex(1);
        
        return $this->picasa->getUserFeed(null, $query)->getTotalResults()->getText();
    }

    private function get_documents_feed($condition, $order_property = null, $offset = null, $count = null)
    {
        $folder = Request :: get(PicasaExternalRepositoryManager :: PARAM_FOLDER);
        $query = new Zend_Gdata_Docs_Query();
        
        if (isset($condition))
        {
            $query->setQuery($condition);
        }
        elseif (isset($folder))
        {
            if (in_array($folder, $this->get_special_folder_names()))
            {
                $query->setCategory($folder);
            }
            else
            {
                $query->setFolder($folder);
            }
        }
        
        if (count($order_property) > 0)
        {
            switch ($order_property[0]->get_property())
            {
                case PicasaExternalRepositoryObject :: PROPERTY_CREATED :
                    $property = 'last-modified';
                    break;
                case PicasaExternalRepositoryObject :: PROPERTY_TITLE :
                    $property = 'title';
                    break;
                default :
                    $property = null;
            }
            $query->setOrderBy($property);
        }
        
        $query->setMaxResults($count);
        
        if ($offset)
        {
            $query->setStartIndex($offset + 1);
        }
        
        return $this->picasa->getDocumentListFeed($query);
    }

    /* (non-PHPdoc)
     * @see application/common/external_repository_manager/ExternalRepositoryConnector#retrieve_external_repository_objects()
     */
    function retrieve_external_repository_objects($condition, $order_property, $offset, $count)
    {
        $query = $this->picasa->newQuery("http://picasaweb.google.com/data/feed/api/all");
        $query->setParam("kind", "photo");
        $query->setMaxResults($count);
        $query->setStartIndex($offset + 1);
        
        $userFeed = $this->picasa->getUserFeed(null, $query);
        
        $objects = array();
        foreach ($userFeed as $photoEntry)
        {
            $published = $photoEntry->getPublished()->getText();
            $published_timestamp = strtotime($published);
            
            $modified = $photoEntry->getUpdated()->getText();
            $modified_timestamp = strtotime($modified);
            
            $author = $photoEntry->getAuthor();
            $author = $author[0];
            
            $object = new PicasaExternalRepositoryObject();
            $object->set_id($author->getEmail()->getText() . ':' . $photoEntry->getGphotoAlbumId()->getText() . ':' . $photoEntry->getGphotoId()->getText());
            $object->set_external_repository_id($this->get_external_repository_instance_id());
            $object->set_title($photoEntry->getTitle()->getText());
            $object->set_description($photoEntry->getSummary()->getText());
            $object->set_owner_id($author->getName()->getText());
            
            $object->set_created($published_timestamp);
            $object->set_type('image');
            $object->set_modified($modified_timestamp);
            $object->set_rights($this->determine_rights());
            
            $originals = $photoEntry->getMediaGroup()->getContent();
            $thumbnails = $photoEntry->getMediaGroup()->getThumbnail();
            
            $photo_urls = array();
            $photo_urls[PicasaExternalRepositoryObject :: SIZE_THUMBNAIL_SMALL] = array('source' => $thumbnails[0]->getUrl(), 'width' => $thumbnails[0]->getWidth(), 'height' => $thumbnails[0]->getHeight());
            $photo_urls[PicasaExternalRepositoryObject :: SIZE_THUMBNAIL_MEDIUM] = array('source' => $thumbnails[1]->getUrl(), 'width' => $thumbnails[1]->getWidth(), 'height' => $thumbnails[1]->getHeight());
            $photo_urls[PicasaExternalRepositoryObject :: SIZE_THUMBNAIL_LARGE] = array('source' => $thumbnails[2]->getUrl(), 'width' => $thumbnails[2]->getWidth(), 'height' => $thumbnails[2]->getHeight());
            $photo_urls[PicasaExternalRepositoryObject :: SIZE_ORIGINAL] = array('source' => $originals[0]->getUrl(), 'width' => $originals[0]->getWidth(), 'height' => $originals[0]->getHeight());
            $object->set_urls($photo_urls);
            
            $objects[] = $object;
        }
        
        return new ArrayResultSet($objects);
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