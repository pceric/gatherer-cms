<h1>Banners</h1>
<table border="0" width="100%">
  <tr><td colspan="2"><form>Add New Client: <input type="text" name="client"/><input type="hidden" name="mod" value="gcmsBanner"/><input type="submit" name="action" value="Add"/></form></td></tr>
  <tr><td width="100%"><strong>Client</strong></td><td><!-- strong>Banners<br/>(Active/Total)</strong --></td><td>&nbsp;</td></tr>
{foreach from=$data item=client}
  <tr><td><a href="index.php?mod=gcmsBanner&amp;action=client&amp;client={$client.id}">{$client.name}</a></td><td>&nbsp;</td><td><a href="index.php?mod=gcmsBanner&amp;action=delete&amp;client={$client.id}" onclick="return confirm('Really delete?');"><img src="../../images/trash.gif" alt="Delete" /></a></td></tr>
{/foreach}
</table>