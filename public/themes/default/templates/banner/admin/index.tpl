<h1>Banners &gt; {$client} &gt; {if !empty($smarty.get.bid)}Edit Banner{else}Add New Banner{/if}</h1>
<form>
<table border="0" width="100%">
  <tr><td><strong>Type</strong></td><td width="100%">
  {html_options name=size values=$sizes output=$sizes selected=$data.size|default:'468x60'}
  </td></tr>
  <tr><td colspan="2"><input type="radio" name="format" value="url" onclick="toggleFormat(this)" {if $data.image || (!$data.image && !$data.code)}checked="checked" {/if}/> <strong>IMG + URL:</strong></td></tr>
  <tr><td><strong>Image</strong></td><td><input type="text" name="image" id="image" value="{$data.image}" size="50" /></td></tr>
  <tr><td><strong>URL</strong></td><td><input type="text" name="url" id="url" value="{$data.url}" size="50" /></td></tr>
  <tr><td colspan="2"><input type="radio" name="format" value="custom" onclick="toggleFormat(this)" {if $data.code}checked="checked" {/if}/> <strong>Custom Code:</strong></td></tr>
  <tr><td colspan="2"><textarea rows="5" cols="50" name="code" id="code">{$data.code}</textarea></td></tr>
  <tr><td colspan="2"><input type="checkbox" name="active" {if $data.active|default:true}checked="checked" {/if}/> <strong>Active</strong></td></tr>
  <tr><td colspan="2"><input type="submit" name="save" value="Save" /> <input type="submit" name="cancel" value="Cancel" /></td></tr>
</table>
<input type="hidden" name="mod" value="gcmsBanner" />
<input type="hidden" name="action" value="client" />
<input type="hidden" name="client" value="{$smarty.get.client}" />
<input type="hidden" name="bid" value="{$smarty.get.bid}" />
</form>