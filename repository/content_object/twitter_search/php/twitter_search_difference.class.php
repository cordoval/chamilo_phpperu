<?php

namespace repository\content_object\twitter_search;

use repository\Difference_Engine;
use repository\ContentObjectDifference;
use common\libraries\StringUtilities;

/**
 * This class can be used to get the difference between twitter searches.
 *
 * @copyright (c) 2011 University of Geneva
 * @license GNU General Public License - http://www.gnu.org/copyleft/gpl.html
 * @author laurent opprecht
 * @package repository.lib.content_object.twitter_search
 */
class TwitterSearchDifference extends ContentObjectDifference {

    function get_difference() {
        $object = $this->get_object();
        $version = $this->get_version();

        $object_string = $object->get_query();
        $object_string = explode(StringUtilities::NEW_LINE, strip_tags($object_string));

        $version_string = $version->get_query();
        $version_string = explode(StringUtilities::NEW_LINE, strip_tags($version_string));

        $td = new Difference_Engine($version_string, $object_string);

        return array_merge(parent :: get_difference(), $td->getDiff());
    }

}

?>