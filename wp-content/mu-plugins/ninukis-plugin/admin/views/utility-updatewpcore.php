<?php
$admin_plugin = Ninukis_Plugin_Admin::get_instance();
$plugin = Ninukis_Plugin::get_instance();
$is_staging = $plugin->isStaging(); // check if we are in the staging env.
$active_tab = @$_GET['tab'] ?: 'general';
$form_url = parse_url($_SERVER['REQUEST_URI']);
$form_url = add_query_arg(array('page'=>'ninukis-plugin','tab'=>$active_tab),$form_url['path']);
$latestVersion = $plugin->getLatestWordPressVersion();
$versionInfo = $plugin->getVersionInfo();
$isUpgradeAvailable = strcmp((string)$versionInfo->wpVersion, (string)$latestVersion) != 0;
?>
<p align="justify">
  Updating the software core in WordPress is easy, but on Pressidium&reg; it is even easier <strong>and safer</strong>! 
  Why? Because before upgrading your site we automatically backup your files and database, so in the unfortunate case 
  the upgrade breaks your site, we can instantly rollback the upgrade and bring your site back to it's working state.  
</p>
<p align="justify">
	As part of Pressidium&reg; Pinnacle Platform management (<em>remember we are a fully managed service</em>), we are responsible
	for upgrading your site in a secure and timely manner. In case you are in a big hurry and you wish to upgrade your WordPress 
	site to the latest version immediately, without waiting for our scheduled deployment, you can bypass our upgrade cycle and 
	trigger the update process by yourself. To do so, just press the button below.
</p>
<?php if ($latestVersion): ?>
  <?php if ($isUpgradeAvailable): ?>
    <?php if ($is_staging): ?>
      <div class="alert alert-warning">
        <strong>Note !</strong> If you choose to upgrade, you will upgrade your <strong>staging</strong> environment.
      </div>
    <?php endif; ?>
    <div>
      <form method="post" name="options" action="<?php echo esc_url($form_url); ?>">
        <?php wp_nonce_field( WP_NINUKIS_WP_NAME . '-update-core' ); ?>
        <input type="submit" name="upgrade-wp-core" value="Upgrade to <?php echo $latestVersion; ?>" class="button-primary" onclick="return confirm('Are you totally sure you want to upgrade your site now ?');"/>
      </form>
    </div>
  <?php else: ?>
    <div class="alert alert-info">
      <strong>You are up to date!</strong> There is no update for your WordPress site at the moment.
    </div>
  <?php endif; ?>

<?php else: ?>
  <div class="alert alert-info">
    Version info not available at the moment, please try again later.
  </div>
<?php endif; ?>
