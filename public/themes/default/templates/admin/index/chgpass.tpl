{nocache}
{if !empty($error)}
<div class="alert alert-error">
{$error}
</div>
{/if}
{/nocache}
<ul class="breadcrumb">
  <li><a href="{$view->url(['module' => 'admin'],null,true)}">Admin Home</a> <span class="divider">&gt;</span></li>
  <li class="active">Change Password</li>
</ul>
<h1>Change Password</h1>
<form method="post" class="form-horizontal">
  <fieldset>
    <div class="control-group">
      <label class="control-label">Current Password:</label>
      <div class="controls">
        <input type="password" name="oldpass" class="input-xlarge" />
      </div>
    </div>
    <div class="control-group">
      <label class="control-label">New Password:</label>
      <div class="controls">
        <input type="password" name="newpass" class="input-xlarge" />
      </div>
    </div>
    <div class="control-group">
      <label class="control-label">Confirm Password:</label>
      <div class="controls">
        <input type="password" name="conpass" class="input-xlarge" />
      </div>
    </div>
    <div class="control-group">
      <input type="submit" name="save" value="Save" class="btn" /> <input type="submit" name="cancel" value="Cancel" class="btn" />
    </div>
  </fieldset>
</form>
