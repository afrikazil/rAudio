#!/bin/bash

readarray -t args <<< "$1"

date=$( date +%s )
covername=$( echo "$1" | head -2 | tr -d '\n "`?/#&'"'" ) # Artist Album file > ArtistAlbum

### 1 - already fetched online-file #########################
urlname=/data/shm/online-$covername
coverfile=$( ls /srv/http$urlname.* 2> /dev/null )
[[ -e $coverfile ]] && echo $urlname.$date.${coverfile/*.} && exit

urlname=/data/shm/licover-$covername
coverfile=$( ls /srv/http$urlname.* 2> /dev/null )
[[ -e $coverfile ]] && echo $urlname.$date.${coverfile/*.} && exit

### 2 - already extracted embedded-file #########################
urlname=/data/embedded/$covername
[[ -e /srv/http$urlname.jpg ]] && echo $urlname.$date.jpg && exit

### 3 - coverfile in directory ##################################
[[ ${#args[@]} > 1 ]] && mpdpath=${args[2]} || mpdpath=${args[0]} # for mpdlibrary.php
[[ ${mpdpath: -14:10} == .cue/track ]] && mpdpath=$( dirname "$mpdpath" )
path="/mnt/MPD/$mpdpath"
[[ -d $path ]] && dir=$path || dir=$( dirname "$path" )
coverfile=$( ls -1 "$dir" \
				| grep -i '^cover\.\|^folder\.\|^front\.\|^album\.' \
				| grep -i '.gif$\|.jpg$\|.png$' \
				| head -1 )
[[ -e "$dir/$coverfile" ]] && echo ${coverfile%.*}.$date.${coverfile/*.} && exit

### 4 - embedded ################################################
urlname=/data/embedded/$covername
coverfile=/srv/http$urlname.jpg
files=$( mpc ls "$mpdpath" 2> /dev/null )
readarray -t files <<<"$files"
for file in "${files[@]}"; do
	file="/mnt/MPD/$file"
	if [[ -f "$file" ]]; then
		kid3-cli -c "select \"$file\"" -c "get picture:$coverfile" &> /dev/null # suppress '1 space' stdout
		[[ -e $coverfile ]] && echo $urlname.$date.jpg && exit
	fi
done
