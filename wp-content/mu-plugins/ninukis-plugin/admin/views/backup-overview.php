<?php
$admin_plugin = Ninukis_Plugin_Admin::get_instance();
?>
<p align="justify">
	With instant backups, you can quickly and easily take a backup snapshot of your production WordPress site. 
	This feature is really handy for situations where you wish to install and test a plugin while maintaining 
	the ability to easily rollback to the previous state. Generally you should use this feature every time you 
	are performing tasks that could <em>break</em> your production site.
</p>
<p align="justify">
	All of these great functions (and few more) can also be easily performed from the <a target="_blank" 
	href="https://cp.pressidium.com/index.php?r=site/backups&name=<?php echo urlencode(WP_NINUKIS_WP_NAME); ?>">customer portal</a>.
</p>
