<?php
DEFINE('NOW', time());
DEFINE('SECRET', 'chewbacca');

// setlocale(LC_TIME, "de_DE");
// date_default_timezone_set('Europe/Berlin');

try {
	$db = new PDO('sqlite:'.ROOT.DS.'posts.db');
	$db->exec("CREATE TABLE IF NOT EXISTS posts (
		id integer PRIMARY KEY NOT NULL,
		post_content TEXT,
		post_timestamp integer
	);");
} catch(PDOException $e) {
	print 'Exception : '.$e->getMessage();
	die('database error');
}

function db_insert($message, $timestamp=NOW) {
	global $db;
	if (strlen($message)===0) return;
	if(empty($db)) return false;
	$statement = $db->prepare('INSERT INTO posts (post_content, post_timestamp) VALUES (:post_content, :post_timestamp)');
	$statement->bindParam(':post_content', $message, PDO::PARAM_STR);
	$statement->bindParam(':post_timestamp', $timestamp, PDO::PARAM_INT);
	$statement->execute();
	return $db->lastInsertId();
}

function db_delete_latest() {
	global $db;
	if(empty($db)) return false;
	$statement = $db->prepare('DELETE FROM posts ORDER BY id DESC LIMIT 1');
	$statement->execute();
	return true;
}

function db_select_posts($from=NOW, $amount=10, $sort='desc', $page=1) {
	global $db;
	if(empty($db)) return false;
	if($sort !== 'desc') $sort = 'asc';
    $postsperpage = 20;
	$offset = ($page-1)*$postsperpage;
	$statement = $db->prepare('SELECT * FROM posts WHERE post_timestamp < :post_timestamp ORDER BY id '.$sort.' LIMIT :limit OFFSET :page');
	$statement->bindParam(':post_timestamp', $from, PDO::PARAM_INT);
	$statement->bindParam(':limit', $amount, PDO::PARAM_INT);
	$statement->bindParam(':page', $offset, PDO::PARAM_INT);
	$statement->execute();
	$rows = $statement->fetchAll(PDO::FETCH_ASSOC);
	return (!empty($rows)) ? $rows : false;
}

/////////////// HTTP API

if (isset($_POST["add"]) ) {
    print $_POST["secret"] === SECRET?db_insert($_POST["add"]):"access denied";
} elseif (isset($_POST["delete"]) ) {
    print $_POST["secret"] === SECRET? db_delete_latest():"access denied";
}
