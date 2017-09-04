<?php
$admin_plugin = Ninukis_Plugin_Admin::get_instance();
$wp_install_info = $admin_plugin->getWPInstallInfo();
?>
<?php if($wp_install_info->networkCDNEnabled) : ?>
<p align="justify">
  Currently, the network installation has one or more CDN enabled WP sites. Use 
  the following buttons to mass-control the CDN service on all eligible members
  of the network. 
</p>
  

<p>
<strong>Note:</strong>&nbsp;CDN operations will be performed on the entire network
</p>
<table class="form-table">
  <tr style="border-top: 1px solid #c0c0c0;">
    <td width="100px">
      <input type="submit" name="purge-network-cdn" value="Purge Network CDN Caches" class="button-primary" onclick="return confirm('Are you sure you wish to purge all network CDN caches ?');"/>
    </td>
    <td>
      Invalidates <i>everything</i> from the CDN caches of all sites in the network. Note, you <strong>can't</strong> undo this operation :-)
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
      Switch <strong>ON</strong> CDN offloading for your WordPress site. Note, this might take few minutes until changes are visible to your visitors.
    </td>
  </tr>
  -->
</table>
<?php else : ?>
  <p>
  Currently, there is no CDN enabled site under this network installation, so the operation is
  disabled.
  <p>
  <p>
    To CDN enable a site under this network, please contact support from the <a href="https://cp.pressidium.com/index.php?r=support/index">customer portal</a>
  </p>
<?php endif; ?>
