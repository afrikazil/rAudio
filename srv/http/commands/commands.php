<?php
include_once __DIR__ . "/common.php";

function sendCommand(string $command, ?array $params = null): array
{

	shell_exec('mpc ' . $command);
	$output = shell_exec('mpc -f "[[%artist%]----[%album%]----[%title%]----[%track%]----[%time%]----[%file%]----[%position%]----[%id%]----[%prio%]----[%mtime%]"');

	return parseMPCOutput($output);
}
