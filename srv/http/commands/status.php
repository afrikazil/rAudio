<?php
require_once __DIR__.'/common.php';
function getStatus()
{
	$output = shell_exec('mpc -f "[[%artist%]----[%album%]----[%title%]----[%track%]----[%time%]----[%file%]----[%position%]----[%id%]----[%prio%]----[%mtime%]"');

	return parseMPCOutput($output);
}
