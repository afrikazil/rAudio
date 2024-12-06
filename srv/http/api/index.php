<?php
include_once __DIR__ . "/../commands/albumArt.php";
include_once __DIR__ . "/../commands/status.php";
include_once __DIR__ . "/../commands/commands.php";

$command = str_replace('/api/', '', $_SERVER['REQUEST_URI']);

if ($command == 'status') {
	$cover = saveCurrentAlbumArt();
	$response = getStatus();
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

if ($command == 'command') {

	header('Content-Type: application/json');
	// Читаем тело запроса
	$json = file_get_contents('php://input');

// Декодируем JSON в ассоциативный массив
	$data = json_decode($json, true);
	$response = sendCommand($data['command']);
	$cover = saveCurrentAlbumArt();
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
