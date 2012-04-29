{nocache}
<ul class="breadcrumb">
  <li><a href="{$view->url(['module' => 'admin'],null,true)}">Admin Home</a> <span class="divider">&gt;</span></li>
  <li class="active">Configuration</li>
</ul>
<h1>Site Configuration</h1>
<form method="post">
<table cellspacing="10" border="0">
<tr><td>Site Name</td><td><input type="text" name="sitename" value="{$gcms.config.sitename}" size="64" maxlength="128" /></td></tr>
<tr><td>Site Author</td><td><input type="text" name="siteauthor" value="{$gcms.config.siteauthor}" size="64" maxlength="128" /></td></tr>
<tr><td>Site URL</td><td><input type="text" name="siteURL" value="{$gcms.config.siteURL}" size="64" maxlength="255" /></td></tr>
<tr><td>Contact E-Mail</td><td><input type="text" name="sitecontact" value="{$gcms.config.sitecontact}" size="64" maxlength="255" /></td></tr>
<tr><td>Site Description</td><td><input type="text" name="sitedesc" value="{$gcms.config.sitedesc}" size="64" maxlength="255" /></td></tr>
<tr><td>Site Slogan</td><td><input type="text" name="siteslogan" value="{$gcms.config.siteslogan}" size="64" maxlength="128" /></td></tr>
<tr><td>Site Keywords</td><td><input type="text" name="sitekeywords" value="{$gcms.config.sitekeywords}" size="64" maxlength="128" /></td></tr>
<tr><td>Theme</td><td><select name="sitetheme">
{foreach from=$themelist item=v}
    {if is_dir("./themes/$v") && preg_match('/^([A-Z]|[a-z]|_)/',$v)}
        <option value="{$v}"{if $v eq $gcms.config.sitetheme} selected="selected"{/if}>{$v}</option>
    {/if}
{/foreach}
</select></td></tr>
<tr><td>WYSIWYG Editor</td><td>
{html_options name=editor values=$eOptions output=$eOptions selected=$gcms.config.editor}
</td></tr>
<tr><td>Image Upload Dir</td><td><input type="text" name="imagedir" id="imageuploaddir" value="{$gcms.config.imagedir|default:$smarty.server.DOCUMENT_ROOT}" size="64" maxlength="255" /></td></tr>
<tr><td>File Upload Dir</td><td><input type="text" name="filedir" id="fileuploaddir" value="{$gcms.config.filedir|default:$smarty.server.DOCUMENT_ROOT}" size="64" maxlength="255" /></td></tr>
<tr><td colspan="2"><hr /></td></tr>
<tr><td colspan="2"><h4>Optional Extra Meta Tags</h4></td></tr>
<tr><td>Name <input type="text" name="meta1name" value="{$gcms.config.meta1name}" size="16" maxlength="255" /></td><td>Content <input type="text" name="meta1value" value="{$gcms.config.meta1value}" size="64" maxlength="255" /></td></tr>
<tr><td>Name <input type="text" name="meta2name" value="{$gcms.config.meta2name}" size="16" maxlength="255" /></td><td>Content <input type="text" name="meta2value" value="{$gcms.config.meta2value}" size="64" maxlength="255" /></td></tr>
<tr><td>Name <input type="text" name="meta3name" value="{$gcms.config.meta3name}" size="16" maxlength="255" /></td><td>Content <input type="text" name="meta3value" value="{$gcms.config.meta3value}" size="64" maxlength="255" /></td></tr>
<tr><td colspan="2"><hr /></td></tr>
<tr><td colspan="2"><h4>News Import Engine</h4></td></tr>
<tr><td colspan="2">
  <select name="import_engine" id="import-engine">
    {html_options options=$importOptions selected=$gcms.config.engine}
  </select>
</td></tr>
</table>
<div id="engine-ajax-feed">
<table cellspacing="10" border="0">
  <tr><td>RSS/Atom Feed URL</td><td><input type="text" name="googlefeed" value="{$gcms.config.googlefeed}" class="input-xlarge" maxlength="255" /></td></tr>
</table>
</div>
<div id="engine-ajax-gplus">
<table cellspacing="10" border="0">
  <tr><td colspan="2">To use Google+ you must activate your free API key <a href="https://code.google.com/apis/console#access" target="_blank">here</a>.  You can find your Google+ user id by logging in and selecting 'Profile'.  Your id will be the large number in your browsers address bar.</td></tr>
  <tr><td>Google+ API Key</td><td><input type="text" name="plusapikey" value="{$gcms.config.plusapikey}" class="input-xlarge" maxlength="255" /></td></tr>
  <tr><td>Google+ User Id</td><td><input type="text" name="plusid" value="{$gcms.config.plusid}" class="input-xlarge" maxlength="255" /></td></tr>
  <tr><td colspan="2">{html_toggle name="plusmyposts" selected=$gcms.config.plusmyposts labels=false} Include My Personal Public Posts</td></tr>
</table>
</div>
<table cellspacing="10" border="0">
{*
<tr><td colspan="2"><hr /></td></tr>
<tr><td>phpBB3 Dir</td><td><input type="text" name="phpbbdir" id="phpbbpath" value="{$config.forumdir}" size="64" onkeyup="popTopic(this.value)" onchange="popTopic(this.value)" /></td></tr>
<tr><td>phpBB3 Forum</td><td><select name="phpbbtopic" id="phpbbtopic"></select></td></tr>
<tr><td>phpBB3 Thread Intro</td><td><textarea name="threadintro" cols="40">{$config.threadintro}</textarea><pre>%u = URL, %t = Title, %c = Content</pre></td></tr>
*}
<tr><td colspan="2"><hr /></td></tr>
<tr><td colspan="2"><h4>reCAPTCHA</h4>To use reCAPTCHA you must register for a free key set at <a href="http://www.google.com/recaptcha" target="_blank">Google</a>.</td></tr>
<tr><td>Public Key</td><td><input type="text" name="publickey" value="{$gcms.config.republickey}" class="input-xlarge"></td></tr>
<tr><td>Private Key</td><td><input type="text" name="privatekey" value="{$gcms.config.reprivatekey}" class="input-xlarge"></td></tr>
<tr><td colspan="2"><hr /></td></tr>
<tr><td colspan="2"><input type="submit" name="configsubmit" value="Save" class="btn" /> <input type="submit" name="cancel" value="Cancel" class="btn" /></td></tr>
</table>
</form>
{/nocache}
