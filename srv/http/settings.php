<?php
include 'common.php';

echo '
<div class="head">'.i( $icon.' page-icon' ).'<span class="title">'.$title.'</span>'.i( 'close close', 'close' );
if ( $addon_guide ) {
	echo '</div>';
} else {
	echo i( 'help helphead' ).i( 'gear' ).'</div>
<div class="container '.$page.' hide" tabindex="-1">
';
}
if ( $addonsprogress ) {
	include 'settings/'.$page.'.php';
	exit;
//----------------------------------------------------------------------------------
}

$prefix = '';
$htmlbar = '';
if ( $camilla ) {
	$tabs   = [ 'Filters', 'Mixers', 'Processors', 'Pipeline', 'Devices' ];
	$prefix = 'tab';
} else if ( $guide ) {
	$tabs   = [ 'Library', 'Playback', 'Playlist', 'Settings' ];
} else {
	$tabs   = [ 'Features', 'Player', 'Networks', 'System', 'Addons' ];
}
foreach ( $tabs as $tab ) {
	$id      = strtolower( $tab );
	$htmlbar.= '<div id="'.$prefix.$id.'">'.i( $id ).' <a>'.$tab.'</a></div>';
}
if ( $guide ) {
	echo '<img id="guideimg" src="/assets/img/guide/1.jpg'.$hash.'">';
	$htmlbar.= i( 'back', 'guideprev' ).i( 'arrow-right', 'guidenext' );
} else if ( ! $addons ) {
	include 'settings/function.php';
	include 'settings/'.$page.'.php'; // addons: by addons.js
}
htmlBottom();
