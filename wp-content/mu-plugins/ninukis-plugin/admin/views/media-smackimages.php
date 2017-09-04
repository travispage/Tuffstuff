<?php
$admin_plugin = Ninukis_Plugin_Admin::get_instance();
$plugin = Ninukis_Plugin::get_instance();
$is_staging = $plugin->isStaging(); // check if we are in the staging env.
$active_tab = @$_GET['tab'] ?: 'general';
$form_url = parse_url($_SERVER['REQUEST_URI']);
$form_url = add_query_arg(array('page'=>'ninukis-plugin','tab'=>$active_tab),$form_url['path']);
if(isset($_POST) && count($_POST) > 0) {
	if(isset($_POST['_wpnonce']) && wp_verify_nonce($_POST['_wpnonce'], WP_NINUKIS_WP_NAME . '-mediasmack')) {
		if(isset($_POST['media-smacking-switch']) && $_POST['media-smacking-switch'] == 'on') {
			update_option(WP_NINUKIS_WP_NAME . '-autosmack', 1);
		} else {
			update_option(WP_NINUKIS_WP_NAME . '-autosmack', 0);
		}
	}
}
?>
<p>
  Image smacking uses Pressidium's internal service to automatically reduce image sizes without changing the image quality.
</p>
<form role="form" method="post" name="options" action="<?php echo esc_url($form_url); ?>">
  <div class="form-group">
    <label for="media-smacking-switch">Enable Image Smacking on upload</label>
    <?php 
    	wp_nonce_field( WP_NINUKIS_WP_NAME . '-mediasmack' ); 
		$checked = (get_option(WP_NINUKIS_WP_NAME . '-autosmack') == 1)? 'checked' : ''; 
	?>
    <input type="checkbox" class="form-control" id="media-smacking-switch" name="media-smacking-switch" <?php echo $checked;?>> 
  </div>
  <div class="form-group">
  	<p>
  		<button type="submit" class="button-primary">Update</button>
	</p>
  </div>
</form>

