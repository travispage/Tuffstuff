<?php
$admin_plugin = Ninukis_Plugin_Admin::get_instance();
$wp_install_info = $admin_plugin->getWPInstallInfo();
?>
<p align="justify">
	Your SFTP account is available at <code><?php echo $wp_install_info->sftpHost; ?></code> and port <code><?php echo $wp_install_info->sftpPort; ?></code>. 
	Your username is <code><?php echo $wp_install_info->sftpAccountName; ?></code> and your password is the same with your Pressidium portal account password.
</p>
<p align="justify">
	<strong>Note:</strong> If you experience odd behavior after uploading content over SFTP to your site, as a best practice you should 
	<a href="<?php echo add_query_arg(array('tab'=>'caching'));?>">reset your site caches</a> and have your 
	<a href="<?php echo add_query_arg(array('tab'=>'utility'));?>">file permissions fixed</a>.
</p>
