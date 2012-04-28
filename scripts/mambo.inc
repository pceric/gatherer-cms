<?php
	// Import our news feeds
	echo "Importing news feeds...";
	$rs = $import_db->query("SELECT * FROM mos_content WHERE sectionid = 1");
	$all = $rs->fetchAll();
	foreach($all as $item) {
		$data = array(
			'title' => $item['title'],
			'content' => $item['introtext'].$item['fulltext'],
			'tags' => $item['metakey'],
			'pubdate' => $item['created'],
			'moddate' => $item['modified'],
			'published' => $item['state'],
			'hits' => $item['hits']
		);
		$db->insert('news', $data);
	}
	echo "Done!\n<br />";
	
	// Import our articles
	echo "Importing articles...";
	$rs2 = $import_db->query("SELECT * FROM mos_content WHERE sectionid = 0");
	$all = $rs2->fetchAll();
	foreach($all as $item) {
		$data = array(
			'title' => $item['title'],
			'content' => preg_replace('/\{mospagebreak(.*)title=(.*)\}/i', '<!--pagebreak title="$2"-->', $item['introtext']),
			'tags' => $item['metakey'],
			'pubdate' => $item['created'],
			'moddate' => $item['modified'],
			'published' => $item['state'],
			'hits' => $item['hits']
		);
		$db->insert('articles', $data);
	}
	echo "Done!\n<br />";
	
	// Import our links
	echo "Importing links...";
	$rs3 = $import_db->query("SELECT * FROM mos_weblinks");
	$all = $rs3->fetchAll();
	foreach($all as $item) {
		$data = array(
			'title' => $item['title'],
			'url' => $item['url'],
			'desc' => $item['description'],
			'hits' => $item['hits']
		);
		$db->insert('links', $data);
	}
	echo "Done!\n<br />";
?>
