<h1>Contact</h1>
<div id="contact">
<form method="post" action="{$smarty.server.PHP_SELF}" class="form-horizontal">
  <fieldset>
    <input type="hidden" name="token" value="{$token}" />
    <div class="control-group">
      <label class="control-label">Name</label>
      <div class="controls"><input type="text" name="name" class="input-xlarge" maxlength="64" value="{$smarty.post.name|default:''}" /></div>
    </div>
    <div class="control-group">
      <label class="control-label">E-Mail</label>
      <div class="controls"><input type="text" name="email" class="input-xlarge" maxlength="64" value="{$smarty.post.email|default:''}" />{if isset($smarty.request.contact_submit) && !$valid->isValid($smarty.request.email)} <span class="label label-important">Valid E-Mail Required</span>{/if}</div>
    </div>
    <div class="control-group">
      <label class="control-label">Subject</label>
      <div class="controls"><input type="text" name="subject" class="input-xlarge" maxlength="64" value="{$smarty.post.subject|default:''}" /></div>
    </div>
    <div class="control-group">
      <label class="control-label">Message </label>
      <div class="controls"><textarea name="message" class="input-xxlarge">{$smarty.post.message|default:''}</textarea></div>
    </div>
    <div class="control-group">
      {$captcha|default:''}
    </div>
    <div class="control-group">
      <input type="submit" class="btn btn-primary" name="contact_submit" value="Send" />
    </div>
  </fieldset>
</form>
</div>

