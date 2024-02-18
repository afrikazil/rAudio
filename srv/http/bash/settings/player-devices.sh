#!/bin/bash

# get hardware devices data with 'aplay' and amixer
# - aplay - get card index, sub-device index and aplayname
# - mixer device
#    - from file if manually set
#    - from 'amixer'
#        - if more than 1, filter with 'Digital|Master' | get 1st one
# - mixer_type
#    - from file if manually set
#    - set as hardware if mixer device available
#    - if nothing, set as software

### included by player-conf.sh, player-data.sh
rm -f $dirshm/{amixercontrol,listdevice,listmixer,nosound,output}
audioaplayname=$( getContent $dirsystem/audio-aplayname 'bcm2835 Headphones' )
audiooutput=$( getContent $dirsystem/audio-output 'On-board Headphones' )
aplayl=$( aplay -l 2> /dev/null | awk '/^card/ && !/Loopback/' )
if [[ ! $aplayl ]]; then
	[[ -e $dirshm/btreceiver ]] && asoundcard=0 || asoundcard=-1
	echo $asoundcard > $dirsystem/asoundcard
	touch $dirshm/nosound
	pushData display '{ "volumenone": true }'
	return
fi

readarray -t lines <<< $( awk '/^card/ && !/Loopback/' <<< $aplayl \
							| sed -E 's/^.*\[|]//g' \
							| sort -u ) # remove duplicate control names
for aplayname in "${lines[@]}"; do
	[[ ${aplayname:0:8} == snd_rpi_ ]] && aplayname=$( tr _ - <<< ${aplayname:8} ) # some snd_rpi_xxx_yyy > xxx-yyy
	#card 1: sndrpiwsp [snd_rpi_wsp], device 0: WM5102 AiFi wm5102-aif1-0 []
	#card 1: RPiCirrus [RPi-Cirrus],  device 0: WM5102 AiFi wm5102-aif1-0 [WM5102 AiFi wm5102-aif1-0]
	[[ $aplayname == wsp || $aplayname == RPi-Cirrus ]] && aplayname=cirrus-wm5102
	[[ $aplayname == $audioaplayname ]] && name=$audiooutput || name=${aplayname/bcm2835/On-board}
	listdevice+=', "'$name'": "'$aplayname'"'
done
########
echo "{ ${listdevice:1} }" > $dirshm/listdevice

if [[ $usbdac == add ]]; then
	line=$( tail -1 <<< $aplayl )
elif [[ $aplayname == cirrus-wm5102 ]]; then
	line=$( grep wm5102 <<< $aplayl | head -1 )
	hwmixer='HPOUT2 Digital'
	listmixer='[ "HPOUT1 Digital", "HPOUT2 Digital", "SPDIF Out", "Speaker Digital" ]'
else
	line=$( grep "$audioaplayname" <<< $aplayl | head -1 ) # remove duplicate control names
fi
readarray -t cnd <<< $( sed -E 's/card (.*):.*\[(.*)], device (.*):.*/\1\n\2\n\3/' <<< "$line" )
card=${cnd[0]}
aplayname=${cnd[1]}
device=${cnd[2]}
[[ $usbdac == add ]] && name=$aplayname || name=$audiooutput

if [[ $aplayname != cirrus-wm5102 ]]; then
	amixer=$( amixer -c $card scontents )
	if [[ $amixer ]]; then
		amixer=$( grep -A1 ^Simple <<< $amixer \
					| sed 's/^\s*Cap.*: /^/' \
					| tr -d '\n' \
					| sed 's/--/\n/g' \
					| grep -v "'Mic'" )
		controls=$( grep -E 'volume.*pswitch|Master.*volume' <<< $amixer )
		[[ ! $controls ]] && controls=$( grep volume <<< $amixer )
		if [[ $controls ]]; then
			readarray -t controls <<< $( cut -d"'" -f2 <<< $controls | sort -u )
			for control in "${controls[@]}"; do
				listmixer+=', "'$control'"'
				[[ $control == Digital ]] && hwmixer=Digital
			done
			listmixer="[ ${listmixer:1} ]"
			hwmixerfile="$dirsystem/hwmixer-$aplayname"
			if [[ -e $hwmixerfile ]]; then # manual
				hwmixer=$( < "$hwmixerfile" )
			elif [[ ! $hwmixer ]]; then    # not Digital
				hwmixer=${controls[0]}
			fi
		fi
	fi
fi
mixertypefile="$dirsystem/mixertype-$aplayname"
if [[ -e $mixertypefile ]]; then
	mixertype=$( < "$mixertypefile" )
else
	[[ $listmixer ]] && mixertype=hardware || mixertype=none
fi

########
echo $card > $dirsystem/asoundcard
[[ $hwmixer ]] && echo "$hwmixer" > $dirshm/amixercontrol # quote to includes trailing space (if any)
[[ $listmixer ]] && echo $listmixer > $dirshm/listmixer
echo '
aplayname="'$aplayname'"
name="'$name'"
card='$card'
device='$device'
hwmixer='$hwmixer'
mixertype='$mixertype > $dirshm/output
