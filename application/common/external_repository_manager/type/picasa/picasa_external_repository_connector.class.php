<?php
require_once 'Zend/Loader.php';
require_once dirname(__FILE__) . '/picasa_external_repository_object.class.php';

class PicasaExternalRepositoryConnector extends ExternalRepositoryConnector
{
    /**
     * @var Zend_Gdata_Photos
     */
    private $picasa;
    
    /**
     * The id of the user on Picasa
     * @var string
     */
    private $user_id;

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

    private function process_photo_entry(Zend_Gdata_Photos_PhotoEntry $photo_entry)
    {
        $object = new PicasaExternalRepositoryObject();
        
        $published = $photo_entry->getPublished()->getText();
        $published_timestamp = strtotime($published);
        
        $modified = $photo_entry->getUpdated()->getText();
        $modified_timestamp = strtotime($modified);
        
        $object->set_external_repository_id($this->get_external_repository_instance_id());
        $object->set_title($photo_entry->getTitle()->getText());
        $object->set_description($photo_entry->getSummary()->getText());
        
        $object->set_created($published_timestamp);
        $object->set_modified($modified_timestamp);
        
        $original = array_shift($photo_entry->getMediaGroup()->getContent());
        $thumbnail = array_shift($photo_entry->getMediaGroup()->getThumbnail());
        
        $medium_url = str_replace('/s72/', '/s500/', $thumbnail->getUrl());
        $medium_info = getimagesize($medium_url);
        
        $photo_urls = array();
        $photo_urls[PicasaExternalRepositoryObject :: SIZE_THUMBNAIL] = array('source' => $thumbnail->getUrl(), 'width' => $thumbnail->getWidth(), 'height' => $thumbnail->getHeight());
        $photo_urls[PicasaExternalRepositoryObject :: SIZE_MEDIUM] = array('source' => $medium_url, 'width' => $medium_info[0], 'height' => $medium_info[1]);
        $photo_urls[PicasaExternalRepositoryObject :: SIZE_ORIGINAL] = array('source' => $original->getUrl(), 'width' => $original->getWidth(), 'height' => $original->getHeight());
        $object->set_urls($photo_urls);
        
        $object->set_type(str_replace('/', '_', $original->getType()));
        
        $license = $photo_entry->getGphotoLicense();
        $object->set_license(array('id' => $license->getId(), 'name' => $license->getName(), 'url' => $license->getUrl()));
        
        if ($photo_entry->getMediaGroup()->getKeywords() instanceof Zend_Gdata_Media_Extension_MediaKeywords)
        {
            $tags = array();
            $tags_elements = explode(',', $photo_entry->getMediaGroup()->getKeywords()->getText());
            
            foreach ($tags_elements as $tag)
            {
                $tags[] = $tag;
            }
            
            $object->set_tags($tags);
        
        }
        
        $object->set_album_id($photo_entry->getGphotoAlbumId());
        
        return $object;
    }

    /**
     * @param string $id
     */
    function retrieve_external_repository_object($id)
    {
        $identifiers = explode(':', $id);
        
        $photo_query = new Zend_Gdata_Photos_PhotoQuery();
        $photo_query->setUser($identifiers[0]);
        $photo_query->setAlbumId($identifiers[1]);
        $photo_query->setPhotoId($identifiers[2]);
        $photo_query->setType("entry");
        
        $user_query = new Zend_Gdata_Photos_UserQuery();
        $user_query->setUser($identifiers[0]);
        $user_query->setType("entry");
        
        $photo_entry = $this->picasa->getPhotoEntry($photo_query);
        $photo_user = $this->picasa->getUserEntry($user_query);
        
        $object = $this->process_photo_entry($photo_entry);
        $object->set_owner_id($photo_user->getGphotoUser()->getText());
        $object->set_owner($photo_user->getGphotoNickname()->getText());
        $object->set_id($photo_user->getGphotoUser() . ':' . $photo_entry->getGphotoAlbumId()->getText() . ':' . $photo_entry->getGphotoId()->getText());
        $object->set_rights($this->determine_rights($object->get_owner_id()));
        
        return $object;
    }

    /**
     * @param string $id
     */
    function delete_external_repository_object($id)
    {
        $identifiers = explode(':', $id);
        
        $photo_query = new Zend_Gdata_Photos_PhotoQuery();
        $photo_query->setUser($identifiers[0]);
        $photo_query->setAlbumId($identifiers[1]);
        $photo_query->setPhotoId($identifiers[2]);
        $photo_query->setType("entry");
        
        $photo_entry = $this->picasa->getPhotoEntry($photo_query);
        $photo_entry->delete();
        
        return true;
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
        
        if (! empty($condition))
        {
            $query->setQuery($condition);
        }
        
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
        
        if (! empty($condition))
        {
            $query->setQuery($condition);
        }
        
        $user_feed = $this->picasa->getUserFeed(null, $query);
        
        $objects = array();
        foreach ($user_feed as $photo_entry)
        {
            $author = array_shift($photo_entry->getAuthor());
            
            $object = $this->process_photo_entry($photo_entry);
            $object->set_owner_id($author->getEmail()->getText());
            $object->set_owner($author->getName()->getText());
            $object->set_id($author->getEmail()->getText() . ':' . $photo_entry->getGphotoAlbumId()->getText() . ':' . $photo_entry->getGphotoId()->getText());
            $object->set_rights($this->determine_rights($object->get_owner_id()));
            
            $objects[] = $object;
        }
        
        return new ArrayResultSet($objects);
    }

    /**
     * @param string $owner_id
     * @return array
     */
    function determine_rights($owner_id)
    {
        $rights = array();
        $rights[ExternalRepositoryObject :: RIGHT_USE] = true;
        
        if ($owner_id == $this->get_authenticated_user())
        {
            $rights[ExternalRepositoryObject :: RIGHT_EDIT] = true;
            $rights[ExternalRepositoryObject :: RIGHT_DELETE] = true;
            $rights[ExternalRepositoryObject :: RIGHT_DOWNLOAD] = true;
        }
        else
        {
            $rights[ExternalRepositoryObject :: RIGHT_EDIT] = false;
            $rights[ExternalRepositoryObject :: RIGHT_DELETE] = false;
            $rights[ExternalRepositoryObject :: RIGHT_DOWNLOAD] = false;
        }
        return $rights;
    }

    private function get_authenticated_user()
    {
        if (! isset($this->user_id))
        {
            $user_query = new Zend_Gdata_Photos_UserQuery();
            $user_query->setUser('default');
            $user_query->setType('entry');
            
            $this->user_id = $this->picasa->getUserEntry($user_query)->getGphotoUser();
        }
        
        return $this->user_id;
    }

    function get_authenticated_user_albums()
    {
        $albums = array();
        $albums['default'] = Translation :: get('PicasaDropBoxAlbum');
        
        $user_feed = $this->picasa->getUserFeed('default');
        
        foreach ($user_feed as $album)
        {
            $albums[$album->getGphotoId()->getText()] = $album->getTitle()->getText() . ' (' . $album->getGphotoNumPhotos() . ')';
        }
        
        return $albums;
    }

    /**
     * @param array $values
     * @param string $photo_path
     * @return mixed
     */
    function create_external_repository_object($values, $photo)
    {
        $media_source = $this->picasa->newMediaFileSource($photo['tmp_name']);
        $media_source->setContentType($photo["type"]);
        
        $entry = $this->picasa->newPhotoEntry();
        $entry->setMediaSource($media_source);
        $entry->setTitle($this->picasa->newTitle($photo['name']));
        //$entry->setTitle($this->picasa->newTitle($values[PicasaExternalRepositoryObject :: PROPERTY_TITLE]));
        //$entry->setSummary($this->picasa->newSummary($values[PicasaExternalRepositoryObject :: PROPERTY_DESCRIPTION]));
        $entry->setSummary($this->picasa->newSummary($values[PicasaExternalRepositoryObject :: PROPERTY_TITLE]));
        
        $keywords = new Zend_Gdata_Media_Extension_MediaKeywords();
        $keywords->setText($values[PicasaExternalRepositoryObject :: PROPERTY_TAGS]);
        $entry->mediaGroup = new Zend_Gdata_Media_Extension_MediaGroup();
        $entry->mediaGroup->keywords = $keywords;
        
        $album_query = $this->picasa->newAlbumQuery();
        $album_query->setUser($this->get_authenticated_user());
        $album_query->setAlbumId($values[PicasaExternalRepositoryObject :: PROPERTY_ALBUM]);
        
        $entry = $this->picasa->insertPhotoEntry($entry, $album_query->getQueryUrl());
        return $this->get_authenticated_user() . ':' . $entry->getGphotoAlbumId()->getText() . ':' . $entry->getGphotoId()->getText();
    }

    /**
     * @param array $values
     * @return boolean
     */
    function update_external_repository_object($values)
    {
        $identifiers = explode(':', $values[PicasaExternalRepositoryObject :: PROPERTY_ID]);
        
        $photo_query = new Zend_Gdata_Photos_PhotoQuery();
        $photo_query->setUser($identifiers[0]);
        $photo_query->setAlbumId($identifiers[1]);
        $photo_query->setPhotoId($identifiers[2]);
        $photo_query->setType("entry");
        
        $photo_entry = $this->picasa->getPhotoEntry($photo_query);
        $photo_entry->summary->text = $values[PicasaExternalRepositoryObject :: PROPERTY_TITLE];
        //$photo_entry->setGphotoAlbumId($values[PicasaExternalRepositoryObject :: PROPERTY_ALBUM_ID]);
        

        $keywords = new Zend_Gdata_Media_Extension_MediaKeywords();
        $keywords->setText($values[PicasaExternalRepositoryObject :: PROPERTY_TAGS]);
        $photo_entry->mediaGroup->keywords = $keywords;
        $updated_entry = $photo_entry->save();
        
        if ($updated_entry instanceof Zend_Gdata_Photos_PhotoEntry)
        {
            return true;
        }
        else
        {
            return false;
        }
    }
}
?>