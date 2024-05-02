#!/bin/bash

. /srv/http/bash/common.sh

if [[ $1 == off ]]; then
	audioCDplClear
	pushData power '{ "type": "off" }'
else
	audioCDplClear && $dirbash/status-push.sh
	reboot=1
	startup=$( systemd-analyze | sed -n '/^Startup/ {s/.*= //; s/[^0-9]//g; p}' )
	pushData power '{ "type": "reboot", "startup": '$startup' }'
fi

playerActive upnp && $dirbash/cmd.sh playerstop

if systemctl -q is-active nfs-server; then # server rAudio
	ipserver=$( ipAddress )
	ipclients=$( grep -v $ipserver $filesharedip )
	if [[ $ipclients ]]; then
		[[ ! $2 ]] && echo -1 && exit # $2 confirm proceed
# --------------------------------------------------------------------
		[[ $reboot ]] && msg='Reboot ...' || msg='Power off ...'
		for ip in $ipclients; do
			notify -ip $ip 'networks blink' 'Server rAudio' "$msg"
		done
	fi
	sed -i "/$ipserver/ d" $filesharedip
elif [[ -e $filesharedip ]]; then
	sed -i "/$( ipAddress )/ d" $filesharedip
fi
if [[ -e $dirsystem/camilladsp ]]; then
	$dirsettings/camilla.sh saveconfig
	[[ -e /etc/default/camilladsp.backup ]] && mv -f /etc/default/camilladsp{.backup,}
fi
[[ -e $dirshm/btreceiver ]] && cp $dirshm/btreceiver $dirsystem
touch $dirshm/power
mpc -q stop
if [[ -e $dirsystem/lcdchar ]]; then
	systemctl stop lcdchar
	$dirbash/lcdchar.py logo
fi
if [[ -e $dirshm/clientip ]]; then
	clientip=$( < $dirshm/clientip )
	for ip in $clientip; do
		sshCommand $ip $dirbash/cmd.sh playerstop
	done
fi
cdda=$( mpc -f %file%^%position% playlist | grep ^cdda: | cut -d^ -f2 )
[[ $cdda ]] && mpc -q del $cdda
[[ -e $dirshm/relayson ]] && $dirbash/relays.sh off && sleep 2
ply-image /srv/http/assets/img/splash.png &> /dev/null
if mount | grep -q -m1 $dirnas; then
	umount -l $dirnas/* &> /dev/null
	sleep 3
fi
echo 1 > /sys/class/backlight/rpi_backlight/bl_power

[[ -e /boot/shutdown.sh ]] && /boot/shutdown.sh
[[ $reboot ]] && reboot || poweroff
