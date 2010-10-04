<?php
require_once Path :: get_admin_path() . 'lib/package_updater/package_updater_source.class.php';

class PackageUpdaterRemoteSource extends PackageUpdaterSource
{

    function get_archive()
    {
        $package = $this->get_parent()->get_remote_package();
                
        $this->set_attributes($package);
        
        $remote_file = $package->get_filename();
        $remote_path = 'http://192.170.200.245/' . $remote_file;
        $local_hash = md5(time() . $remote_path);
        $local_file = $local_hash . '.zip';
        $local_path = Path :: get(SYS_TEMP_PATH) . $local_file;
        
        $remote_handle = fopen($remote_path, 'r');
        $local_handle = fopen($local_path, 'w+b');
        
        if ($remote_handle)
        {
            $this->get_parent()->add_message(Translation :: get('RemotePackageFound'));
            while ($line = fread($remote_handle, 1024))
            {
                fwrite($local_handle, $line);
            }
            
            fclose($remote_handle);
            fclose($local_handle);
        }
        else
        {
            $this->get_parent()->add_message(Translation :: get('RemotePackageNotFound'), PackageUpdater::TYPE_ERROR);
            return false;
        }
        if (! $this->verify_hashes($local_path))
        {

        	$this->get_parent()->add_message(Translation :: get('RemotePackageHashFail'),PackageUpdater::TYPE_ERROR);
            return false;
        }
        else
        {
            $this->get_parent()->add_message(Translation :: get('RemotePackageHashVerified'));
        }
        
        return $local_path;
    }

    function verify_hashes($local_path)
    {
 
    	$attributes = $this->get_attributes();

        if (md5_file($local_path) !== $attributes->get_md5())
        {
        	return false;
        }
        
        if (sha1_file($local_path) !== $attributes->get_sha1())
        {
        	return false;
        }
        
        if (hash_file('sha256', $local_path) !== $attributes->get_sha256())
        {
        	return false;
        }
        
        if (hash_file('sha512', $local_path) !== $attributes->get_sha512())
        {
        	return false;
        }
        return true;
    }
}
?>