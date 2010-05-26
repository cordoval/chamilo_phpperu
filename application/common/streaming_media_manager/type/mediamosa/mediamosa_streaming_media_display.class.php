<?php
/**
 * Description of mediamosa_streaming_media_displayclass
 *
 * @author jevdheyd
 */
class MediamosaStreamingMediaDisplay  extends StreamingMediaObjectDisplay
{
    function get_video_player_as_html()
	{
		$object = $this->get_object();
		$html = array();
		$html[] = '<embed style="margin: 1em 0 1em 0;" height="344" width="425" type="application/x-shockwave-flash" src="' . $object->get_url() . '"></embed>';
		return implode("\n", $html);
	}
}
?>
