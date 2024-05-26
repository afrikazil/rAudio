<?php
$sudo         = '/usr/bin/sudo ';
$sudobin      = $sudo.'/usr/bin/';
$sudosettings = $sudo.'/srv/http/bash/settings/';
$sudobash     = $sudo.'/srv/http/bash/';
$dirshm       = '/srv/http/data/shm/';
$dirsystem    = '/srv/http/data/system/';

switch( $_POST[ 'cmd' ] ) {

case 'bash':
	$filesh    = $_POST[ 'filesh' ];
	$args      = $_POST[ 'args' ] ?? '';
	if ( $args ) {
		$multiline = implode( "\n", $args );                               // array to multiline
		$multiline = escape( $multiline );                                 // escape multiline
		$result    = shell_exec( $sudobash.$filesh.' "'.$multiline.'"' );  // multiline > bash
	} else {
		$result    = shell_exec( $sudobash.$filesh );
	}
	echo rtrim( $result );
	break;
case 'camilla': // from camilla.js (formdata)
	fileUploadSave( '/srv/http/data/camilladsp/'.$_POST[ 'dir' ].'/'.$_FILES[ 'file' ][ 'name' ] );
	exec( $sudosettings.'camilla-data.sh pushrefresh', $output, $result );
	break;
case 'datarestore': // from system.js (formdata)
	fileUploadSave( $dirshm.'backup.gz' );
	$libraryonly = $_POST[ 'libraryonly' ] ?? '';
	exec( $sudosettings.'system-datarestore.sh '.$libraryonly, $output, $result );
	if ( $result != 0 ) echo 'Restore failed';
	break;
case 'giftype': // from common.js (formdata)
	$tmpfile  = $_FILES[ 'file' ][ 'tmp_name' ];
	$animated = exec( $sudobin.'gifsicle -I '.$tmpfile.' | grep -q -m1 "image #1" && echo 1 || echo 0' );
	echo $animated;
	if ( $animated ) move_uploaded_file( $tmpfile, $dirshm.'local/tmp.gif' );
	break;
case 'imagereplace': // from function.js - imageReplace()
	$imagefile = $_POST[ 'imagefile' ];
	$type      = $_POST[ 'type' ];
	if ( $type === 'coverart' && ! is_writable( dirname( $imagefile ) ) ) exit( '-1' );
//----------------------------------------------------------------------------------
	$bookmarkname = $_POST[ 'bookmarkname' ] ?? '';
	$imagedata    = $_POST[ 'imagedata' ];
	$jpg          = substr( $imagedata, 0, 4 ) === 'data'; // animated gif passed as already uploaded tmp/file
	if ( $jpg ) {
		$tmpfile = $dirshm.'local/binary';
		$base64  = preg_replace( '/^.*,/', '', $imagedata ); // data:imgae/jpeg;base64,... > ...
		file_put_contents( $tmpfile, base64_decode( $base64 ) );
	} else {
		$tmpfile = $imagedata;
	}
	$sh           = [ $type, $tmpfile, $imagefile, $bookmarkname ];
	$multiline    = implode( "\n", $sh );
	$multiline    = escape( $multiline );
	shell_exec( $sudobash.'cmd-coverartsave.sh "'.$multiline.'"' );
	break;
case 'login': // from features.js - imageReplace()
	$file = $dirsystem.'login';
	if ( file_exists( $file )  && ! password_verify( $_POST[ 'password' ], file_get_contents( $file ) ) ) exit( '-1' );
//----------------------------------------------------------------------------------
	if ( isset( $_POST[ 'disable' ] ) ) {
		unlink( $file );
		exec( $sudosettings.'features.sh logindisable' );
		exit;
//----------------------------------------------------------------------------------
	}
	$pwdnew = $_POST[ 'pwdnew' ] ?? '';
	if ( $pwdnew ) {
		$hash = password_hash( $pwdnew, PASSWORD_BCRYPT, [ 'cost' => 12 ] );
		file_put_contents( $file, $hash );
		exec( $sudosettings.'features.sh login' );
	} else {
		session_start();
		$_SESSION[ 'login' ] = 1;
	}
	break;
case 'logout':
	session_start();
	session_destroy();
	break;
case 'timezonelist':
	$list   = timezone_identifiers_list();
	$option = '<option value="auto">Auto</option>';
	foreach( $list as $key => $zone ) {
		$datetime = new DateTime( 'now', new DateTimeZone( $zone ) );
		$offset   = $datetime->format( 'P' );
		$zonename = preg_replace( [ '/_/', '/\//' ], [ ' ', ' <gr>&middot;</gr> ' ], $zone );
		$option  .= '<option value="'.$zone.'">'.$zonename.'&ensp;'.$offset.'</option>';
	}
	echo $option;
	break;
	
}

function escape( $string ) {
	return preg_replace( '/(["`])/', '\\\\\1', $string ); // \1 inside function - $1 normal 
}
function fileUploadSave( $filepath ) {
	if ( $_FILES[ 'file' ][ 'error' ] != UPLOAD_ERR_OK ) exit( '-1' );
//----------------------------------------------------------------------------------
	move_uploaded_file( $_FILES[ 'file' ][ 'tmp_name' ], $filepath );
}
