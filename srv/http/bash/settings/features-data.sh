#!/bin/bash

# shareddata:
#    [[ -L $dirshareddata ]]       = server rAudio
#    [[ -L $dirmpd ]]              = clients
#    grep -q -m1 ":$dirsd" /etc/fstab  = clients with server rAudio
#    [[ -e $filesharedip ]]        = server + clients

. /srv/http/bash/common.sh

spotifyredirect=https://rern.github.io/raudio/spotify

packageActive hostapd localbrowser mediamtx shairport-sync smb snapclient spotifyd upmpdcli

data+='
  "page"             : "features"
, "autoplay"         : '$( exists $dirsystem/autoplay )'
, "autoplayconf"     : '$( conf2json $dirsystem/autoplay.conf )'
, "camilladsp"       : '$( exists $dirsystem/camilladsp )'
, "camilladspconf"   : { "REFRESH": '$( getVar status_update_interval /srv/http/settings/camillagui/config/gui-config.yml )' }
, "equalizer"        : '$( exists $dirsystem/equalizer )'
, "hostname"         : "'$( hostname )'"
, "hostip"           : "'$( ipAddress )'"
, "httpd"            : '$( exists $dirmpdconf/httpd.conf )'
, "ipsub"            : "'$( ipSub )'"
, "latest"           : '$( exists $dirsystem/latest )'
, "lcd"              : '$( grep -E -q 'waveshare|tft35a' /boot/config.txt 2> /dev/null && echo true )'
, "login"            : '$( exists $dirsystem/login )'
, "lyricsembedded"   : '$( exists $dirsystem/lyricsembedded )'
, "multiraudio"      : '$( exists $dirsystem/multiraudio )'
, "multiraudioconf"  : '$( getContent $dirsystem/multiraudio.json )'
, "nfsconnected"     : '$( [[ $( ls /proc/fs/nfsd/clients 2> /dev/null ) ]] && echo true )'
, "nfsserver"        : '$( [[ -L $dirshareddata ]] && systemctl -q is-active nfs-server && echo true )'
, "nosound"          : '$( exists $dirshm/nosound )'
, "scrobble"         : '$( exists $dirsystem/scrobble )'
, "scrobbleconf"     : '$( conf2json scrobble.conf )'
, "scrobblekey"      : '$( exists $dirsystem/scrobblekey )'
, "shareddata"       : '$( [[ -L $dirmpd ]] && echo true )'
, "state"            : "'$( getVar state $dirshm/status )'"
, "stoptimer"        : '$( exists $dirshm/stoptimer )'
, "stoptimerconf"    : '$( conf2json stoptimer.conf )
if [[ -e /usr/bin/hostapd ]]; then
	ip=$( grep router /etc/dnsmasq.conf | cut -d, -f2 )
	wpa_passphrase=$( getVar wpa_passphrase /etc/hostapd/hostapd.conf )
	data+='
, "hostapd"          : '$hostapd'
, "hostapdconf"      : { "IP": "'$ip'", "PASSPHRASE": "'$wpa_passphrase'" }
, "wlan"             : '$( lsmod | grep -q -m1 brcmfmac && echo true )'
, "wlanconnected"    : '$( ip r | grep -q -m1 "^default.*wlan0" && echo true )
fi
[[ -e /usr/bin/shairport-sync ]] && data+='
, "shairport-sync"   : '$shairportsync
[[ -e /usr/bin/snapserver ]] && data+='
, "snapclientactive" : '$snapclient'
, "snapserver"       : '$( exists $dirmpdconf/snapserver.conf )'
, "snapserveractive" : '$( [[ -e $dirshm/clientip ]] || ( [[ -e $dirsystem/snapclientserver ]] && systemctl -q is-active snapclient ) && echo true )'
, "snapclient"       : '$( exists $dirsystem/snapclient )'
, "snapclientconf"   : { "LATENCY": '$( grep latency /etc/default/snapclient | tr -d -c 0-9 )' }'
[[ -e /usr/bin/spotifyd ]] && data+='
, "spotifyd"         : '$spotifyd'
, "spotifyredirect"  : "'$spotifyredirect'"
, "spotifytoken"     : '$( grep -q -m1 refreshtoken $dirsystem/spotify 2> /dev/null && echo true )
[[ -e /usr/bin/upmpdcli ]] && data+='
, "upmpdcli"         : '$upmpdcli'
, "upmpdcliconf"     : { "OWNQUEUE": '$( grep -q -m1 'ownqueue = 1' /etc/upmpdcli.conf && echo true || echo false )' }'
if [[ -e /etc/systemd/system/localbrowser.service ]]; then
	if [[ ! -e /tmp/localbrowser.conf ]]; then
		[[ -e $dirsystem/localbrowser.conf ]] && cp $dirsystem/localbrowser.conf /tmp || touch /tmp/localbrowser.conf
	fi
	data+='
, "brightness"       : '$( getContent /sys/class/backlight/rpi_backlight/brightness )'
, "localbrowser"     : '$localbrowser'
, "localbrowserconf" : '$( conf2json $dirsystem/localbrowser.conf )
fi
if [[ -e /usr/bin/smbd ]]; then
	file=/etc/samba/smb.conf
	sed -n '/\[SD]/,/^\[/ p' $file | grep -q 'read only = no' && sd=true || sd=false
	sed -n '/\[USB]/,/^\[/ p' $file | grep -q 'read only = no' && usb=true || usb=false
	smbconf='{ "SD": '$sd', "USB": '$usb' }'
	data+='
, "smb"              : '$smb'
, "smbconf"          : '$smbconf
fi
if [[ -e /usr/bin/mediamtx ]]; then
	timeout 1 rtl_test -t &> /dev/null && dabdevice=true || systemctl disable --now mediamtx
	data+='
, "dabdevice"        : '$dabdevice'
, "dabradio"         : '$mediamtx
fi
data2json "$data" $1
