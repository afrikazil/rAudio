#!/bin/bash

. /srv/http/bash/common.sh

. $dirshm/radio
filelabel=$dirshm/webradio/DABlabel.txt
filecover=$dirshm/webradio/DABslide.jpg
filetitle=$dirshm/webradio/DABtitle

while true; do
	# title
	if [[ ! $( awk NF $filelabel ) ]]; then
		pushData mpdradio '{ "Title": "" }'
		sleep 10
		continue
	fi
	
	if ! cmp -s $filelabel $filetitle; then
		cp -f $filelabel $filetitle
		elapsed=$( mpcElapsed )
		data='
  "Album"        : "DAB Radio"
, "Artist"       : "'$station'"
, "coverart"     : ""
, "elapsed"      : '$elapsed'
, "file"         : "'$file'"
, "icon"         : "dabradio"
, "sampling"     : "'$sampling'"
, "state"        : "play"
, "station"      : ""
, "stationcover" : "'$stationcover'"
, "Time"         : false
, "Title"        : "'$( < $filetitle )'"
, "webradio"     : true'
		pushData mpdradio "{ $data }"
		sed 's/^.."//; s/" *: /=/' <<< $data > $dirshm/status
		$dirbash/status-push.sh statusradio &
	fi
	# coverart
	[[ ! $( awk NF $filecover ) ]] && sleep 10 && continue
	
	name=$( tr -d ' \"`?/#&'"'" < $filetitle )
	coverfile=/srv/http/data/shm/webradio/$name.jpg
	if ! cmp -s $filecover $coverfile; then # change later than title or multiple covers
		cp -f $filecover $coverfile
		coverart="${coverfile:9}"
		sed -i -E "s/^(coverart=).*/\1$coverart/" $dirshm/status
		pushData coverart '{ "url": "'$coverart'" }'
	fi
	sleep 10
done
