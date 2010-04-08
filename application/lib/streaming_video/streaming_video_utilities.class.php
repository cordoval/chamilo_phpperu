<?php

/*
 * This file is part of Ovis.
 * 
 * Ovis is free software: you can redistribute it and/or modify it under the
 * terms of the GNU General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any later
 * version.
 * 
 * Ovis is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
 * A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License along with
 * Ovis. If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Provides useful methods for Ovis-based applications.
 * 
 * @author Tim De Pauw
 * @package ovis
 */

class StreamingVideoUtilities {
	static function get_clip_thumbnail_url($clip) {
		return OVIS_THUMBNAILS_URL . '/' . $clip->get_id() . '.jpg';
	}

	static function get_clip_stream_url($clip, $profile) {
		return OVIS_STREAMS_URL . '/' . $clip->get_id()
			. '-' . $profile->get_name() . '.ogg';
	}
	
	static function get_generic_playback_xhtml($clip, $profile, $object_id = null) {
		$stream_url = htmlspecialchars(
			self::get_clip_stream_url($clip, $profile));
		list($width, $height) = self::determine_dimensions($clip, $profile);
		$id = (!is_null($object_id)
			? ' id="' . htmlspecialchars($object_id) . '"'
			: '');
		return <<<END
<object type="application/ogg"$id
data="$stream_url" width="$width" height="$height">
</object>
END;
	}
	
	static function print_generic_playback_xhtml($clip, $profile, $object_id = null) {
		echo self::get_generic_playback_xhtml($clip, $profile);
	}

	static function get_applet_playback_xhtml($clip, $profile, $object_id = null) {
		$stream_url = htmlspecialchars(
			self::get_clip_stream_url($clip, $profile));
		list($width, $height) = self::determine_dimensions($clip, $profile);
		$jar = 'cortado-ovt.jar';
		$jar_url = htmlspecialchars(OVIS_CORTADO_URL . '/' . $jar);
		$class = 'com.fluendo.player.Cortado';
		$duration = $clip->get_duration() / 1000;
		$params = array(
			'url' => $stream_url,
			'duration' => $duration,
			'seekable' => 'true'
		);
		if (defined('OVIS_CORTADO_STATUS_HEIGHT')) {
			$params['statusHeight'] = OVIS_CORTADO_STATUS_HEIGHT;
		}
		$params_html = '';
		foreach ($params as $name => $value) {
			$params_html .= '<param'
				. ' name="' . htmlspecialchars($name) . '"'
				. ' value="' . htmlspecialchars($value) . '"/>' . CRLF;
		}
		$id = (!is_null($object_id)
			? ' id="' . htmlspecialchars($object_id) . '"'
			: '');
		$html = '<!--[if !IE]>-->' . CRLF
			. '<object' . $id . ' classid="java:' . $class . '.class"' . CRLF
			. 'type="application/x-java-applet"' . CRLF
			. 'archive="' . $jar_url . '" '
			. 'width="' . $width . '" height="' . $height . '">' . CRLF
			. '<param name="archive" value="' . $jar_url . '"/>' . CRLF
			. $params_html
			. '<!--<![endif]-->' . CRLF
			. '<object' . $id . CRLF
			. 'classid="clsid:8AD9C840-044E-11D1-B3E9-00805F499D93"' . CRLF 
			. 'width="' . $width . '" height="' . $height . '">' . CRLF
			. '<param name="code" value="' . $class . '"/>' . CRLF
			. '<param name="archive" value="' . $jar_url . '"/>' . CRLF
			. $params_html
			. '</object>' . CRLF
			. '<!--[if !IE]>-->' . CRLF
			. '</object>' . CRLF
			. '<!--<![endif]-->' . CRLF;
		return $html;
	}

	static function print_applet_playback_xhtml($clip, $profile, $object_id = null) {
		echo self::get_applet_playback_xhtml($clip, $profile);
	}
	
	private static function determine_dimensions($clip, $profile) {
		$ar = $clip->get_aspect_ratio();
		$max_width = $profile->get_width();
		$max_height = $profile->get_height();
		if ($ar > $max_width / $max_height) {
			$width = $max_width;
			$height = round($width / $ar);
		}
		else {
			$height = $max_height;
			$width = round($height * $ar);
		}
		return array($width, $height);
	}
	
	static function get_jnlp($username, $password, $ws_url,
			$jnlp_codebase_url, $jnlp_relative_url,
			$uploader_codebase_url) {
		$hostname = PlatformSetting :: get('upload_hostname','streaming_video');
		$port = PlatformSetting :: get('upload_port','streaming_video');
		$username = htmlspecialchars($username);
		$password = htmlspecialchars($password);
		$ws_url = htmlspecialchars($ws_url);
		$jnlp_codebase_url = htmlspecialchars($jnlp_codebase_url);
		$jnlp_relative_url = htmlspecialchars($jnlp_relative_url);
		$uploader_codebase_url = htmlspecialchars($uploader_codebase_url);
		return <<<END
<?xml version="1.0" encoding="utf-8"?><jnlp spec="1.0+" codebase="$jnlp_codebase_url" href="$jnlp_relative_url">
	<information>
		<title>Clip Uploader</title>
		<vendor>Ovis</vendor>
		<homepage href="http://ovis.pwnt.be/"/>
		<description>Clip Uploader</description>
		<description kind="short">
			Uploads media clips
		</description>
	</information>
	
	<security>
		<all-permissions/>
	</security>
	
	<resources>
		<j2se version="1.5+" href="http://java.sun.com/products/autodl/j2se"/>
		<jar href="$uploader_codebase_url/be.pwnt.ovis.jar"/>
		<jar href="$uploader_codebase_url/be.pwnt.ftp.jar"/>
		<jar href="$uploader_codebase_url/axis.jar"/>
		<jar href="$uploader_codebase_url/commons-discovery.jar"/>
		<jar href="$uploader_codebase_url/commons-logging.jar"/>
		<jar href="$uploader_codebase_url/commons-net.jar"/>
		<jar href="$uploader_codebase_url/jaxrpc.jar"/>
		<jar href="$uploader_codebase_url/saaj.jar"/>
		<property name="be.pwnt.ovis.upload.hostname" value="$hostname"/>
		<property name="be.pwnt.ovis.upload.port" value="$port"/>
		<property name="be.pwnt.ovis.upload.username" value="$username"/>
		<property name="be.pwnt.ovis.upload.password" value="$password"/>
		<property name="be.pwnt.ovis.upload.webservice" value="$ws_url"/>
	</resources>
	
	<application-desc main-class="be.pwnt.ovis.upload.ui.ClipUploader"/>
</jnlp>
END;
	}
	
	static function print_jnlp($username, $password, $ws_url,
			$jnlp_codebase_url, $jnlp_relative_url,
			$uploader_codebase_url) {
		header('Content-Type: application/x-java-jnlp-file; charset=UTF-8');
		header('Content-Disposition: inline; filename="uploader.jnlp"');
		echo self::get_jnlp($username, $password, $ws_url,
			$jnlp_codebase_url, $jnlp_relative_url,
			$uploader_codebase_url);
	}

        static function create_upload_pasword()
        {
              

                $configuration = Configuration :: get_instance();
                $algorithm = $configuration->get_parameter('general','hashing_algorithm');
                //return hash($algorithm, $_SERVER['REMOTE_ADDR'] . $configuration->get_parameter('general', 'security_key'));

                //TODO: put code elsewhere
                //require_once Path :: get_user_path().'lib/user_data_manager.class.php';

                $user_id = Session :: get_user_id();
                $udm = UserDataManager::get_instance();
                $user = $udm->retrieve_user($user_id);
                
                return hash($algorithm, $_SERVER['REMOTE_ADDR'] . $user->get_password());
        }
}

?>