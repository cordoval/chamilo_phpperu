<?php
require_once Path :: get_plugin_path() . 'phpflickr-3.0/phpFlickr.php';
require_once dirname(__FILE__) . '/flickr_external_repository_object.class.php';

/**
 * @author Scaramanga
 *
 * Test developer key for Flickr: 61a0f40b9cb4c22ec6282e85ce2ae768
 */

class FlickrExternalRepositoryConnector
{
    private static $instance;

    private $manager;
    private $flickr;

    private $key;
    private $secret;

    private $licenses;

    function FlickrExternalRepositoryConnector($manager)
    {
        $this->manager = $manager;

        $this->key = PlatformSetting :: get('flickr_key', RepositoryManager :: APPLICATION_NAME);
        $this->secret = PlatformSetting :: get('flickr_secret', RepositoryManager :: APPLICATION_NAME);
        $this->flickr = new phpFlickr($this->key, $this->secret);

        $session_token = LocalSetting :: get('flickr_session_token', UserManager :: APPLICATION_NAME);

        if (! $session_token)
        {
            $frob = Request :: get('frob');

            if (! $frob)
            {
                if ($manager->is_stand_alone())
                {
                    $next_url = PATH :: get(WEB_PATH) . 'common/launcher/index.php?type=flickr&application=external_repository';
                }
                else
                {
                    $next_url = PATH :: get(WEB_PATH) . 'core.php?go=external_repository&application=repository&category=0&type=flickr';
                }

                $this->flickr->auth("delete", $next_url);
            }
            else
            {
                $token = $this->flickr->auth_getToken($frob);
                if ($token['token'])
                {
                    LocalSetting :: create_local_setting('flickr_session_token', $token['token'], UserManager :: APPLICATION_NAME, $this->manager->get_user_id());
                }
            }
        }
        else
        {
            $this->flickr->setToken($session_token);
        }
    }

    static function get_instance($manager)
    {
        if (! isset(self :: $instance))
        {
            self :: $instance = new FlickrExternalRepositoryConnector($manager);
        }
        return self :: $instance;
    }

    function get_licenses()
    {
        if (!isset($this->licenses))
        {
            $raw_licenses = $this->flickr->photos_licenses_getInfo();

            $this->licenses = array();
            foreach($raw_licenses as $raw_license)
            {
                $this->licenses[$raw_license['id']] = array('name' => $raw_license['name'], 'url' => $raw_license['url']);
            }
        }

        return $this->licenses;
    }

    function retrieve_external_repository_objects($condition, $order_property, $offset, $count)
    {
        $offset = (($offset - ($offset % $count)) / $count);

        $parameters = array();
        $parameters['api_key'] = $this->key;
        $parameters['per_page'] = 10;
        $parameters['page'] = 1;
        $parameters['privacy_filter'] = 1;
        $parameters['text '] = 'chamilo';

        $licenses = $this->get_licenses();

        $photos = $this->flickr->photos_getRecent(null, $count, $offset);
        $objects = array();

        foreach ($photos['photo'] as $photo)
        {
            $photo_info = $this->flickr->photos_getInfo($photo['id'], $photo['secret']);

            $object = new FlickrExternalRepositoryObject();
            $object->set_id($photo['id']);
            $object->set_title($photo_info['title']);
            $object->set_description($photo_info['description']);
            $object->set_created($photo_info['dates']['posted']);
            $object->set_owner_id($photo_info['owner']['username']);

            $photo_sizes = $this->flickr->photos_getSizes($photo['id']);
            $photo_urls = array();

            foreach ($photo_sizes as $photo_size)
            {
                $key = strtolower($photo_size['label']);
                unset($photo_size['label']);
                unset($photo_size['media']);
                unset($photo_size['url']);
                $photo_urls[$key] = $photo_size;
            }

            $object->set_urls($photo_urls);
            $object->set_license($licenses[$photo_info['license']]);

            $types = array();
            $types[] = $photo_info['media'];
            if (isset($photo_info['originalformat']))
            {
                $types[] = $photo_info['originalformat'];
            }

            $object->set_type(implode('_', $types));

            $objects[] = $object;
        }

        return new ArrayResultSet($objects);
    }

    function count_external_repository_objects($condition)
    {
        $recent = $this->flickr->photos_getRecent(null, 1, 1);
        return $recent['total'];
    }
}
?>