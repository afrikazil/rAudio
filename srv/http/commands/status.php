<?php

// Функция для запуска команды и обработки её вывода
function parseMPCOutput() {
	// Запускаем команду mpc и получаем её вывод
	$output = shell_exec('mpc -f "[[%artist%]----[%album%]----[%title%]----[%track%]----[%time%]----[%file%]----[%position%]----[%id%]----[%prio%]----[%mtime%]"');

	if (!$output) {
		return [
			'error' => 'Failed to execute mpc command'
		];
	}

	// Разделяем строки
	$lines = explode("\n", $output);

	// Обрабатываем первую строку (информация о текущем треке)
	$trackInfo = explode("----", $lines[0]);
	$artist = $trackInfo[0] ?? '';
	$album = $trackInfo[1] ?? '';
	$title = $trackInfo[2] ?? '';
	$track = intval($trackInfo[3] ?? 0);
	$time = $trackInfo[4] ?? '0:00';
	$file = $trackInfo[5] ?? '';
	$currentPosition = intval($trackInfo[6] ?? 0);

	// Переводим время из формата MM:SS в секунды
	$totalTime = array_reduce(explode(':', $time), function($carry, $item) {
		return $carry * 60 + intval($item);
	}, 0);

	// Обрабатываем вторую строку (статус плеера)
	$statusLine = $lines[1] ?? '';
	preg_match('/#(\d+)\/(\d+)/', $statusLine, $playlistMatch);
	preg_match('/(\d+):(\d+)\/(\d+):(\d+)/', $statusLine, $timeMatch);
	preg_match('/volume:\s*(n\/a|\d+).+repeat:\s*(on|off).+random:\s*(on|off).+single:\s*(on|off)/', $statusLine, $settingsMatch);

	// Определяем состояние воспроизведения
	$playbackStatus = 'stop'; // Значение по умолчанию
	if (strpos($statusLine, '[playing]') !== false) {
		$playbackStatus = 'play';
	} elseif (strpos($statusLine, '[paused]') !== false) {
		$playbackStatus = 'pause';
	}


	$playListLength = intval($playlistMatch[2] ?? 0);
	$currentTime = isset($timeMatch[1], $timeMatch[2]) ? (intval($timeMatch[1]) * 60 + intval($timeMatch[2])) : 0;
	$repeat = isset($settingsMatch[2]) && $settingsMatch[2] === 'on' ? 2 : 0;
	$repeat = isset($settingsMatch[4]) && $settingsMatch[4] === 'on' ? 1 : $repeat;
	$volume = $settingsMatch[1] === 'n/a' ? null : intval($settingsMatch[1] ?? 100);

	return [
		'artist' => $artist,
		'title' => $title,
		'album' => $album,
		'track' => $track,
		'playListLength' => $playListLength,
		'volume' => $volume,
		'volumemax' => $volume === 100,
		'volumemute' => $volume === 0 ? 1 : 0,
		'repeat' => $repeat,
		'time' => $totalTime,
		'currentTime' => $currentTime,
		'file' => $file,
		'playbackStatus' => $playbackStatus,
	];
}
