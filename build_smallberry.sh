#!/bin/sh

export DEFARGS="-j`grep -i processor /proc/cpuinfo | wc -l`"

if [ -f .config ]; then
	make ${DEFARGS} $*
else
	make stp_smallberry_defconfig
	make ${DEFARGS}
fi
