#!/bin/sh

mkimage \
    -A arm \
    -T script \
    -n "TFTP Boot script" \
    -d bscript.txt \
    bscript.img
