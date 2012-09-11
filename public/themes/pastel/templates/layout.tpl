<!DOCTYPE html>
<html>
  <head>
    {$view->headMeta() nocache}
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    {$view->headTitle() nocache}
    {$view->headLink() nocache}
    <link href="{$view->baseUrl('/themes/pastel/css/bootstrap.min.css')}" rel="stylesheet">
    <link href="{$view->baseUrl('/themes/pastel/css/style.css')}" rel="stylesheet">
    {$view->headScript() nocache}
    <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
  </head>
  <body>
    <div class="navbar navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container">
          {nocache}
          <a class="brand" href="{$view->url(['module' => 'default'],null,true)}">{$gcms.config.sitename}</a>
          <ul class="nav">
            <li{if $gcms.param.module == 'default'} class="active"{/if}><a href="{$view->url(['module' => 'default'],null,true)}">Home</a></li>
            <li{if $gcms.param.module == 'archives'} class="active"{/if}><a href="{$view->url(['module' => 'archives'],null,true)}">Archives</a></li>
            <li{if $gcms.param.module == 'article' && $gcms.param.id|default:0 == 1} class="active"{/if}><a href="{$view->url(['module' => 'article', 'id' => 1],null,true)}">About</a></li>
            <li{if $gcms.param.module == 'contact'} class="active"{/if}><a href="{$view->url(['module' => 'contact'],null,true)}">Contact</a></li>
          </ul>
          {if $gcms.isAdmin}<ul class="nav pull-right"><li><a href="{$view->url(['module' => 'admin'],null,true)}">Admin</a></li></ul>{/if}
          {/nocache}
        </div>
      </div>
    </div>

    <div class="container">
      <div class="row">
        <div class="span12">
          <div class="row">
            <div class="span8">
              <div id="content">{$view->layout()->content nocache}</div>
            </div>
            <div class="span3 offset1">
              <div id="sidebar">
                <div id="search">
                  <form class="well well-small form-search" onsubmit="return gcms.doSearch(this.s);">
                    <div class="input-append">
                      <input type="text" name="s" class="search-query input-small" placeholder="Search..." />
                      <button type="submit" class="btn" id="search-submit">GO</button>
                    </div>
                  </form>
                </div>
                <div id="sidebar-syndication">
                  <h3>Syndication</h3>
                    <ul>
                      <li><a href="{$view->baseUrl('feed.php?RSS')}"><img src="{$view->baseUrl('/themes/default/images/feed-icon-14x14.png')}" alt="RSS" />RSS</a></li>
                      <li><a href="{$view->baseUrl('feed.php')}"><img src="{$view->baseUrl('/themes/default/images/feed-icon-14x14.png')}" alt="ATOM" />ATOM</a></li>
                    </ul>
                </div>
                <div id="sidebar-navigation">
                  <h3>Navigation</h3>
                  {$view->navigation()->menu() nocache}
                </div>
                <div id="sidebar-posts">
                  <h3>My Recent Posts</h3>
                  {$view->myPosts() nocache}
                </div>
                <div id="sidebar-archives">
                  <h3>Archives</h3>
                  {$view->archives() nocache}
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div> <!-- /container -->
    <div class="row">
      <div id="footer">{include file="footer.tpl"}</div>
    </div>
    </body>
</html>
