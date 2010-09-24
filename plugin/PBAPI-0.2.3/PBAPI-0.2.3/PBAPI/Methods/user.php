<?php
/**
 * Photobucket API 
 * Fluent interface for PHP5
 * User methods
 * 
 * @author Photobucket
 * @package PBAPI
 * @subpackage Methods
 * 
 * @copyright Copyright Copyright (c) 2008, Photobucket, Inc.
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 */

/**
 * Load Methods parent
 */
require_once PATH :: get_plugin_path() . 'PBAPI-0.2.3/PBAPI-0.2.3/PBAPI/Methods.php';

/**
 * User Methods
 *
 * @package PBAPI
 * @subpackage Methods
 */
class PBAPI_Methods_user extends PBAPI_Methods {
    
    /**
     * search
     *
     * @param array $params
     */
    public function search($params = null) {
        $this->core->_setParamList($params);
        $this->core->_appendUri('/search');
    }
    
    /**
     * URLs
     *
     * @param array $params
     */
    public function url($params = null) {
        $this->core->_appendUri('/url');
    }
    
    /**
     * Contacts
     *
     * @param array $params
     */
    public function contact($params = null) {
        $this->core->_appendUri('/contact');
    }
    
    /**
     * upload options
     *
     * @param array $params
     */
    public function uploadoption($params = null) {
        $this->core->_setParamList($params);
        $this->core->_appendUri('/uploadoption');
    }
    
    /**
     * get Tags for a user
     *
     * @param string $tagname name of a single tag to get media for
     * @param array $params
     */
    public function tag($tagname = '', $params = null) {
        if (is_array($tagname) && $params == null) {
            $params = $tagname;
            $tagname = '';
        }
        $this->core->_appendUri('/tag/%s', $tagname);
        $this->core->_setParamList($params);
    }
    
    /**
     * get Favorites for a user
     *
     * @param int $favid id of a single favorite
     * @param array $params
     */
    public function favorite($favid = '', $params = null) {
        if (is_array($favid) && $params == null) {
            $params = $favid;
            $favid = '';
        }
        $this->core->_appendUri('/favorite/%s', $favid);
        $this->core->_setParamList($params);
    }
    
}
