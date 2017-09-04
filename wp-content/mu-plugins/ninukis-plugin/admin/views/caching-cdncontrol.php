<?php
$admin_plugin = Ninukis_Plugin_Admin::get_instance();
$wp_install_info = $admin_plugin->getWPInstallInfo();
?>
<?php if($wp_install_info->cdnEnabled) : ?>
<p align="justify">
    CDN support is <strong>enabled</strong> and your static content is currently served from <code><?php echo $wp_install_info->cdnDomainName; ?></code>.
</p>
<p>
  Use the following buttons to control CDN operation on your WordPress site.
</p>
<table class="form-table">
  <tr style="border-top: 1px solid #c0c0c0;">
    <td width="100px">
      <input type="submit" name="purge-cdn" value="Purge CDN Cache" class="button-primary" onclick="return confirm('Are you sure you wish to purge your CDN cache ?');"/>
    </td>
    <td>
      Invalidates <i>everything</i> from your CDN caches. Note, you <strong>can't</strong> undo this operation :-)
    </td>
  </tr>
  <!--
  <tr>
    <td>
      <input  disabled type="submit" name="switch-off-cdn" value="Switch OFF CDN" class="button-primary" onclick="return confirm('Are you sure you wish to switch off CDN ?');"/>
    </td>
    <td>
      Switch <strong>OFF</strong> CDN offloading for your WordPress site. Note, this is completely safe and it will <strong>not</strong> affect your CDN
      caches in any way. Use this option if you wish to temporarily stop generating CDN aware links for your static content.
    </td>
  </tr>
  <tr>
    <td>
      <input type="submit" name="switch-on-cdn" value="Switch ON CDN" class="button-primary" onclick="return confirm('Are you sure you wish to switch on CDN again ?');"/>
    </td>
    <td>
      Switch <strong>ON</strong> again CDN offloading for your WordPress site. Note, this might take few minutes until changes are visible to your visitors.
    </td>
  </tr>
  -->
</table>

<p>
  <strong>Note:</strong> If you wish to change your CDN domain name to a custom domain name, please contact support from the <a href="http://cp.pressidium.com/index.php?r=support/index">customer portal</a>.
</p>
<?php else : ?>
  <p>
  CDN support is not yet enabled for this WordPress installation.
  <p>
  <p>
    To CDN enable your WordPress install, please contact support from the <a href="https://cp.pressidium.com/index.php?r=support/index">customer portal</a>
  </p>
<?php endif; ?>
