<?php
/**
 * Represents the view for the administration dashboard.
 *
 * This includes the header, options, and other information that should provide
 * The User Interface to the end user.
 *
 * @package   Ninukis Plugin
 * @author    Filip Slavik <filip@pressidium.com>
 * @license   GPL-2.0+
 * @link      http://www.pressidium.com
 */
?>

<?php

  if ( ! current_user_can('manage_options') )
  	return;
  
  //setup form url
  $form_url = parse_url($_SERVER['REQUEST_URI']);
  $admin_plugin = Ninukis_Plugin_Admin::get_instance();
  $plugin = Ninukis_Plugin::get_instance();
  $wp_install_info = $admin_plugin->getWPInstallInfo();
  $is_staging = $plugin->isStaging(); // check if we are in the staging env.
  $screen_suffix = $admin_plugin->plugin_screen_hook_suffix;
  $ninukisCaching = NinukisCaching::get_instance();
  // set the default tab
  if ( is_super_admin() )
	$active_tab = @$_GET['tab'] ?: 'general';
  else 
  	$active_tab = @$_GET['tab'] ?: 'caching';
  
  $form_url = add_query_arg(array('page'=>'ninukis-plugin','tab'=>$active_tab),$form_url['path']);
  $message   = '';
  $error     = '';

  // Process form submissions

  if (isset( $_POST['purge-all'] )) {
    check_admin_referer( WP_NINUKIS_WP_NAME . '-caching' );
    if($plugin->purgeAllCaches()) {
      $message = "All caches have been purged (HTML pages and WordPress Object Cache)";
    } else {
      $error = "Failed to purge all caches. Please try again in a while or contact support.";
    }
  } elseif(isset( $_POST['purge-network-all']) ) {
    check_admin_referer( WP_NINUKIS_WP_NAME . '-network' );
    if($plugin->purge_network_varnish_cache()) {
      $message = "All caches for the entire network has been purged.";
    } else {
      $error = "Failed to purge network caches. Please try again in a while or contact support.";
    }
   } elseif(isset( $_POST['purge-network-cdn']) ) {
    check_admin_referer( WP_NINUKIS_WP_NAME . '-network' );
    if($plugin->purge_network_cdn_cache()) {
      $message = "WordPress object cache is being purged. Depending on the amount of cached content and number of blogs, this might take from few seconds to few minutes to complete.";
    } else {
      $error = "Failed to purge network CDN cache. Please try again or contact support.";
    }
  } elseif(isset( $_POST['purge-objectcache']) ) {
    check_admin_referer( WP_NINUKIS_WP_NAME . '-caching' );
    if( $plugin->purge_site_object_cache()) {
      $message = "WordPress object cache is being purged. Depending on the amount of cached content, this might take few seconds to complete.";
    } else {
      $error = "Failed to purge object cache. Please try again or contact support.";
    }
  } elseif(isset( $_POST['purge-cdn']) ) {
    check_admin_referer( WP_NINUKIS_WP_NAME . '-caching' );
    if($plugin->purge_site_cdn_cache()) {
      $message = "Your CDN cache is currently being purged. Depending on the amount of cached content, this might take few seconds to several minutes to complete. <p>In any case we will send you an e-mail when the operation completes.</p>";
    } else {
      $error = "Failed to purge CDN cache. Please try again or contact support.";
    }
  } elseif(isset( $_POST['instant-backup']) ) {
  	if ( ! is_super_admin() ) /* only by admin or super-admin for multisite */
  		return;
    check_admin_referer( WP_NINUKIS_WP_NAME . '-backup' );
    if($admin_plugin->requestInstantBackup($_POST['backup-comment'])) {
      $message = "Instant backup of your site has been requested. You will receive an e-mail once it is done.";
    } else {
      $error = "Failed to request instant backup. Please try again or contact support.";
    }
  } elseif(isset( $_POST['fix-file-perms']) ) {
    if ( ! is_super_admin() ) /* only by admin or super-admin for multisite */
  		return;
    check_admin_referer( WP_NINUKIS_WP_NAME . '-fileperm' );
    if($admin_plugin->requestFilePermissionFix($is_staging)) {
      $message = "File permissions repair for your site has been requested. You will receive an e-mail once it is done.";
    } else {
      $error = "Failed to request file permissions repair. Please try again or contact support.";
    }
  } elseif(isset( $_POST['upgrade-wp-core']) ) {
  	if ( ! is_super_admin() ) /* only by admin or super-admin for multisite */
  		return;
    check_admin_referer( WP_NINUKIS_WP_NAME . '-update-core' );
    if($admin_plugin->requestWPCoreUpdate($is_staging)) {
      $message = "Update your WP site core request has been sent. You will receive an e-mail once the operation is done.";
    } else {
      $error = "Failed to request WP site core update. Please try again or contact support.";
    }
  }
?>
<h1><img src="<?php echo plugins_url( '../assets/images/pressidium-title.png', __FILE__ ); ?>" alt="Pressidium" style=""><span class="separator"></span></h1>
<div class="wrap">
  <!-- if this is the staging environment please display an alert -->
  <?php if ( $is_staging ) : ?>
  <div class="alert alert-warning">
    <strong>Warning !</strong> You are accessing your staging site. Our plugin has intentionally limited functionality in the staging environment.
  </div>
  <?php endif; ?>

  <?php if ( ! empty( $error ) ) : ?>
    <div class="error"><p><?php echo $error; ?></p></div>
  <?php endif; ?>

  <?php if ( ! empty( $message ) ) : ?>
    <div class="updated fade"><p><?php echo $message; ?></p></div>
  <?php endif; ?>
  <h2 class="nav-tab-wrapper">
    <?php if ( is_super_admin() ) : ?>
    <a class="nav-tab <?php if($active_tab=='general') { echo 'nav-tab-active'; } ?>" href="<?php echo esc_url(add_query_arg(array('tab'=>'general'))); ?>">General</a>
        <a class="nav-tab <?php if($active_tab=='media') { echo 'nav-tab-active'; } ?>" href="<?php echo esc_url(add_query_arg(array('tab'=>'media'))); ?>">Media</a>
    <?php endif; ?>
    <a class="nav-tab <?php if($active_tab=='caching') { echo 'nav-tab-active'; } ?>" href="<?php echo esc_url(add_query_arg(array('tab'=>'caching'))); ?>">Caching</a>
    <?php if ( is_super_admin() && is_multisite() ) : ?>
        <a class="nav-tab <?php if($active_tab=='network') { echo 'nav-tab-active'; } ?>" href="<?php echo esc_url(add_query_arg(array('tab'=>'network'))); ?>">Network</a>
    <?php endif; ?>
    <?php if ( is_super_admin() ) : ?>
        <a class="nav-tab <?php if($active_tab=='backup') { echo 'nav-tab-active'; } ?>" href="<?php echo esc_url(add_query_arg(array('tab'=>'backup'))); ?>">Instant Backup</a>
	<a class="nav-tab <?php if($active_tab=='utility') { echo 'nav-tab-active'; } ?>" href="<?php echo esc_url(add_query_arg(array('tab'=>'utility'))); ?>">Utility</a>
    <?php endif; ?>
   </h2>

  <div class=""> <!-- main content -->


  <?php if( $active_tab == 'general' && is_super_admin() ): ?>
    <div>
      <form method="post">
        <?php
        wp_nonce_field( 'some-action-nonce' );
        /* Used to save closed meta boxes and their order */
        wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false );
        wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false );
        ?>

      	<div id="poststuff">
          <div id="post-body" class="metabox-holder columns-2">

            <div id="postbox-container-1" class="postbox-container">
              <?php do_meta_boxes($screen_suffix.'generaltab-help','general', null); ?>
            </div>

            <div id="postbox-container-2" class="postbox-container">
              <?php do_meta_boxes($screen_suffix.'generaltab-dnsinfo','general', null); ?>
              <?php do_meta_boxes($screen_suffix.'generaltab-sftpinfo','general', null); ?>
            </div>

          </div> <!-- #post-body -->
        </div> <!-- #poststuff -->
      </form>
    </div> <!-- general tab -->
    <?php elseif ( $active_tab == 'media' && is_super_admin() ) : ?> <!-- Media tab -->
  	<div>
        <form method="post" name="options" action="<?php echo esc_url($form_url); ?>">
          <?php
          wp_nonce_field( WP_NINUKIS_WP_NAME . '-media' );
          wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false );
          wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false );
          ?>
            <div id="poststuff">
            <div id="post-body" class="metabox-holder columns-2">

              <div id="postbox-container-1" class="postbox-container">
                <?php do_meta_boxes($screen_suffix.'mediatab-help','media', null); ?>
              </div>

              <div id="postbox-container-2" class="postbox-container">
                <?php do_meta_boxes($screen_suffix.'mediatab-smackimages','media', null); ?>
              </div>

            </div> <!-- #post-body -->
            </div>
        </form>
 	</div> <!-- media tab -->
  <?php elseif ( $active_tab == 'caching') : ?> <!-- caching tab -->
    <div>

      <?php if ($is_staging): ?>
        <div class="alert alert-warning">
          <strong>Note:</strong> Caching functionality is unavailable in staging environment.
        </div>
      <?php else: ?>

        <form method="post" name="options" action="<?php echo esc_url($form_url); ?>">
          <?php
          wp_nonce_field( WP_NINUKIS_WP_NAME . '-caching' );
          /* Used to save closed meta boxes and their order */
          wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false );
          wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false );
          ?>

          <div id="poststuff">
            <div id="post-body" class="metabox-holder columns-2">

              <div id="postbox-container-1" class="postbox-container">
                <?php do_meta_boxes($screen_suffix.'cachingtab-help','caching', null); ?>
              </div>

              <div id="postbox-container-2" class="postbox-container">
                <?php do_meta_boxes($screen_suffix.'cachingtab-overview','caching', null); ?>
                <?php do_meta_boxes($screen_suffix.'cachingtab-control','caching', null); ?>
                <?php do_meta_boxes($screen_suffix.'cachingtab-cdncontrol','caching', null); ?>
              </div>

            </div> <!-- #post-body -->
          </div> <!-- #poststuff -->
        </form>


    <?php endif; ?>

    </div> <!-- caching tab -->
   <?php elseif ( $active_tab == 'network' && is_super_admin() ) : ?> <!-- network tab -->
    <div>
      <?php if ($is_staging): ?>
        <div class="alert alert-warning">
          <strong>Note:</strong> Network operations are unavailable in staging environment.
        </div>
      <?php else: ?>
        <form method="post" name="options" action="<?php echo esc_url($form_url); ?>">
          <?php
          wp_nonce_field( WP_NINUKIS_WP_NAME . '-network' );
          /* Used to save closed meta boxes and their order */
          wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false );
          wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false );
          ?>

          <div id="poststuff">
            <div id="post-body" class="metabox-holder columns-2">

              <div id="postbox-container-1" class="postbox-container">
                <?php do_meta_boxes($screen_suffix.'network-tab-help','network', null); ?>
              </div>

              <div id="postbox-container-2" class="postbox-container">
                <?php do_meta_boxes($screen_suffix.'network-tab-overview','network', null); ?>
                <?php do_meta_boxes($screen_suffix.'network-caching-tab-control','network', null); ?>
                <?php do_meta_boxes($screen_suffix.'network-caching-cdn-tab-control','network', null); ?>
              </div>

            </div> <!-- #post-body -->
          </div> <!-- #poststuff -->
        </form>

    <?php endif; ?>
    </div> <!-- network tab -->
  <?php elseif ( $active_tab == 'backup' && is_super_admin() ) : ?> <!-- backup tab -->
    <div>
      <?php if ($is_staging): ?>
        <div class="alert alert-warning">
          <strong>Note:</strong> Instant backup functionality is unavailable in staging environment.
        </div>
      <?php else: ?>
        <form method="post" name="options" action="<?php echo esc_url($form_url); ?>">
          <?php
          wp_nonce_field( WP_NINUKIS_WP_NAME . '-backup' );
          /* Used to save closed meta boxes and their order */
          wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false );
          wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false );
          ?>

          <div id="poststuff">
            <div id="post-body" class="metabox-holder columns-2">

              <div id="postbox-container-1" class="postbox-container">
                <?php do_meta_boxes($screen_suffix.'backuptab-help','backup', null); ?>
              </div>

              <div id="postbox-container-2" class="postbox-container">
                <?php do_meta_boxes($screen_suffix.'backuptab-overview','backup', null); ?>
                <?php do_meta_boxes($screen_suffix.'backuptab-control','backup', null); ?>
              </div>

            </div> <!-- #post-body -->
          </div> <!-- #poststuff -->
        </form>

    <?php endif; ?>
    </div> <!-- backup tab -->
  <?php elseif ( $active_tab == 'utility' && is_super_admin() ) : ?> <!-- Utilities tab -->
    <div>
      <form method="post" name="options" action="<?php echo esc_url($form_url); ?>">
        <?php
        wp_nonce_field( WP_NINUKIS_WP_NAME . '-utility' );
        /* Used to save closed meta boxes and their order */
        wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false );
        wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false );
        ?>

        <div id="poststuff">
          <div id="post-body" class="metabox-holder columns-2">

            <div id="postbox-container-1" class="postbox-container">
              <?php do_meta_boxes($screen_suffix.'utilitytab-help','utility', null); ?>
            </div>

            <div id="postbox-container-2" class="postbox-container">
              <?php do_meta_boxes($screen_suffix.'utilitytab-fixfileperm','utility', null); ?>
            </div>

            <div id="postbox-container-2" class="postbox-container">
              <?php do_meta_boxes($screen_suffix.'utilitytab-updatewpcore','utility', null); ?>
            </div>

          </div> <!-- #post-body -->
        </div> <!-- #poststuff -->
      </form>



    </div> <!-- utility tab -->
  <?php endif; ?> <!-- sftp tab -->

  </div> <!-- main content div -->


  <br class="clear"/>
  <hr>

  <p>
    Pressidium&reg; WP Plugin<span class="separator"></span> Version <code><?php echo Ninukis_Plugin::VERSION; ?></code> | Problems ? <a href="http://pressidium.com/support" target="_blank">Get Support</a>
  </p>
  

</div>
