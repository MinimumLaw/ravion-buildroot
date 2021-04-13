#!/bin/sh

set -u
set -e

# Add a console on tty1
if [ -e ${TARGET_DIR}/etc/inittab ]; then
    grep -qE '^tty0::' ${TARGET_DIR}/etc/inittab || \
	sed -i '/GENERIC_SERIAL/a\
tty0::respawn:/sbin/getty -L tty0 0 linux # HDMI console' ${TARGET_DIR}/etc/inittab
fi

# Add system version numbers
echo !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
echo "stp-${BR2_VERSION_FULL} set into /etc/stp-release and /var/www/html/stp_release.txt"
echo "stp-${BR2_VERSION_FULL}" > ${TARGET_DIR}/etc/stp-release
echo "stp-${BR2_VERSION_FULL}" > ${TARGET_DIR}/var/www/html/stp-release.txt
echo "stp-${BR2_VERSION_FULL}" > ${BINARIES_DIR}/stp-release.txt
echo !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
