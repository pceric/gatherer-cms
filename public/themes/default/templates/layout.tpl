<!DOCTYPE html>
<html>
  <head>
    {$view->headMeta() nocache}
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    {$view->headTitle() nocache}
    {$view->headLink() nocache}
    <link href="{$view->baseUrl('/themes/default/css/bootstrap.min.css')}" rel="stylesheet">
    <link href="{$view->baseUrl('/themes/default/css/style.css')}" rel="stylesheet">
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
          <a class="brand" href="{$view->url(['module' => 'default'],null,true)}">{$gcms.config.sitename}</a>
          <ul class="nav">
            <li{if $gcms.param.module == 'default'} class="active"{/if}><a href="{$view->url(['module' => 'default'],null,true)}">Home</a></li>
            <li{if $gcms.param.module == 'archives'} class="active"{/if}><a href="{$view->url(['module' => 'archives'],null,true)}">Archives</a></li>
            <li{if $gcms.param.module == 'article' && $gcms.param.id|default:0 == 1} class="active"{/if}><a href="{$view->url(['module' => 'article', 'id' => 1],null,true)}">About</a></li>
            <li{if $gcms.param.module == 'contact'} class="active"{/if}><a href="{$view->url(['module' => 'contact'],null,true)}">Contact</a></li>
          </ul>
          {if $gcms.isAdmin}<ul class="nav pull-right"><li><a href="{$view->url(['module' => 'admin'],null,true)}">Admin</a></li></ul>{/if}
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
                  <form class="well form-search" onsubmit="return gcms.doSearch(this.s);">
                    <input type="text" name="s" class="search-query input-small" placeholder="Search..." />
                    <input type="submit" id="search-submit" class="btn" value="GO" />
                  </form>
                </div>
                <div id="sidebar-syndication">
                  <h2>Syndication</h2>
                    <ul>
                      <li><a href="{$view->baseUrl('feed.php?RSS')}"><img src="{$view->baseUrl('/themes/default/images/feed-icon-14x14.png')}" alt="RSS" />RSS</a></li>
                      <li><a href="{$view->baseUrl('feed.php')}"><img src="{$view->baseUrl('/themes/default/images/feed-icon-14x14.png')}" alt="ATOM" />ATOM</a></li>
                    </ul>
                </div>
                <div id="sidebar-navigation">
                  <h2>Navigation</h2>
                  {$view->navigation()->menu() nocache}
                </div>
                <div id="sidebar-posts">
                  <h2>My Recent Posts</h2>
                  {$view->myPosts() nocache}
                </div>
                <div id="sidebar-archives">
                  <h2>Archives</h2>
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
