<?php
/////////////////////////////////////////////////////////////////
/// getID3() by James Heinrich <info@getid3.org>               //
//  available at http://getid3.sourceforge.net                 //
//            or http://www.getid3.org                         //
/////////////////////////////////////////////////////////////////
// See readme.txt for more details                             //
/////////////////////////////////////////////////////////////////
//                                                             //
// write.real.php                                              //
// module for writing RealAudio/RealVideo tags                 //
// dependencies: module.tag.real.php                           //
//                                                            ///
/////////////////////////////////////////////////////////////////

class getid3_write_real
{
	var $filename;
	var $tag_data     = array();
	var $warnings     = array(); // any non-critical errors will be stored here
	var $errors       = array(); // any critical errors will be stored here
	var $paddedlength = 512;     // minimum length of CONT tag in bytes

	function __construct() {
		return true;
	}

	function WriteReal() {
		// File MUST be writeable - CHMOD(646) at least
		if (is_writeable($this->filename)) {
			if ($fp_source = @fopen($this->filename, 'r+b')) {

				// Initialize getID3 engine
				$getID3 = new getID3;
				$OldThisFileInfo = $getID3->analyze($this->filename);
				if (empty($OldThisFileInfo['real']['chunks']) && !empty($OldThisFileInfo['real']['old_ra_header'])) {
					$this->errors[] = 'Cannot write Real tags on old-style file format';
					fclose($fp_source);
					return false;
				}

				if (empty($OldThisFileInfo['real']['chunks'])) {
					$this->errors[] = 'Cannot write Real tags because cannot find DATA chunk in file';
					fclose($fp_source);
					return false;
				}
				foreach ($OldThisFileInfo['real']['chunks'] as $chunknumber => $chunkarray) {
					$oldChunkInfo[$chunkarray['name']] = $chunkarray;
				}
				if (!empty($oldChunkInfo['CONT']['length'])) {
					$this->paddedlength = max($oldChunkInfo['CONT']['length'], $this->paddedlength);
				}

				$new_CONT_tag_data = $this->GenerateCONTchunk();
				$new_PROP_tag_data = $this->GeneratePROPchunk($OldThisFileInfo['real']['chunks'], $new_CONT_tag_data);
				$new__RMF_tag_data = $this->GenerateRMFchunk($OldThisFileInfo['real']['chunks']);

				if (@$oldChunkInfo['.RMF']['length'] == strlen($new__RMF_tag_data)) {
					fseek($fp_source, $oldChunkInfo['.RMF']['offset'], SEEK_SET);
					fwrite($fp_source, $new__RMF_tag_data);
				} else {
					$this->errors[] = 'new .RMF tag ('.strlen($new__RMF_tag_data).' bytes) different length than old .RMF tag ('.$oldChunkInfo['.RMF']['length'].' bytes)';
					fclose($fp_source);
					return false;
				}

				if (@$oldChunkInfo['PROP']['length'] == strlen($new_PROP_tag_data)) {
					fseek($fp_source, $oldChunkInfo['PROP']['offset'], SEEK_SET);
					fwrite($fp_source, $new_PROP_tag_data);
				} else {
					$this->errors[] = 'new PROP tag ('.strlen($new_PROP_tag_data).' bytes) different length than old PROP tag ('.$oldChunkInfo['PROP']['length'].' bytes)';
					fclose($fp_source);
					return false;
				}

				if (@$oldChunkInfo['CONT']['length'] == strlen($new_CONT_tag_data)) {

					// new data length is same as old data length - just overwrite
					fseek($fp_source, $oldChunkInfo['CONT']['offset'], SEEK_SET);
					fwrite($fp_source, $new_CONT_tag_data);
					fclose($fp_source);
					return true;

				} else {

					if (empty($oldChunkInfo['CONT'])) {
						// no existing CONT chunk
						$BeforeOffset = $oldChunkInfo['DATA']['offset'];
						$AfterOffset  = $oldChunkInfo['DATA']['offset'];
					} else {
						// new data is longer than old data
						$BeforeOffset = $oldChunkInfo['CONT']['offset'];
						$AfterOffset  = $oldChunkInfo['CONT']['offset'] + $oldChunkInfo['CONT']['length'];
					}
					if ($tempfilename = tempnam('*', 'getID3')) {
						ob_start();
						if ($fp_temp = fopen($tempfilename, 'wb')) {

							rewind($fp_source);
							fwrite($fp_temp, fread($fp_source, $BeforeOffset));
							fwrite($fp_temp, $new_CONT_tag_data);
							fseek($fp_source, $AfterOffset, SEEK_SET);
							while ($buffer = fread($fp_source, GETID3_FREAD_BUFFER_SIZE)) {
								fwrite($fp_temp, $buffer, strlen($buffer));
							}
							fclose($fp_temp);

							if (copy($tempfilename, $this->filename)) {
								unlink($tempfilename);
								fclose($fp_source);
								return true;
							}
							unlink($tempfilename);
							$this->errors[] = 'FAILED: copy('.$tempfilename.', '.$this->filename.') - '.strip_tags(ob_get_contents());

						} else {

							$this->errors[] = 'Could not open '.$tempfilename.' mode "wb" - '.strip_tags(ob_get_contents());

						}
						ob_end_clean();
					}
					fclose($fp_source);
					return false;

				}


			} else {
				$this->errors[] = 'Could not open '.$this->filename.' mode "r+b"';
				return false;
			}
		}
		$this->errors[] = 'File is not writeable: '.$this->filename;
		return false;
	}

	function GenerateRMFchunk(&$chunks) {
		$oldCONTexists = false;
		foreach ($chunks as $key => $chunk) {
			$chunkNameKeys[$chunk['name']] = $key;
			if ($chunk['name'] == 'CONT') {
				$oldCONTexists = true;
			}
		}
		$newHeadersCount = $chunks[$chunkNameKeys['.RMF']]['headers_count'] + ($oldCONTexists ? 0 : 1);

		$RMFchunk  = "\x00\x00"; // object version
		$RMFchunk .= getid3_lib::BigEndian2String($chunks[$chunkNameKeys['.RMF']]['file_version'], 4);
		$RMFchunk .= getid3_lib::BigEndian2String($newHeadersCount,                                4);

		$RMFchunk  = '.RMF'.getid3_lib::BigEndian2String(strlen($RMFchunk) + 8, 4).$RMFchunk; // .RMF chunk identifier + chunk length
		return $RMFchunk;
	}

	function GeneratePROPchunk(&$chunks, &$new_CONT_tag_data) {
		$old_CONT_length = 0;
		$old_DATA_offset = 0;
		$old_INDX_offset = 0;
		foreach ($chunks as $key => $chunk) {
			$chunkNameKeys[$chunk['name']] = $key;
			if ($chunk['name'] == 'CONT') {
				$old_CONT_length = $chunk['length'];
			} elseif ($chunk['name'] == 'DATA') {
				if (!$old_DATA_offset) {
					$old_DATA_offset = $chunk['offset'];
				}
			} elseif ($chunk['name'] == 'INDX') {
				if (!$old_INDX_offset) {
					$old_INDX_offset = $chunk['offset'];
				}
			}
		}
		$CONTdelta = strlen($new_CONT_tag_data) - $old_CONT_length;

		$PROPchunk  = "\x00\x00"; // object version
		$PROPchunk .= getid3_lib::BigEndian2String($chunks[$chunkNameKeys['PROP']]['max_bit_rate'],    4);
		$PROPchunk .= getid3_lib::BigEndian2String($chunks[$chunkNameKeys['PROP']]['avg_bit_rate'],    4);
		$PROPchunk .= getid3_lib::BigEndian2String($chunks[$chunkNameKeys['PROP']]['max_packet_size'], 4);
		$PROPchunk .= getid3_lib::BigEndian2String($chunks[$chunkNameKeys['PROP']]['avg_packet_size'], 4);
		$PROPchunk .= getid3_lib::BigEndian2String($chunks[$chunkNameKeys['PROP']]['num_packets'],     4);
		$PROPchunk .= getid3_lib::BigEndian2String($chunks[$chunkNameKeys['PROP']]['duration'],        4);
		$PROPchunk .= getid3_lib::BigEndian2String($chunks[$chunkNameKeys['PROP']]['preroll'],         4);
		$PROPchunk .= getid3_lib::BigEndian2String(max(0, $old_INDX_offset + $CONTdelta),              4);
		$PROPchunk .= getid3_lib::BigEndian2String(max(0, $old_DATA_offset + $CONTdelta),              4);
		$PROPchunk .= getid3_lib::BigEndian2String($chunks[$chunkNameKeys['PROP']]['num_streams'],     2);
		$PROPchunk .= getid3_lib::BigEndian2String($chunks[$chunkNameKeys['PROP']]['flags_raw'],       2);

		$PROPchunk  = 'PROP'.getid3_lib::BigEndian2String(strlen($PROPchunk) + 8, 4).$PROPchunk; // PROP chunk identifier + chunk length
		return $PROPchunk;
	}

	function GenerateCONTchunk() {
		foreach ($this->tag_data as $key => $value) {
			// limit each value to 0xFFFF bytes
			$this->tag_data[$key] = substr($value, 0, 65535);
		}

		$CONTchunk  = "\x00\x00"; // object version

		$CONTchunk .= getid3_lib::BigEndian2String(strlen(@$this->tag_data['title']), 2);
		$CONTchunk .= @$this->tag_data['title'];

		$CONTchunk .= getid3_lib::BigEndian2String(strlen(@$this->tag_data['artist']), 2);
		$CONTchunk .= @$this->tag_data['artist'];

		$CONTchunk .= getid3_lib::BigEndian2String(strlen(@$this->tag_data['copyright']), 2);
		$CONTchunk .= @$this->tag_data['copyright'];

		$CONTchunk .= getid3_lib::BigEndian2String(strlen(@$this->tag_data['comment']), 2);
		$CONTchunk .= @$this->tag_data['comment'];

		if ($this->paddedlength > (strlen($CONTchunk) + 8)) {
			$CONTchunk .= str_repeat("\x00", $this->paddedlength - strlen($CONTchunk) - 8);
		}

		$CONTchunk  = 'CONT'.getid3_lib::BigEndian2String(strlen($CONTchunk) + 8, 4).$CONTchunk; // CONT chunk identifier + chunk length

		return $CONTchunk;
	}

	function RemoveReal() {
		// File MUST be writeable - CHMOD(646) at least
		if (is_writeable($this->filename)) {
			if ($fp_source = @fopen($this->filename, 'r+b')) {

				// Initialize getID3 engine
				$getID3 = new getID3;
				$OldThisFileInfo = $getID3->analyze($this->filename);
				if (empty($OldThisFileInfo['real']['chunks']) && !empty($OldThisFileInfo['real']['old_ra_header'])) {
					$this->errors[] = 'Cannot remove Real tags from old-style file format';
					fclose($fp_source);
					return false;
				}

				if (empty($OldThisFileInfo['real']['chunks'])) {
					$this->errors[] = 'Cannot remove Real tags because cannot find DATA chunk in file';
					fclose($fp_source);
					return false;
				}
				foreach ($OldThisFileInfo['real']['chunks'] as $chunknumber => $chunkarray) {
					$oldChunkInfo[$chunkarray['name']] = $chunkarray;
				}

				if (empty($oldChunkInfo['CONT'])) {
					// no existing CONT chunk
					fclose($fp_source);
					return true;
				}

				$BeforeOffset = $oldChunkInfo['CONT']['offset'];
				$AfterOffset  = $oldChunkInfo['CONT']['offset'] + $oldChunkInfo['CONT']['length'];
				if ($tempfilename = tempnam('*', 'getID3')) {
					ob_start();
					if ($fp_temp = fopen($tempfilename, 'wb')) {

						rewind($fp_source);
						fwrite($fp_temp, fread($fp_source, $BeforeOffset));
						fseek($fp_source, $AfterOffset, SEEK_SET);
						while ($buffer = fread($fp_source, GETID3_FREAD_BUFFER_SIZE)) {
							fwrite($fp_temp, $buffer, strlen($buffer));
						}
						fclose($fp_temp);

						if (copy($tempfilename, $this->filename)) {
							unlink($tempfilename);
							fclose($fp_source);
							return true;
						}
						unlink($tempfilename);
						$this->errors[] = 'FAILED: copy('.$tempfilename.', '.$this->filename.') - '.strip_tags(ob_get_contents());

					} else {

						$this->errors[] = 'Could not open '.$tempfilename.' mode "wb" - '.strip_tags(ob_get_contents());

					}
					ob_end_clean();
				}
				fclose($fp_source);
				return false;


			} else {
				$this->errors[] = 'Could not open '.$this->filename.' mode "r+b"';
				return false;
			}
		}
		$this->errors[] = 'File is not writeable: '.$this->filename;
		return false;
	}

}

?>