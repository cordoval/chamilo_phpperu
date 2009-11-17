<?php
/**
 * $Id: web_service_search_source.class.php 222 2009-11-13 14:39:28Z chellee $
 * @package application.search_portal.search_source
 */
require_once dirname(__FILE__) . '/../search_source.class.php';
require_once dirname(__FILE__) . '/web_service/content_object_soap_search_client.class.php';
require_once dirname(__FILE__) . '/web_service/content_object_soap_search_utilities.class.php';
require_once dirname(__FILE__) . '/web_service/content_object_soap_search_result_set.class.php';
require_once dirname(__FILE__) . '/../repository_search_result.class.php';

class WebServiceSearchSource implements SearchSource
{
    const CACHE_TIME = 60; // minutes
    const CACHE_FILE_EXTENSION = 'cache';
    const CACHE_CLEANUP_TAG_FILE = 'cleanup.tag';
    
    private static $cache_dir;
    
    private $client;
    
    private $url;

    function WebServiceSearchSource($url)
    {
        $file = ContentObjectSoapSearchUtilities :: get_wsdl_file_path($url);
        try
        {
            $this->client = new ContentObjectSoapSearchClient($file);
        }
        catch (Exception $ex)
        {
            throw $ex;
        }
        $this->url = $url;
    }

    function search($query)
    {
        if (! ($result = self :: get_cached_result($this->url, $query)))
        {
            try
            {
                $result = $this->client->search($query);
            }
            catch (Exception $ex)
            {
                throw $ex;
            }
            self :: cache_result($this->url, $query, $result);
        }
        $repository_title = $result[ContentObjectSoapSearchClient :: KEY_REPOSITORY_TITLE];
        $repository_url = $result[ContentObjectSoapSearchClient :: KEY_REPOSITORY_URL];
        $returned_results = new ContentObjectSoapSearchResultSet($result[ContentObjectSoapSearchClient :: KEY_RETURNED_RESULTS]);
        $result_count = $result[ContentObjectSoapSearchClient :: KEY_RESULT_COUNT];
        return new RepositorySearchResult($repository_title, $repository_url, $returned_results, $result_count);
    }

    static function is_supported()
    {
        return extension_loaded('soap');
    }

    private function get_cached_result($url, $query)
    {
        $file = self :: cache_file_path($url, $query);
        if (! file_exists($file))
        {
            return null;
        }
        return unserialize(file_get_contents($file));
    }

    private function cache_result($url, $query, $data)
    {
        $serialized = serialize($data);
        $file = self :: cache_file_path($url, $query);
        file_put_contents($file, $serialized);
    }

    private function cache_file_path($url, $query)
    {
        $md5sum = md5($url . "\t" . $query);
        return self :: cache_dir() . '/' . $md5sum . '.' . self :: CACHE_FILE_EXTENSION;
    }

    private function cache_dir()
    {
        if (isset(self :: $cache_dir))
        {
            return self :: $cache_dir;
        }
        self :: $cache_dir = dirname(__FILE__) . '/web_service/result_cache';
        if (! is_dir(self :: $cache_dir) || ! is_writable(self :: $cache_dir))
        {
            die('Cannot write to cache directory "' . self :: $cache_dir . '"');
        }
        self :: clean_cache_dir();
        return self :: $cache_dir;
    }

    private function clean_cache_dir()
    {
        $cache_dir = self :: $cache_dir;
        $tag_file = $cache_dir . '/' . self :: CACHE_CLEANUP_TAG_FILE;
        $min_time = time() - self :: CACHE_TIME * 60;
        $exists = file_exists($tag_file);
        if ($exists && filemtime($tag_file) > $min_time)
        {
            return;
        }
        if ($exists)
        {
            file_put_contents($tag_file, '');
        }
        else
        {
            touch($tag_file);
        }
        $handle = opendir($cache_dir);
        while (($file = readdir($handle)) !== false)
        {
            if (strrpos($file, '.' . self :: CACHE_FILE_EXTENSION) !== false)
            {
                $path = $cache_dir . '/' . $file;
                if (is_file($path) && filemtime($path) < $min_time)
                {
                    unlink($path);
                }
            }
        }
    }
}
?>