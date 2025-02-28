<?php

$cmd         = $_POST[ 'cmd' ] ?? $argv[ 1 ];
$sudo        = '/usr/bin/sudo ';
$dirbash     = $sudo.'/srv/http/bash/';
$dirsettings = $dirbash.'settings/';
$dirdata     = '/srv/http/data/';
$dirshm      = $dirdata.'shm/';

function getSystemStatus(?string $args = null ) {
	global $dirbash;
	$cmd = $dirbash.'status.sh';

	$cmd   .= isset( $_GET[ 'args' ] ) ? ' "'.escape( implode( "\n", $args ) ).'"' : '';
	$result = shell_exec( $cmd );
	return rtrim( $result );
}

$command = str_replace('/api/', '', $_SERVER['REQUEST_URI']);
if ($command == 'status') {
	header('Content-Type: application/json');
	echo getSystemStatus();
	return;
}
//
//if ($command == 'command') {
//
//	header('Content-Type: application/json');
//	// Читаем тело запроса
//	$json = file_get_contents('php://input');
//
//// Декодируем JSON в ассоциативный массив
//	$data = json_decode($json, true);
//	$params = $data['params'];
//
//	$response = sendCommand($data['command']);
//	$cover = saveCurrentAlbumArt();
//	echo json_encode(
//		array_merge(
//			$response,
//			[
//				'cover' => $cover['path'],
//			],
//		),
//		JSON_PRETTY_PRINT
//	);
//	return;
//}
