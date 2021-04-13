##############################################################################
# stp-tools
##############################################################################

STP_TOOLS_VERSION = core_test #HEAD
STP_TOOLS_SITE = git@bitbucket.org:DenisKalou/speakingcity_raspbery.git
STP_TOOLS_SITE_METHOD = git
STP_TOOLS_LICENSE = PROPRIETARY
# STP-TOOLS_DEPENDENCIES = ???

define STP_TOOLS_BUILD_CMDS
	$(TARGET_MAKE_ENV) $(TARGET_CONFIGURE_OPTS) $(MAKE) -C $(@D)/source all
endef

define STP_TOOLS_INSTALL_TARGET_CMDS
	$(INSTALL) -D -m 0755 $(@D)/source/call $(TARGET_DIR)/usr/local/bin/call
	$(INSTALL) -D -m 0755 $(@D)/source/sc_uart $(TARGET_DIR)/usr/local/bin/sc_uart
	$(INSTALL) -D -m 0755 $(@D)/source/sc_ble_scan $(TARGET_DIR)/usr/local/bin/sc_ble_scan
	$(INSTALL) -D -m 0755 $(@D)/source/siogg $(TARGET_DIR)/usr/local/bin/siogg
endef

define STP_TOOLS_USERS
staff 50 staff -1 =staff /overlay/update /bin/sh wheel,daemon,video,audio,disk,tty SpeakingSity update
endef

define STP_TOOLS_INSTALL_INIT_SYSV
	$(INSTALL) -D -m 0755 package/stp-tools/S01wireless-comm \
		$(TARGET_DIR)/etc/init.d/S01wireless-comm
	$(INSTALL) -D -m 0755 package/stp-tools/S98update-data \
		$(TARGET_DIR)/etc/init.d/S98update-data
	$(INSTALL) -D -m 0755 package/stp-tools/S99stp-tools \
		$(TARGET_DIR)/etc/init.d/S99stp-tools
endef

$(eval $(generic-package))
