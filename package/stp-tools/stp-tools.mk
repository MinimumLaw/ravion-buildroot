##############################################################################
# stp-tools
##############################################################################

STP_TOOLS_VERSION = origin/master
STP_TOOLS_SITE = git@bitbucket.org:DenisKalou/raspberry_uart.git
STP_TOOLS_SITE_METHOD = git
STP_TOOLS_LICENSE = PROPRIETARY
# STP-TOOLS_DEPENDENCIES = ???

define STP_TOOLS_BUILD_CMDS
	$(TARGET_MAKE_ENV) $(TARGET_CONFIGURE_OPTS) $(MAKE) -C $(@D) all
endef

define STP_TOOLS_INSTALL_TARGET_CMDS
	$(INSTALL) -D -m 0755 $(@D)/call $(TARGET_DIR)/usr/local/bin/call
	$(INSTALL) -D -m 0755 $(@D)/gps $(TARGET_DIR)/usr/local/bin/gps
	$(INSTALL) -D -m 0755 $(@D)/sc_uart $(TARGET_DIR)/usr/local/bin/sc_uart
endef

$(eval $(generic-package))
