#!/bin/bash

alias=r1

. /srv/http/bash/settings/addons.sh

# 20240818
file=$dirmpd/albumbyartist
[[ -e $file && $( grep -m1 . $file | cut -c 2 ) != ^ ]] && php /srv/http/cmd.php sort albumbyartist

lsblk -no path,vendor,model | grep -v ' $' > $dirshm/lsblkusb

if [[ -e /boot/kernel.img ]]; then
	file=/usr/bin/mount.ntfs3
	if [[ ! -e $file ]]; then
		ln -s /usr/bin/ntfs-3g $file
		sed -i '/^allowed_types/ s/$/, ntfs3/' /etc/udevil/udevil.conf
	fi
else
	file=/etc/pacman.conf
	! grep -q wpa_supplicant $file && sed -i '/^#*IgnorePkg/ {s/^#//; s/$/ wpa_supplicant/}' $file
fi

# 20240719
rm -f $dirshm/system

lsblk -Sno path,vendor,model > $dirshm/lsblkusb

file=$dirsystem/lcdcharconf.py
if [[ -e $file ]]; then
	sed -i -E 's/False|"//g' $file
	mv $file $dirsystem/lcdchar.conf
fi
# 20240707
dir=/srv/http/assets/img/guide
if [[ -e $dir/59.jpg ]]; then
	rm -f $dir/*
	curl -skL https://github.com/rern/_assets/raw/master/guide/guide.tar.xz | bsdtar xf - -C $dir
fi

#-------------------------------------------------------------------------------
installstart "$1"

rm -rf /srv/http/assets/{css,js} /srv/http/{bash,settings}

getinstallzip

. $dirbash/common.sh
dirPermissions
$dirbash/cmd.sh cachebust
[[ -e $dirsystem/color ]] && $dirbash/cmd.sh color

installfinish

# 20240816
systemctl restart mpd
