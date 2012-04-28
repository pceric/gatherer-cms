<ul>
  {foreach $data as $post}
  <li{if $post@iteration == 1} class="first"{/if}>
    <a href="{$view->url(['module' => 'news', 'id' => $post.id],null,true)}">{$post.title|escape}</a></li>
  {/foreach}
</ul>
