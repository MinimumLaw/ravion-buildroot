#!/bin/bash

set -e

BOARD_DIR="$(dirname $0)"
BOARD_NAME="$(basename ${BOARD_DIR})"
GENIMAGE_CFG="${BOARD_DIR}/genimage-${BOARD_NAME}.cfg"
GENIMAGE_TMP="${BUILD_DIR}/genimage.tmp"

for arg in "$@"
do
	case "${arg}" in
		--add-audio-overlay)
		if ! grep -qE '^dtoverlay=pwm-2chan' "${BINARIES_DIR}/rpi-firmware/config.txt"; then
			echo "Adding audio overlay to config.txt"
			cat << __EOF__ >> "${BINARIES_DIR}/rpi-firmware/config.txt"
# Add audio overlay
dtoverlay=pwm-2chan,pin=18,func=2,pin2=13,func2=4
__EOF__
		fi
		;;
		--enable-audio)
		if ! grep -qE '^dtparam=audio=on' "${BINARIES_DIR}/rpi-firmware/config.txt"; then
			echo "Enabling audio in config.txt"
			cat << __EOF__ >> "${BINARIES_DIR}/rpi-firmware/config.txt"
# Enable audio
dtparam=audio=on
__EOF__
		fi
		;;
		--add-pi3-miniuart-bt-overlay)
		if ! grep -qE '^dtoverlay=' "${BINARIES_DIR}/rpi-firmware/config.txt"; then
			echo "Adding 'dtoverlay=pi3-miniuart-bt' to config.txt (fixes ttyAMA0 serial console)."
			cat << __EOF__ >> "${BINARIES_DIR}/rpi-firmware/config.txt"

# fixes rpi3 ttyAMA0 serial console
dtoverlay=pi3-miniuart-bt
__EOF__
		fi
		;;
		--aarch64)
		# Run a 64bits kernel (armv8)
		sed -e '/^kernel=/s,=.*,=Image,' -i "${BINARIES_DIR}/rpi-firmware/config.txt"
		if ! grep -qE '^arm_64bit=1' "${BINARIES_DIR}/rpi-firmware/config.txt"; then
			cat << __EOF__ >> "${BINARIES_DIR}/rpi-firmware/config.txt"

# enable 64bits support
arm_64bit=1
__EOF__
		fi

		# Enable uart console
		if ! grep -qE '^enable_uart=1' "${BINARIES_DIR}/rpi-firmware/config.txt"; then
			cat << __EOF__ >> "${BINARIES_DIR}/rpi-firmware/config.txt"

# enable rpi3 ttyS0 serial console
enable_uart=1
__EOF__
		fi
		;;
		--gpu_mem_256=*|--gpu_mem_512=*|--gpu_mem_1024=*)
		# Set GPU memory
		gpu_mem="${arg:2}"
		sed -e "/^${gpu_mem%=*}=/s,=.*,=${gpu_mem##*=}," -i "${BINARIES_DIR}/rpi-firmware/config.txt"
		;;
	esac

done

#
# Add requeied params
#
if ! grep -qE '^core_freq=250' "${BINARIES_DIR}/rpi-firmware/config.txt"; then
	cat << __EOF__ >> "${BINARIES_DIR}/rpi-firmware/config.txt"

# set core frequency
core_freq=250
__EOF__
fi

if ! grep -qE '^init_uart_clock=3000000' "${BINARIES_DIR}/rpi-firmware/config.txt"; then
	cat << __EOF__ >> "${BINARIES_DIR}/rpi-firmware/config.txt"

# set uart clock
init_uart_clock=3000000
__EOF__
fi

# Copy initrd image (update procedure here)
cp -f ${BOARD_DIR}/rootfs.cpio.gz ${BINARIES_DIR}

# then uncomment initrd in config.txt
if ! grep -qE '^initramfs rootfs.cpio.gz' "${BINARIES_DIR}/rpi-firmware/config.txt"; then
	cat << __EOF__ >> "${BINARIES_DIR}/rpi-firmware/config.txt"

# set initramfs image (prebuild)
initramfs rootfs.cpio.gz
__EOF__
fi

echo "root=/dev/mmcblk0p2 rootwait console=tty2" > "${BINARIES_DIR}/rpi-firmware/cmdline.txt"

rm -rf "${GENIMAGE_TMP}"

genimage                           \
	--rootpath "${TARGET_DIR}"     \
	--tmppath "${GENIMAGE_TMP}"    \
	--inputpath "${BINARIES_DIR}"  \
	--outputpath "${BINARIES_DIR}" \
	--config "${GENIMAGE_CFG}"

exit $?
