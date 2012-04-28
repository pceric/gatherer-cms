{$view->doctype()}
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>{$title nocache}</title>
</head>
<body onload="window.print()">
{nocache}
<h1>{$title}</h1>
<h5>Published by {$gcms.config.siteauthor} on {$published|date_format:"%c"}.
{if $modified}<br />Modified on {$modified|date_format:"%c"}.{/if}</h5>
<p>{$content}</p>
{/nocache}
</body>
</html>
