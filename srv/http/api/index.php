<?php
include_once __DIR__ . "/../commands/albumArt.php";
include_once __DIR__ . "/../commands/status.php";

$command = str_replace('/api/', '', $_SERVER['REQUEST_URI']);

if ($command == 'status') {
	$cover = saveCurrentAlbumArt();
	$response = parseMPCOutput();
	header('Content-Type: application/json');
	echo json_encode(
		array_merge(
			$response,
			[
				'cover' => $cover['path'],
			],
		),
		JSON_PRETTY_PRINT
	);
	return;
}
