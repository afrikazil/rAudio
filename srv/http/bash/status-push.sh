#!/bin/bash

. /srv/http/bash/common.sh

[[ -e $dirshm/usbdacadd ]] && exit
# --------------------------------------------------------------------
killProcess statuspush
echo $$ > $dirshm/pidstatuspush

if [[ $1 == statusradio ]]; then # from status-radio.sh
	state=play
	playing=1
else
	status=$( $dirbash/status.sh )
	statusnew=$( sed -E -n '/^, "Artist|^, "Album|^, "Composer|^, "elapsed|^, "file| *"player|^, "station"|^, "state|^, "Time|^, "timestamp|^, "Title|^, "webradio"/ {s/^,* *"//; s/" *: */=/; p}' <<< $status )
	echo "$statusnew" > $dirshm/statusnew
	statusprev=$( < $dirshm/status )
	compare='^Artist|^Title|^Album'
	[[ "$( grep -E "$compare" <<< $statusnew | sort )" != "$( grep -E "$compare" <<< $statusprev | sort )" ]] && trackchanged=1
	. <( echo "$statusnew" )
	[[ $state == play ]] && playing=1
	if [[ $webradio == true ]]; then
		[[ ! $trackchanged && $playing ]] && exit
# --------------------------------------------------------------------
	else
		compare='^state|^elapsed'
		[[ "$( grep -E "$compare" <<< $statusnew | sort )" != "$( grep -E "$compare" <<< $statusprev | sort )" ]] && statuschanged=1
		[[ ! $trackchanged && ! $statuschanged ]] && exit
# --------------------------------------------------------------------
	fi
########
	pushData mpdplayer "$status"
	if [[ -e $dirsystem/scrobble ]]; then
		cp -f $dirshm/status{,prev}
		timestampnew=$( grep ^timestamp <<< $statusnew | cut -d= -f2 )
	fi
	mv -f $dirshm/status{new,}
fi

if systemctl -q is-active localbrowser; then
	if grep -q onwhileplay=true $dirsystem/localbrowser.conf; then
		export DISPLAY=:0
		[[ $playing ]] && sudo xset -dpms || sudo xset +dpms
	fi
fi

if [[ -e $dirshm/clientip ]]; then
	serverip=$( ipAddress )
	[[ ! $status ]] && status=$( $dirbash/status.sh ) # $statusradio
	status=$( sed -E -e '1,/^, "single" *:/ d;/^, "icon" *:/ d; /^, "login" *:/ d; /^}/ d
					' -e '/^, "stationcover"|^, "coverart"/ s|(" *: *")|\1http://'$serverip'|' <<< $status )
	data='{ "channel": "mpdplayer", "data": { '${status:1}' } }'
	clientip=$( < $dirshm/clientip )
	for ip in $clientip; do
		ipOnline $ip && websocat ws://$ip:8080 <<< $( tr -d '\n' <<< $data )
	done
fi
if [[ -e $dirsystem/lcdchar ]]; then
	sed -E 's/(true|false)$/\u\1/' $dirshm/status > $dirshm/lcdcharstatus.py
	systemctl restart lcdchar
fi

if [[ -e $dirsystem/mpdoled ]]; then
	[[ $playing ]] && start_stop=start || start_stop=stop
	systemctl $start_stop mpd_oled
fi

[[ -e $dirsystem/vuled || -e $dirsystem/vumeter ]] && cava=1
if [[ $playing ]]; then
	[[ $cava ]] && systemctl start cava
else
	[[ $cava ]] && systemctl stop cava
	[[ -e $dirsystem/vumeter ]] && pushData vumeter '{ "val": 0 }'
fi

[[ -e $dirsystem/librandom && $webradio == false ]] && $dirbash/cmd.sh pladdrandom &

[[ ! -e $dirsystem/scrobble ]] && exit
# --------------------------------------------------------------------
[[ ! $trackchanged && ! -e $dirshm/elapsed ]] && exit # track changed || prev/next/stop
# --------------------------------------------------------------------
. $dirshm/statusprev
[[ $state == stop || $webradio == true || ! $Artist || ! $Title || $Time -lt 30 ]] && exit
# --------------------------------------------------------------------
if [[ $player != mpd ]]; then
	! grep -q $player=true $dirsystem/scrobble.conf && exit
# --------------------------------------------------------------------
	if [[ $state =~ ^(play|pause)$ ]]; then # renderers prev/next
		elapsed=$(( ( timestampnew - timestamp ) / 1000 ))
		(( $elapsed < $Time )) && echo $elapsed > $dirshm/elapsed
	fi
fi
if [[ -e $dirshm/elapsed ]];then
	elapsed=$( < $dirshm/elapsed )
	rm $dirshm/elapsed
	(( $elapsed < 240 && $elapsed < $(( Time / 2 )) )) && exit
# --------------------------------------------------------------------
fi
$dirbash/scrobble.sh "cmd
$Artist
$Title
CMD ARTIST TITLE"&> /dev/null &
