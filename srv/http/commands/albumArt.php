<?php
require_once __DIR__.'/config.php';

function saveCurrentAlbumArt(): array
{
	$filePath = trim(shell_exec('mpc --format %file% current'));

	// Команда для получения обложки
	$command = "mpc albumart \"$filePath\"";

	// Открываем процесс с дескрипторами
	$descriptorspec = [
		1 => ['pipe', 'w'], // stdout
		2 => ['pipe', 'w']  // stderr
	];

	$process = proc_open($command, $descriptorspec, $pipes);
	if (!is_resource($process)) {
		return ['error' => 'Не удалось запустить команду mpc albumart'];
	}

	// Читаем stdout (бинарные данные)
	$binaryData = stream_get_contents($pipes[1]);
	fclose($pipes[1]);

	// Читаем stderr (ошибки)
	$errorData = stream_get_contents($pipes[2]);
	fclose($pipes[2]);

	// Завершаем процесс
	$returnCode = proc_close($process);

	// Проверка ошибок
	if ($returnCode !== 0 || empty($binaryData)) {
		return ['error' => $errorData ?: 'Обложка не найдена', 'path' => DEFAULT_COVER_ART];
	}

	// Сохраняем данные в файл
	if (file_put_contents($_SERVER['DOCUMENT_ROOT'].COVER_ART, $binaryData)) {
		return ['success' => true, 'path' => COVER_ART];
	} else {
		return ['error' => 'Не удалось сохранить файл', 'path' => DEFAULT_COVER_ART];
	}
}

saveCurrentAlbumArt();

