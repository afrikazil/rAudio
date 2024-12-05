<?php
include_once __DIR__ . "/../commands/albumArt.php";

$command = str_replace('/api/', '', $_SERVER['REQUEST_URI']);

if ($command == 'status') {
	$cover = saveCurrentAlbumArt();
	echo json_encode([
		'cover' => $cover['path'],
	]);
	return;
}
