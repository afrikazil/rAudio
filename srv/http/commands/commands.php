<?php
include_once __DIR__ . "/common.php";

function sendCommand(string $command, ?array $params = null): array
{
	$output = (string) shell_exec('mpc ' . $command. ' -f "[[%artist%]----[%album%]----[%title%]----[%track%]----[%time%]----[%file%]----[%position%]----[%id%]----[%prio%]----[%mtime%]"');

	return parseMPCOutput($output);
}
