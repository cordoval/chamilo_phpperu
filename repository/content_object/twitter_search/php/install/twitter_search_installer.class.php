<?php

namespace repository\content_object\twitter_search;

use repository\ContentObjectInstaller;

/**
 *
 * @copyright (c) 2011 University of Geneva
 * @license GNU General Public License - http://www.gnu.org/copyleft/gpl.html
 * @author laurent opprecht
 */
class TwitterSearchContentObjectInstaller extends ContentObjectInstaller {

    function get_path() {
        return dirname(__FILE__);
    }

}

?>