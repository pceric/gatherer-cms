<?php
	// Import our news feeds
	echo "Importing news feeds...";
	$rs = $import_db->query("SELECT * FROM `node` WHERE type = 'story'");
	$all = $rs->fetchAll();
	foreach($all as $item) {
		$data = array(
			'title' => $item['title'],
			'content' => new Zend_Db_Expr("(SELECT body from `node_revisions` WHERE nid = $item[nid] AND vid = $item[vid])"),
			'pubdate' => new Zend_Db_Expr("FROM_UNIXTIME($item[created])"),
			'moddate' => new Zend_Db_Expr("FROM_UNIXTIME($item[changed])"),
			'published' => $item['status']
		);
		$db->insert('news', $data);
	}
	echo "Done!\n<br />";

	// Import our articles
	echo "Importing articles...";
	$rs2 = $import_db->query("SELECT * FROM `node` WHERE type = 'page'");
	$all = $rs2->fetchAll();
	foreach($all as $item) {
		$data = array(
			'title' => $item['title'],
			'content' => new Zend_Db_Expr("(SELECT body from `node_revisions` WHERE nid = $item[nid] AND vid = $item[vid])"),
			'pubdate' => new Zend_Db_Expr("FROM_UNIXTIME($item[created])"),
			'moddate' => new Zend_Db_Expr("FROM_UNIXTIME($item[changed])"),
			'published' => $item['status']
		);
		$db->insert('articles', $data);
	}
	echo "Done!\n<br />";
?>
