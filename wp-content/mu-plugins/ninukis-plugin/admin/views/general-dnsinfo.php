<?php
$admin_plugin = Ninukis_Plugin_Admin::get_instance();
$wp_install_info = $admin_plugin->getWPInstallInfo();
?>
<p align="justify">
	To add your domain you must use your DNS provider's admin tools to create a <code>CNAME</code> 
	record in your domain zone so your host name points to your personal Pressidium&reg; hostname
	<code><?php echo $wp_install_info->publicCNAME; ?></code>. If you are unable to add a <code>CNAME</code> record, alternatively
	use the IP addresses <code><?php echo implode('</code>&<code> ', $wp_install_info->publicIP); ?></code> to create A record(s). Note 
	that you may be required to change these and we recommend using <code>CNAME</code> where possible. For more information about
	the domain mapping process please read the <a target="_blank" href="https://pressidium.com/kb/add-a-domain/">How do I add my domain?</a>
	support article.
</p>
<p align="justify">
	If your setup is compatible with the <code>CNAME</code> approach, please prefer it as it will make things much easier
	if we ever need to move your site to another cluster.
</p>


