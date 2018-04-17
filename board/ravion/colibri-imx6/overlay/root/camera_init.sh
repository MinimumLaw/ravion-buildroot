#!/bin/bash

media-ctl -l "'adv7180 2-0020':0 -> 'ipu1_csi1_mux':4[1]"
media-ctl -l "'ipu1_csi1_mux':5 -> 'ipu1_csi1':0[1]"
media-ctl -l "'ipu1_csi1':2 -> 'ipu1_csi1 capture':0[1]"

media-ctl -V "'adv7180 2-0020':0 [fmt:UYVY2X8/720x480 field:interlaced]"
media-ctl -V "'ipu1_csi1_mux':5 [fmt:UYVY2X8/720x480 field:interlaced]"
media-ctl -V "'ipu1_csi1':2 [fmt:UYVY/720x480 field:interlaced]"

gst-launch-1.0 v4l2src device=/dev/video5 ! videoconvert ! ximagesink
