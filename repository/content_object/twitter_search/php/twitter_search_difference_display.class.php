<?php

namespace repository\content_object\twitter_search;

use repository\ContentObject;
use common\libraries\Theme;
use repository\ContentObjectDifferenceDisplay;
use common\libraries\SimpleTemplate;

/**
 * This class can be used to display the difference between twitter searches.
 *
 * @copyright (c) 2011 University of Geneva
 * @license GNU General Public License - http://www.gnu.org/copyleft/gpl.html
 * @author laurent opprecht
 * @package repository.lib.content_object.twitter_search
 */
class TwitterSearchDifferenceDisplay extends ContentObjectDifferenceDisplay {

    function get_diff_as_html() {
        $diff = $this->get_difference();

        $ICON = Theme :: get_image_path(ContentObject :: get_content_object_type_namespace($diff->get_object()->get_type())) . 'logo/' . $diff->get_object()->get_icon_name() . '.png';
        $TITLE = $diff->get_object()->get_title();
        $CREATION_DATE = date(' (d M Y, H:i:s O)', $diff->get_object()->get_creation_date());
        $VERSION_TITLE = $diff->get_version()->get_title();
        $VERSION_CREATION_DATE = date(' (d M Y, H:i:s O)', $diff->get_version()->get_creation_date());

        $FINAL = array();
        foreach ($diff->get_difference() as $d) {
            $FINAL[] = print_r($d->parse('final'), true) . '<br style="clear:both;" />';
        }

        $ORIGIN = array();
        foreach ($diff->get_difference() as $d) {
            $ORIGIN[] = print_r($d->parse('orig'), true) . '<br style="clear:both;" />';
        }

        $html = array();
        $html[] = '<div class="difference" style="background-image: url({$ICON});">';
        $html[] = '<div class="titleleft">';
        $html[] = '{$TITLE}';
        $html[] = '{$CREATION_DATE}';
        $html[] = '</div>';
        $html[] = '<div class="titleright">';
        $html[] = '{$VERSION_TITLE}';
        $html[] = '{$VERSION_CREATION_DATE}';
        $html[] = '</div>';

        $html[] = '<div class="left">';
        $html[] = '{$FINAL}';
        $html[] = '</div>';

        $html[] = '<div class="right">';
        $html[] = '{$ORIGIN}';
        $html[] = '</div>';
        $html[] = '<div class="clear"></div>';
        $html[] = '</div>';
        return SimpleTemplate::ex($html, get_defined_vars());
    }

}

?>