<?php
$admin_plugin = Ninukis_Plugin_Admin::get_instance();
$wp_install_info = $admin_plugin->getWPInstallInfo();
?>
<p align="justify">
  You use the following buttons to control cache invalidation on your WordPress site.
</p>


<table class="form-table">
  <tr style="border-top: 1px solid #c0c0c0;">
    <td width="100px">
      <input type="submit" name="purge-all" value="Purge All Caches" class="button-primary" onclick="return confirm('Please be patient, this sometimes takes a while.');"/>
    </td>
    <td>
      Purges the page cache and WordPress’s own object cache.
      <?php if($wp_install_info->cdnEnabled) : ?>
        For CDN cache purge, use the "<i>CDN Cache Control</i>" section.
      <?php endif; ?>
    </td>
  </tr>
  <tr>
    <td>
      <input type="submit" name="purge-objectcache" value="Purge WP Object Cache" class="button-primary" onclick="return confirm('Please be patient, this sometimes takes a while.');"/>
    </td>
    <td>
       Purge WordPress object cache only.
    </td>
  </tr>


</table>
