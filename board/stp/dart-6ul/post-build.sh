#!/bin/sh

BOARD_DIR="$(dirname $0)"

$HOST_DIR/bin/mkimage -A arm -O linux -T script -C none  \
-n "boot script" -d $BOARD_DIR/boot.scr.txt $BOARD_DIR/boot.scr

install -m 0644 -D $BOARD_DIR/boot.scr $BINARIES_DIR/boot.scr
install -m 0644 -D $BOARD_DIR/uEnv.txt $BINARIES_DIR/uEnv.txt


