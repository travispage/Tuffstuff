<?php
$admin_plugin = Ninukis_Plugin_Admin::get_instance();
$wp_install_info = $admin_plugin->getWPInstallInfo();
?>
<p align="justify">
  You use the following buttons to control cache invalidation on the <strong>entire</strong> WordPress Network. 
</p>
<p>
<strong>Note:</strong>&nbsp;Cache operations will be performed for the entire network
</p>

<table class="form-table">

  <tr style="border-top: 1px solid #c0c0c0;">
    <td width="100px">
      <input type="submit" name="purge-network-all" value="Purge All Network Caches" class="button-primary" onclick="return confirm('Please be patient, this sometimes takes a while.');"/>
    </td>
    <td>
      Purges the page cache for the entire network and WordPress’s own object cache.
    </td>
  </tr>
  


</table>
