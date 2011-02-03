<?php

namespace repository\content_object\twitter_search;

use common\libraries\Translation;
use common\libraries\Path;
use common\libraries\Theme;
use repository\ContentObjectDisplay;
use repository\ContentObject;
use common\libraries\SimpleTemplate;

/**
 *
 * @copyright (c) 2011 University of Geneva
 * @license GNU General Public License - http://www.gnu.org/copyleft/gpl.html
 * @author laurent opprecht
 * @package repository.lib.content_object.twitter_search
 */
class TwitterSearchDisplay extends ContentObjectDisplay {

    function get_widget_html($scrollbar = false, $loop = true, $live = true, $hashtags = true, $timestamp = true, $avatars = true, $toptweets = true) {
        $object = $this->get_content_object();
        $query = $object->get_query();
        $pattern = '/\@[a-zA-Z_][a-zA-Z0-9_]*/';
        if (preg_match($pattern, $query)) {//i.e. query is @username
            return $this->get_widget_profile_html($scrollbar, $loop, $live, $hashtags, $timestamp, $avatars, $toptweets);
        } else {
            return $this->get_widget_search_html($scrollbar, $loop, $live, $hashtags, $timestamp, $avatars, $toptweets);
        }
    }

    function get_widget_search_html($scrollbar = false, $loop = true, $live = true, $hashtags = true, $timestamp = true, $avatars = true, $toptweets = true) {
        $object = $this->get_content_object();

        $bloc_id = 'twtr-widget-' . $object->get_id();
        $query = $object->get_query();
        $subject = $object->get_title();
        $title = '';
        $scrollbar = $scrollbar ? 'true' : 'false';
        $loop = $loop ? 'true' : 'false';
        $live = $live ? 'true' : 'false';
        $hashtags = $hashtags ? 'true' : 'false';
        $timestamp = $timestamp ? 'true' : 'false';
        $avatars = $avatars ? 'true' : 'false';
        $toptweets = $toptweets ? 'true' : 'false';
        $footer = Translation::get('Go');

        $content = <<<EOT

<script src="http://widgets.twimg.com/j/2/widget.js"></script>

<div class="action_bar" ><div class="twtr-container"><div class="twtr-widget" id="$bloc_id"></div></div></div>
<script>
new TWTR.Widget({
  version: 2,
  type: 'search',
  search: '$query',
  interval: 6000,
  title: '$title',
  subject: '$subject',
  width: 'auto',
  height: 300,
  footer: '$footer',
  id: '$bloc_id',
  theme: {
    shell: {
      background: 'transparent',
      color: 'inherit'
    },
    tweets: {
      background: 'none',
      color: 'none',
      links: 'none'
    }
  },
  features: {
    scrollbar: $scrollbar,
    loop: $loop,
    live: $live,
    hashtags: $hashtags,
    timestamp: $timestamp,
    avatars: $avatars,
    toptweets: $toptweets,
    behavior: 'default'
  }
}).render().start();
</script>

EOT;
        return $content;
    }

    function get_widget_profile_html($scrollbar = false, $loop = true, $live = true, $hashtags = true, $timestamp = true, $avatars = true, $toptweets = true) {
        $object = $this->get_content_object();

        $bloc_id = 'twtr-widget-' . $object->get_id();
        $user = $object->get_query();
        $user = trim($user, '@');
        $scrollbar = $scrollbar ? 'true' : 'false';
        $loop = $loop ? 'true' : 'false';
        $live = $live ? 'true' : 'false';
        $hashtags = $hashtags ? 'true' : 'false';
        $timestamp = $timestamp ? 'true' : 'false';
        $avatars = $avatars ? 'true' : 'false';
        $toptweets = $toptweets ? 'true' : 'false';
        $footer = Translation::get('Go');

        $content = <<<EOT
<script src="http://widgets.twimg.com/j/2/widget.js"></script>
<div class="action_bar" ><div class="twtr-container"><div class="twtr-widget" id="$bloc_id"></div></div></div>
<script>
new TWTR.Widget({
  version: 2,
  type: 'profile',
  rpp: 5,
  interval: 6000,
  width: 'auto',
  height: 300,
  footer: '$footer',
  id: '$bloc_id',
  theme: {
    shell: {
      background: 'transparent',
      color: 'inherit'
    },
    tweets: {
      background: 'none',
      color: 'none',
      links: 'none'
    }
  },
  features: {
    scrollbar: $scrollbar,
    loop: $loop,
    live: $live,
    hashtags: $hashtags,
    timestamp: $timestamp,
    avatars: $avatars,
    toptweets: $toptweets,
    behavior: 'all'
  }
}).render().setUser('$user').start();
</script>
EOT;
        return $content;
    }

    function get_full_html() {
        $object = $this->get_content_object();

        $ICON = Theme :: get_image_path(ContentObject :: get_content_object_type_namespace($object->get_type())) . 'logo/' . $object->get_icon_name() . ($object->is_latest_version() ? '' : '_na') . '.png';
        $TITLE = $object->get_title();
        $DESCRIPTION = $this->get_description();
        $WIDGET = $this->get_widget_html();

        $html = array();
        $html[] = '<div class="content_object" style="background-image: url({$ICON});">';
        $html[] = '<div class="title">{$TITLE}</div>';
        $html[] = '<div class="description">{$DESCRIPTION}</div>';
        $html[] = '{$WIDGET}';
        $html[] = '</div>';

        return SimpleTemplate::ex($html, get_defined_vars());
    }

    //Inherited
    function get_list_html() {
        $object = $this->get_content_object();

        $HREF = htmlentities($object->get_url());
        $TITLE = $object->get_title();
        $DESCRIPTION = $object->get_description();

        $html = array();
        $html[] = '<h4 class="table"><a href="{$HREF}">{$TITLE}</a></h4>';
        $html[] = '{$DESCRIPTION}';

        return SimpleTemplate::ex($html, get_defined_vars());
    }

    function get_short_html() {
        $object = $this->get_content_object();

        $HREF = htmlentities($object->get_url());
        $TITLE = $object->get_title();

        $template = '<span class="content_object"><a href="{$HREF}">{$TITLE}</a></span>';

        return SimpleTemplate::ex($template, get_defined_vars());
    }

}

?>