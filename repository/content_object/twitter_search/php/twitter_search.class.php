<?php

namespace repository\content_object\twitter_search;

use common\libraries\Utilities;
use common\libraries\Versionable;
use repository\ContentObject;

/**
 * A Twitter search/query.
 *
 * @copyright (c) 2011 University of Geneva
 * @license GNU General Public License - http://www.gnu.org/copyleft/gpl.html
 * @author laurent opprecht
 * @package repository.lib.content_object.twitter_search
 */
class TwitterSearch extends ContentObject implements Versionable {

    const PROPERTY_QUERY = 'query';
    const CLASS_NAME = __CLASS__;

    static function get_type_name() {
        return Utilities :: camelcase_to_underscores(Utilities :: get_classname_from_namespace(self :: CLASS_NAME));
    }

    /**
     * The twitter query to execute.
     *
     * @return string
     */
    function get_query() {
        return $this->get_additional_property(self :: PROPERTY_QUERY);
    }

    function set_query($value) {
        return $this->set_additional_property(self :: PROPERTY_QUERY, $value);
    }

    /**
     * Returns the url search query. I.e. the Twitter page that displays the query's results.
     *
     * @return string
     */
    function get_url(){
        if($query = $this->get_query()){
            return 'http://twitter.com/#search?q=' . urlencode($query);
        }else{
            return '';
        }
    }

    static function get_additional_property_names() {
        return array(self :: PROPERTY_QUERY);
    }

}

?>