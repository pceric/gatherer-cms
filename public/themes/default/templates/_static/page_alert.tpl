{if isset($alert_msg)}
<div class="alert{if isset($alert_class)} alert-{$alert_class}{/if}">
  <a class="close" data-dismiss="alert" href="#">×</a>
  <ul class="unstyled">
  {foreach $alert_msg as $v}
    <li>{$v}</li>
  {foreachelse}
    <li>An unkown error has occurred.</li>
  {/foreach}
  </ul>
</div>
{/if}
