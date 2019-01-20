##############################################################################
# stp-tools
##############################################################################

STP_TOOLS_VERSION = origin/master
STP_TOOLS_SITE = git@bitbucket.org:DenisKalou/raspberry_uart.git
STP_TOOLS_SITE_METHOD = git
STP_TOOLS_LICENSE = PROPRIETARY
# STP-TOOLS_DEPENDENCIES = ???

define STP_TOOLS_BUILD_CMDS
	echo $(MAKE) CC=$(CROSS_COMPILE)gcc
endef

define STP_TOOLS_INSTALL_TARGET_CMDS
	echo $(INSTALL) -D -m 0644 $(@D)/call $(TARGET_DIR)/usr/local/bin/call
	echo $(INSTALL) -D -m 0644 $(@D)/gps $(TARGET_DIR)/usr/local/bin/gps
	echo $(INSTALL) -D -m 0644 $(@D)/sc_uart $(TARGET_DIR)/usr/local/bin/sc_uart
endef

$(eval $(generic-package))
