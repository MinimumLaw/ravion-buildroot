#!/bin/sh

export DEFARGS="-j`grep -i processor /proc/cpuinfo | wc -l`"

if [ -f .config ]; then
	make ${DEFARGS} $*
else
	make stp_stm32mp1_defconfig
	make ${DEFARGS}
fi
