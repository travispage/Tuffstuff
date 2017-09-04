<?php
$admin_plugin = Ninukis_Plugin_Admin::get_instance();
?>
<p align="justify">
	For your convenience, you can request an instant backup of your WordPress
	site right now, right from here. Just press the button and we are done!
</p>

<table class="form-table">
  <tr>
    <td width="20px" nowrap style="white-space: nowrap">
      Instant backup comment :
    </td>
    <td width="80%">
      <input type="text" class="form-input-tip" name="backup-comment" placeholder="Instant backup comment ..." value="" size="50" maxlength="100" />
    &nbsp;<em>(Optional)</em>
    </td>
  </tr>
  <tr style="border-bottom: 1px solid #c0c0c0;">
    <td>
      <input type="submit" name="instant-backup" value="Perform Instant Backup" class="button-primary" onclick="return confirm('Please confirm you wish we take an instant backup of your site.');"/>
    </td>
    <td>
      &nbsp;
    </td>
  </tr>
</table>
<div class="alert alert-info">
	<strong>Note:</strong> Always create an instant backup before performing dangerous tasks on your WordPress site.
</div>
