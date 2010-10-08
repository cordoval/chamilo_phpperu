<?php 

class DatabaseTemplateCache extends TemplateCache
{
	private $connection;
	private $theme;
	
	function DatabaseTemplateCache($theme)
	{
		$this->theme = $theme;
		
		$this->connection = mysql_connect('localhost', 'root');
		mysql_select_db('test', $this->connection);
	}
	
	function cache($handle, $uncompiled_code, $compiled_code)
	{
		$query = 'DELETE FROM cache WHERE theme=\'' . $this->theme . '\' AND handle=\'' . $handle . '\';';
		mysql_query($query, $this->connection);
		
		$query = 'INSERT INTO cache SET theme=\'' . $this->theme . '\', handle=\'' . $handle . 
				 '\', uncompiled_code_hash=\'' . md5($uncompiled_code) . '\', compiled_code=\'' . mysql_real_escape_string($compiled_code) . '\';';

		mysql_query($query, $this->connection);
		
		return true;
	}
	
	function retrieve_from_cache($handle, $uncompiled_code)
	{
		$query = 'SELECT compiled_code FROM cache WHERE theme=\'' . $this->theme . '\' AND handle=\'' . $handle . 
				 '\' AND uncompiled_code_hash=\'' . md5($uncompiled_code) . '\';';
		$result = mysql_query($query, $this->connection);
		
		$row = mysql_fetch_array($result, MYSQL_ASSOC);
		$compiled_code = $row['compiled_code'];
		
		if($compiled_code && $compiled_code != '')
			return $compiled_code;
		
		return false;
	}
}

?>