{nocache}
{if !empty($error_msg)}<div class="alert alert-block alert-error">{$error_msg}</div>{/if}
<form action="{$smarty.server.REQUEST_URI}" method="post" class="form-horizontal">
  <fieldset>
    <div class="control-group">
      <label class="control-label">Username</label>
      <div class="controls"><input type="text" name="login" autofocus /></div>
    </div>
    <div class="control-group">
      <label class="control-label">Password</label>
      <div class="controls"><input type="password" name="pass" /></div>
    </div>
    <div class="control-group">
      <div class="controls"><input type="submit" class="btn" name="submit" value="Log In" /></div>
    </div>
  </fieldset>
</form>
{/nocache}
