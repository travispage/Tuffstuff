<?php
$admin_plugin = Ninukis_Plugin_Admin::get_instance();
$plugin = Ninukis_Plugin::get_instance();
$is_staging = $plugin->isStaging(); // check if we are in the staging env.
$active_tab = @$_GET['tab'] ?: 'general';
$form_url = parse_url($_SERVER['REQUEST_URI']);
$form_url = add_query_arg(array('page'=>'ninukis-plugin','tab'=>$active_tab),$form_url['path']);
?>
<p align="justify">
  Working with your website’s files through SFTP can often mess up your file permissions. Also, some other operations like installing plugins or themes
  can cause your file permissions to get changed. Sometimes, these file permission changes can lead to various errors in your WordPress install.
</p>
<p align="justify">
	To fix these permission related errors, use the &quot;Reset File Permissions&quot; button and we will fix your file permissions right away!
</p>
<?php if ($is_staging): ?>
<div class="alert alert-warning">
  <strong>Note !</strong> Operation will be performed on the staging environment.
</div>
<?php endif; ?>

<form method="post" name="options" action="<?php echo esc_url($form_url); ?>">
  <?php wp_nonce_field( WP_NINUKIS_WP_NAME . '-fileperm' ); ?>
  <input type="submit" name="fix-file-perms" value="Reset File Permissions" class="button-primary" onclick="return confirm('Please be patient, this sometimes can take a while.');"/>
</form>
