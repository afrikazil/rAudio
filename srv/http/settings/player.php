<div id="divmpd" class="section">
<?php
htmlHead( [ //////////////////////////////////
	  'title'  => 'Music Player Daemon'
	, 'status' => 'mpd'
	, 'button' => [ 'playback' => 'play' ]
] );
?>
	<div class="col-l text gr">
		Version
		<br>Database
	</div>
	<div class="col-r text">
		<div id="statusvalue"></div>
	</div>
	<div style="clear:both"></div>
	<div class="help-block hide">
<a href="https://www.musicpd.org/">MPD</a> - Music Player Daemon is a flexible, powerful, server-side application for playing music.
Through plugins and libraries it can play a variety of sound files while being controlled by its network protocol.
</div>
</div>
<?php
if ( !file_exists( '/srv/http/data/shm/nosound' ) || file_exists( '/srv/http/data/shm/btreceiver' ) ) {
// ----------------------------------------------------------------------------------
$head = [ //////////////////////////////////
	  'title'  => 'Output'
	, 'status' => 'asound'
];
$body = [
	[
			  'label'       => 'Bluetooth'
			, 'sublabel'    => 'bluetoothctl'
			, 'icon'        => 'bluetooth'
			, 'id'          => 'btreceiver'
			, 'status'      => 'bluetooth'
			, 'input'       => '<select id="btaplayname"></select>'
			, 'setting'     => 'custom'
			, 'settingicon' => 'volume'
			, 'help'        => <<< HTML
I-volume-I Volume setting and control:
 • Player: Should be set at 0dB
 • Playback: Should be set at 100%
 • Use device volume to control level
HTML
	]
	, [
		  'label'   => 'Device'
		, 'id'      => 'audiooutput'
		, 'input'   => '<select id="audiooutput"></select>'
		, 'setting' => false
		, 'help'    => <<< HTML
HDMI device available only when connected before boot.
HTML
	]
	, [
		  'label'       => 'Mixer Device'
		, 'id'          => 'hwmixer'
		, 'input'       => '<select id="hwmixer"></select>'
		, 'setting'     => 'custom'
		, 'settingicon' => 'volume'
		, 'help'  => <<< HTML
Available hardware mixers of current device.
I-volume-I Control current mixer device.
HTML
	]
	, [
		  'label'   => 'Volume Control'
		, 'id'      => 'mixertype'
		, 'input'   => '<select id="mixertype"></select>'
		, 'setting' => false
		, 'help'    => <<< HTML
Volume control for each device.
 • <code>None / 0dB</code> Best sound quality. (Use amplifier volume)
 • <code>Mixer device</code> Good and convenient. (Device hardware volume)
 • <code>MPD software</code> Software volume.
HTML
	]
];
htmlSection( $head, $body );
$head = [ 'title' => 'Bit-Perfect' ]; //////////////////////////////////
$body = [
	[
		  'label'       => 'No Volume'
		, 'id'          => 'novolume'
		, 'setting'     => 'custom'
		, 'settingicon' => false
		, 'help'        => <<< HTML
Disable all manipulations for bit-perfect stream from MPD to DAC output.
 • No changes in data stream until it reaches amplifier volume control.
 • Mixer device volume: 0dB (No amplitude manipulations)
 • Volume Control: <code>None / 0db</code> (Hidden volume in Playback)
 • Signal Processors: Disabled
 • Crossfade, Normalization and Replay Gain: Disabled
HTML
	]
	, [
		  'label'   => 'DSD over PCM'
		, 'id'      => 'dop'
		, 'setting' => false
		, 'help'    => <<< HTML
For DSD-capable devices without drivers dedicated for native DSD.
 • Enable if there's static/no sound from the DAC which means not support as native DSD.
 • DoP will repack 16bit DSD stream into 24bit PCM frames and transmit to the DAC. 
 • PCM frames will be reassembled back to original DSD stream, COMPLETELY UNCHANGED, with expense of double bandwith.
 • On-board audio and non-DSD devices will always get DSD converted to PCM stream, no bit-perfect
HTML
	]
];
htmlSection( $head, $body, 'bitperfect' );
$head = [ 'title' => 'Volume' ]; //////////////////////////////////
$body = [
	[	  'label'   => 'Crossfade'
		, 'id'      => 'crossfade'
		, 'help'    => <<< HTML
<code>mpc crossfade N</code>
Fade-out to fade-in between songs.
HTML
	]
	, [
		  'label'   => 'Normalization'
		, 'id'      => 'normalization'
		, 'setting' => false
		, 'help'    => <<< HTML
<code>volume_normalization "yes"</code>
Normalize the volume level of songs as they play.
HTML
	] 
	, [
		  'label'   => 'Replay Gain'
		, 'id'      => 'replaygain'
		, 'help'    => <<< HTML
<code>replaygain "N"</code>
Set gain control to setting in replaygain tag.
Currently support: FLAC, Ogg Vorbis, Musepack, and MP3 (through ID3v2 ReplayGain tags, not APEv2)
HTML
	]
];
htmlSection( $head, $body );
// ----------------------------------------------------------------------------------
}
$head = [ 'title' => 'Options' ]; //////////////////////////////////
$body = [
	[
		  'label'    => 'Buffer - Audio'
		, 'id'       => 'buffer'
		, 'sublabel' => 'custom size'
		, 'help'     => <<< HTML
<code>audio_buffer_size "kB"</code>
Default buffer size: 4096 kB (24 seconds of CD-quality audio)
Increase to fix intermittent audio.
HTML
	]
	, [
		  'label'    => 'Buffer - Output'
		, 'id'       => 'bufferoutput'
		, 'sublabel' => 'custom size'
		, 'help'     => <<< HTML
<code>max_output_buffer_size "kB"</code>
Default buffer size: 8192 kB
Increase to fix missing Album list with large Library.
HTML
	]
	, [
		  'label'    => 'FFmpeg'
		, 'id'       => 'ffmpeg'
		, 'sublabel' => 'decoder plugin'
		, 'setting'  => false
		, 'disabled' => '<wh>DAB Radio'.i( 'dabradio' ).'is currently enabled.'
		, 'help'     => <<< HTML
<code>enable "yes"</code>
Should be disabled if not used for faster Library update.
Decoder for audio filetypes: I-help filetype-I
<div id="divfiletype" class="hide" style="margin-left: 20px"></div>
HTML
	]
	, [
		  'label'   => 'Library Auto Update'
		, 'id'      => 'autoupdate'
		, 'setting' => false
		, 'help'    => <<< HTML
<code>auto_update "yes"</code>
Automatic update MPD database when files changed.
HTML
	]
	, [
		  'label'    => 'SoXR Resampler'
		, 'id'       => 'soxr'
		, 'sublabel' => 'custom settings'
		, 'help'     => <<< HTML
<code>quality "custom"</code>
Default quality: very high

<wh>SoX Resampler custom settings:</wh>
 • Precision - Conversion precision (20 = HQ)
 • Phase Response (50 = Linear)
 • Passband End - 0dB point bandwidth to preserve (100 = Nyquist)
 • Stopband Begin - Aliasing/imaging control
 • Attenuation - Lowers the source to prevent clipping
 • Flags - Extra settings:
	&nbsp; 0 - Rolloff - small (<= 0.01 dB)
	&nbsp; 1 - Rolloff - medium (<= 0.35 dB)
	&nbsp; 2 - Rolloff - none - For Chebyshev bandwidth
	&nbsp; 8 - High precision - Increase irrational ratio accuracy
	16 - Double precision - even if Precision <= 20
	32 - Variable rate resampling
HTML
	]
	, [
		  'label'   => "User's Configurations"
		, 'id'      => 'custom'
		, 'help'    => <<< HTML
Insert custom configurations into <code>/etc/mpd.conf</code>.
HTML
	]
];
htmlSection( $head, $body );

echo '
<div id="divlists" class="section">
	<heading><span class="headtitle">Lists</span></heading>';
htmlHead( [
	  'title'   => 'Ignored Album'
	, 'id'      => 'albumignore'
	, 'subhead' => true
	, 'status'  => 'albumignore'
	, 'help'    => <<< HTML
List of albums excluded from Album page.
To restore:
 • Edit <code>/srv/http/data/mpd/albumignore</code>
 • Remove albums to restore
 • Update Library
HTML
] );
htmlHead( [
	  'title'   => 'Ignored Directory'
	, 'id'      => 'mpdignore'
	, 'subhead' => true
	, 'status'  => 'mpdignore'
	, 'help'    => <<< HTML
List of <code>.mpdignore</code> files contain directories excluded from database.
To restore:
• Edit <code>.../.mpdignore</code>
• Remove directories to restore
• Update Library
HTML
] );
htmlHead( [
	  'title'   => 'Non UTF-8 Files'
	, 'id'      => 'nonutf8'
	, 'subhead' => true
	, 'status'  => 'nonutf8'
	, 'help'    => <<< HTML
List of files with metadata is not UTF-8 encoding which must be corrected.
HTML
] );
echo '
</div>';
