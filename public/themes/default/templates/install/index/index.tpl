{nocache}
Thanks for installing GCMS!  Please set the administrative username and password.
<form method="post" class="form-horizontal">
  <fieldset>
  <table>
  <tr><td colspan="2">{if !empty($error_msg)}<div class="alert alert-error">{$error_msg}</div>{/if}</td></tr>
  <tr><td>Username:</td><td><input type="text" name="username" value="{$smarty.post.username|default:''}" class="input-xlarge" /></td></tr>
  <tr><td>Password:</td><td><input type="password" name="password" class="input-xlarge" /></td></tr>
  <tr><td>Password again:</td><td><input type="password" name="passcheck" class="input-xlarge" /></td></tr>
  <tr><td colspan="2"><input type="submit" name="submit" value="Finish Setup" class="btn" /></td></tr>
  </table>
  </fieldset>
</form>
{/nocache}
