<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

/**
 * Core schema for the trimmed AdamRMS feature set (inventory, projects, maintenance).
 *
 * The table definitions were generated from the production schema prior to pruning and
 * include only the tables still referenced by the active API, common libraries and
 * project views.  Update this migration whenever new tables or columns are required
 * by the supported feature set.
 */
final class CoreSchema extends AbstractMigration
{
    public function up(): void
    {
        $this->execute('SET FOREIGN_KEY_CHECKS=0;');
        $this->execute(<<<'SQL'
CREATE TABLE `analyticsEvents` (
  `analyticsEvents_id` int(11) NOT NULL AUTO_INCREMENT,
  `analyticsEvents_timestamp` timestamp NOT NULL,
  `users_userid` int(11) NOT NULL,
  `adminUser_users_userid` int(11) DEFAULT NULL,
  `authTokens_id` int(11) NOT NULL,
  `instances_id` int(11) DEFAULT NULL,
  `analyticsEvents_path` varchar(500) NOT NULL,
  `analyticsEvents_action` varchar(500) NOT NULL,
  `analyticsEvents_payload` text DEFAULT NULL,
  PRIMARY KEY (`analyticsEvents_id`),
  KEY `analyticsEvents_users_users_userid_fk` (`users_userid`),
  KEY `analyticsEvents_admin_users_users_userid_fk` (`adminUser_users_userid`),
  KEY `analyticsEvents_instances_instances_id_fk` (`instances_id`),
  KEY `analyticsEvents_authTokens_authTokens_id_fk` (`authTokens_id`),
  CONSTRAINT `analyticsEvents_admin_users_users_userid_fk` FOREIGN KEY (`adminUser_users_userid`) REFERENCES `users` (`users_userid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `analyticsEvents_authTokens_authTokens_id_fk` FOREIGN KEY (`authTokens_id`) REFERENCES `authTokens` (`authTokens_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `analyticsEvents_instances_instances_id_fk` FOREIGN KEY (`instances_id`) REFERENCES `instances` (`instances_id`) ON DELETE SET NULL ON UPDATE SET NULL,
  CONSTRAINT `analyticsEvents_users_users_userid_fk` FOREIGN KEY (`users_userid`) REFERENCES `users` (`users_userid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
SQL
        );

        $this->execute(<<<'SQL'
CREATE TABLE `assetCategories` (
  `assetCategories_id` int(11) NOT NULL AUTO_INCREMENT,
  `assetCategories_name` varchar(200) NOT NULL,
  `assetCategories_fontAwesome` varchar(100) DEFAULT NULL,
  `assetCategories_rank` int(11) NOT NULL DEFAULT 999,
  `assetCategoriesGroups_id` int(11) NOT NULL,
  `instances_id` int(11) DEFAULT NULL,
  `assetCategories_deleted` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`assetCategories_id`),
  KEY `assetCategories_instances_instances_id_fk` (`instances_id`),
  KEY `assetCategories_Groups_id_fk` (`assetCategoriesGroups_id`),
  CONSTRAINT `assetCategories_Groups_id_fk` FOREIGN KEY (`assetCategoriesGroups_id`) REFERENCES `assetCategoriesGroups` (`assetCategoriesGroups_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `assetCategories_instances_instances_id_fk` FOREIGN KEY (`instances_id`) REFERENCES `instances` (`instances_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci ROW_FORMAT=DYNAMIC;
SQL
        );

        $this->execute(<<<'SQL'
CREATE TABLE `assetCategoriesGroups` (
  `assetCategoriesGroups_id` int(11) NOT NULL AUTO_INCREMENT,
  `assetCategoriesGroups_name` varchar(200) NOT NULL,
  `assetCategoriesGroups_fontAwesome` varchar(300) DEFAULT NULL,
  `assetCategoriesGroups_order` int(11) NOT NULL DEFAULT 999,
  `instances_id` int(11) DEFAULT NULL,
  `assetCategoriesGroups_deleted` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`assetCategoriesGroups_id`),
  KEY `assetCategoriesGroups_instances_instances_id_fk` (`instances_id`),
  CONSTRAINT `assetCategoriesGroups_instances_instances_id_fk` FOREIGN KEY (`instances_id`) REFERENCES `instances` (`instances_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC;
SQL
        );

        $this->execute(<<<'SQL'
CREATE TABLE `assetGroups` (
  `assetGroups_id` int(11) NOT NULL AUTO_INCREMENT,
  `assetGroups_name` varchar(200) NOT NULL,
  `assetGroups_description` text DEFAULT NULL,
  `assetGroups_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `users_userid` int(11) DEFAULT NULL,
  `instances_id` int(11) NOT NULL,
  PRIMARY KEY (`assetGroups_id`),
  KEY `assetGroups_instances_instances_id_fk` (`instances_id`),
  KEY `assetGroups_users_users_userid_fk` (`users_userid`),
  CONSTRAINT `assetGroups_instances_instances_id_fk` FOREIGN KEY (`instances_id`) REFERENCES `instances` (`instances_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `assetGroups_users_users_userid_fk` FOREIGN KEY (`users_userid`) REFERENCES `users` (`users_userid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC;
SQL
        );

        $this->execute(<<<'SQL'
CREATE TABLE `assetTypes` (
  `assetTypes_id` int(11) NOT NULL AUTO_INCREMENT,
  `assetTypes_name` varchar(500) NOT NULL,
  `assetCategories_id` int(11) NOT NULL,
  `manufacturers_id` int(11) NOT NULL,
  `instances_id` int(11) DEFAULT NULL,
  `assetTypes_description` varchar(1000) DEFAULT NULL,
  `assetTypes_productLink` varchar(500) DEFAULT NULL,
  `assetTypes_definableFields` varchar(500) DEFAULT NULL,
  `assetTypes_mass` decimal(55,5) DEFAULT NULL,
  `assetTypes_inserted` timestamp NULL DEFAULT NULL,
  `assetTypes_dayRate` int(11) NOT NULL,
  `assetTypes_weekRate` int(11) NOT NULL,
  `assetTypes_value` int(11) NOT NULL,
  PRIMARY KEY (`assetTypes_id`),
  KEY `assetTypes_assetCategories_assetCategories_id_fk` (`assetCategories_id`),
  KEY `assetTypes_manufacturers_manufacturers_id_fk` (`manufacturers_id`),
  KEY `assetTypes_instances_instances_id_fk` (`instances_id`),
  CONSTRAINT `assetTypes_assetCategories_assetCategories_id_fk` FOREIGN KEY (`assetCategories_id`) REFERENCES `assetCategories` (`assetCategories_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `assetTypes_instances_instances_id_fk` FOREIGN KEY (`instances_id`) REFERENCES `instances` (`instances_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `assetTypes_manufacturers_manufacturers_id_fk` FOREIGN KEY (`manufacturers_id`) REFERENCES `manufacturers` (`manufacturers_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci ROW_FORMAT=DYNAMIC;
SQL
        );

        $this->execute(<<<'SQL'
CREATE TABLE `assets` (
  `assets_id` int(11) NOT NULL AUTO_INCREMENT,
  `assets_tag` varchar(200) DEFAULT NULL COMMENT 'The ID/Tag that the asset carries marked onto it',
  `assetTypes_id` int(11) NOT NULL,
  `assets_notes` text DEFAULT NULL,
  `instances_id` int(11) NOT NULL,
  `asset_definableFields_1` varchar(200) DEFAULT NULL,
  `asset_definableFields_2` varchar(200) DEFAULT NULL,
  `asset_definableFields_3` varchar(200) DEFAULT NULL,
  `asset_definableFields_4` varchar(200) DEFAULT NULL,
  `asset_definableFields_5` varchar(200) DEFAULT NULL,
  `asset_definableFields_6` varchar(200) DEFAULT NULL,
  `asset_definableFields_7` varchar(200) DEFAULT NULL,
  `asset_definableFields_8` varchar(200) DEFAULT NULL,
  `asset_definableFields_9` varchar(200) DEFAULT NULL,
  `asset_definableFields_10` varchar(200) DEFAULT NULL,
  `assets_inserted` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `assets_dayRate` int(11) DEFAULT NULL,
  `assets_linkedTo` int(11) DEFAULT NULL,
  `assets_weekRate` int(11) DEFAULT NULL,
  `assets_value` int(11) DEFAULT NULL,
  `assets_mass` decimal(55,5) DEFAULT NULL,
  `assets_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `assets_endDate` timestamp NULL DEFAULT NULL,
  `assets_archived` varchar(200) DEFAULT NULL,
  `assets_assetGroups` varchar(500) DEFAULT NULL,
  `assets_storageLocation` int(11) DEFAULT NULL,
  `assets_showPublic` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`assets_id`),
  KEY `assets_assetTypes_assetTypes_id_fk` (`assetTypes_id`),
  KEY `assets_assets_assets_id_fk` (`assets_linkedTo`),
  KEY `assets_instances_instances_id_fk` (`instances_id`),
  KEY `assets_locations_locations_id_fk` (`assets_storageLocation`),
  CONSTRAINT `assets_assetTypes_assetTypes_id_fk` FOREIGN KEY (`assetTypes_id`) REFERENCES `assetTypes` (`assetTypes_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `assets_assets_assets_id_fk` FOREIGN KEY (`assets_linkedTo`) REFERENCES `assets` (`assets_id`) ON DELETE SET NULL ON UPDATE SET NULL,
  CONSTRAINT `assets_instances_instances_id_fk` FOREIGN KEY (`instances_id`) REFERENCES `instances` (`instances_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `assets_locations_locations_id_fk` FOREIGN KEY (`assets_storageLocation`) REFERENCES `locations` (`locations_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci ROW_FORMAT=DYNAMIC;
SQL
        );

        $this->execute(<<<'SQL'
CREATE TABLE `assetsAssignments` (
  `assetsAssignments_id` int(11) NOT NULL AUTO_INCREMENT,
  `assets_id` int(11) NOT NULL,
  `projects_id` int(11) NOT NULL,
  `assetsAssignments_comment` varchar(500) DEFAULT NULL,
  `assetsAssignments_customPrice` int(11) NOT NULL DEFAULT 0,
  `assetsAssignments_discount` float NOT NULL DEFAULT 0,
  `assetsAssignments_timestamp` timestamp NULL DEFAULT NULL,
  `assetsAssignments_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `assetsAssignmentsStatus_id` int(11) DEFAULT NULL COMMENT '0 = None applicable\n10 = Pending pick\n20 = Picked\n30 = Prepping\n40 = Tested\n50 = Packed\n60 = Dispatched\n70 = Awaiting Check-in\n80 = Case opened\n90 = Unpacked\n100 = Tested\n110 = Stored',
  `assetsAssignments_linkedTo` int(11) DEFAULT NULL,
  PRIMARY KEY (`assetsAssignments_id`),
  KEY `assetsAssignments_assets_assets_id_fk` (`assets_id`),
  KEY `assetsAssignments_projects_projects_id_fk` (`projects_id`),
  KEY `assetsAssignments_assetsAssignments_assetsAssignments_id_fk` (`assetsAssignments_linkedTo`),
  KEY `assetsAssignments_assetsAssignmentsStatus_id_fk` (`assetsAssignmentsStatus_id`),
  CONSTRAINT `assetsAssignments_assetsAssignmentsStatus_id_fk` FOREIGN KEY (`assetsAssignmentsStatus_id`) REFERENCES `assetsAssignmentsStatus` (`assetsAssignmentsStatus_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `assetsAssignments_assetsAssignments_assetsAssignments_id_fk` FOREIGN KEY (`assetsAssignments_linkedTo`) REFERENCES `assetsAssignments` (`assetsAssignments_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `assetsAssignments_assets_assets_id_fk` FOREIGN KEY (`assets_id`) REFERENCES `assets` (`assets_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `assetsAssignments_projects_projects_id_fk` FOREIGN KEY (`projects_id`) REFERENCES `projects` (`projects_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci ROW_FORMAT=DYNAMIC;
SQL
        );

        $this->execute(<<<'SQL'
CREATE TABLE `assetsAssignmentsStatus` (
  `assetsAssignmentsStatus_id` int(11) NOT NULL AUTO_INCREMENT,
  `instances_id` int(11) NOT NULL,
  `assetsAssignmentsStatus_name` varchar(200) NOT NULL,
  `assetsAssignmentsStatus_order` int(11) DEFAULT 999,
  `assetsAssignmentsStatus_deleted` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`assetsAssignmentsStatus_id`),
  KEY `assetsAssignmentsStatus_instances_instances_id_fk` (`instances_id`),
  CONSTRAINT `assetsAssignmentsStatus_instances_instances_id_fk` FOREIGN KEY (`instances_id`) REFERENCES `instances` (`instances_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC;
SQL
        );

        $this->execute(<<<'SQL'
CREATE TABLE `assetsBarcodes` (
  `assetsBarcodes_id` int(11) NOT NULL AUTO_INCREMENT,
  `assets_id` int(11) NOT NULL,
  `assetsBarcodes_value` varchar(500) NOT NULL,
  `assetsBarcodes_type` varchar(500) NOT NULL,
  `assetsBarcodes_notes` text DEFAULT NULL,
  `assetsBarcodes_added` timestamp NOT NULL,
  `users_userid` int(11) DEFAULT NULL COMMENT 'Userid that added it',
  `assetsBarcodes_deleted` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`assetsBarcodes_id`),
  KEY `assetsBarcodes_assets_assets_id_fk` (`assets_id`),
  KEY `assetsBarcodes_users_users_userid_fk` (`users_userid`),
  CONSTRAINT `assetsBarcodes_assets_assets_id_fk` FOREIGN KEY (`assets_id`) REFERENCES `assets` (`assets_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `assetsBarcodes_users_users_userid_fk` FOREIGN KEY (`users_userid`) REFERENCES `users` (`users_userid`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC;
SQL
        );

        $this->execute(<<<'SQL'
CREATE TABLE `assetsBarcodesScans` (
  `assetsBarcodesScans_id` int(11) NOT NULL AUTO_INCREMENT,
  `assetsBarcodes_id` int(11) NOT NULL,
  `assetsBarcodesScans_timestamp` timestamp NOT NULL,
  `users_userid` int(11) DEFAULT NULL,
  `locationsBarcodes_id` int(11) DEFAULT NULL,
  `location_assets_id` int(11) DEFAULT NULL,
  `assetsBarcodes_customLocation` varchar(500) DEFAULT NULL,
  `assetsBarcodesScans_barcodeWasScanned` tinyint(1) NOT NULL DEFAULT 1,
  `assetsBarcodesScans_validation` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`assetsBarcodesScans_id`),
  KEY `assetsBarcodesScans_assetsBarcodes_assetsBarcodes_id_fk` (`assetsBarcodes_id`),
  KEY `assetsBarcodesScans_users_users_userid_fk` (`users_userid`),
  KEY `assetsBarcodesScans_locationsBarcodes_locationsBarcodes_id_fk` (`locationsBarcodes_id`),
  KEY `assetsBarcodesScans_assets_assets_id_fk` (`location_assets_id`),
  CONSTRAINT `assetsBarcodesScans_assetsBarcodes_assetsBarcodes_id_fk` FOREIGN KEY (`assetsBarcodes_id`) REFERENCES `assetsBarcodes` (`assetsBarcodes_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `assetsBarcodesScans_assets_assets_id_fk` FOREIGN KEY (`location_assets_id`) REFERENCES `assets` (`assets_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `assetsBarcodesScans_locationsBarcodes_locationsBarcodes_id_fk` FOREIGN KEY (`locationsBarcodes_id`) REFERENCES `locationsBarcodes` (`locationsBarcodes_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `assetsBarcodesScans_users_users_userid_fk` FOREIGN KEY (`users_userid`) REFERENCES `users` (`users_userid`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC;
SQL
        );

        $this->execute(<<<'SQL'
CREATE TABLE `auditLog` (
  `auditLog_id` int(11) NOT NULL AUTO_INCREMENT,
  `auditLog_actionType` varchar(500) DEFAULT NULL,
  `auditLog_actionTable` varchar(500) DEFAULT NULL,
  `auditLog_actionData` longtext DEFAULT NULL,
  `auditLog_timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `users_userid` int(11) DEFAULT NULL,
  `auditLog_actionUserid` int(11) DEFAULT NULL,
  `projects_id` int(11) DEFAULT NULL,
  `auditLog_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `auditLog_targetID` int(11) DEFAULT NULL,
  PRIMARY KEY (`auditLog_id`),
  KEY `auditLog_users_users_userid_fk` (`users_userid`),
  KEY `auditLog_users_users_userid_fk_2` (`auditLog_actionUserid`),
  CONSTRAINT `auditLog_users_users_userid_fk` FOREIGN KEY (`users_userid`) REFERENCES `users` (`users_userid`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `auditLog_users_users_userid_fk_2` FOREIGN KEY (`auditLog_actionUserid`) REFERENCES `users` (`users_userid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci ROW_FORMAT=DYNAMIC;
SQL
        );

        $this->execute(<<<'SQL'
CREATE TABLE `authTokens` (
  `authTokens_id` int(11) NOT NULL AUTO_INCREMENT,
  `authTokens_token` varchar(500) NOT NULL,
  `authTokens_type` varchar(100) DEFAULT NULL,
  `authTokens_created` timestamp NOT NULL DEFAULT current_timestamp(),
  `authTokens_ipAddress` varchar(500) DEFAULT NULL,
  `users_userid` int(11) NOT NULL,
  `authTokens_valid` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1 for true. 0 for false',
  `authTokens_adminId` int(11) DEFAULT NULL,
  `authTokens_deviceType` varchar(1000) NOT NULL,
  PRIMARY KEY (`authTokens_id`),
  UNIQUE KEY `token` (`authTokens_token`),
  KEY `authTokens_users_users_userid_fk` (`users_userid`),
  KEY `authTokens_users_users_userid_fk_2` (`authTokens_adminId`),
  CONSTRAINT `authTokens_users_users_userid_fk` FOREIGN KEY (`users_userid`) REFERENCES `users` (`users_userid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `authTokens_users_users_userid_fk_2` FOREIGN KEY (`authTokens_adminId`) REFERENCES `users` (`users_userid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci ROW_FORMAT=DYNAMIC;
SQL
        );

        $this->execute(<<<'SQL'
CREATE TABLE `clients` (
  `clients_id` int(11) NOT NULL AUTO_INCREMENT,
  `clients_name` varchar(500) NOT NULL,
  `instances_id` int(11) NOT NULL,
  `clients_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `clients_website` varchar(500) DEFAULT NULL,
  `clients_email` varchar(500) DEFAULT NULL,
  `clients_notes` text DEFAULT NULL,
  `clients_address` varchar(500) DEFAULT NULL,
  `clients_phone` varchar(500) DEFAULT NULL,
  `clients_archived` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`clients_id`),
  KEY `clients_instances_instances_id_fk` (`instances_id`),
  CONSTRAINT `clients_instances_instances_id_fk` FOREIGN KEY (`instances_id`) REFERENCES `instances` (`instances_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci ROW_FORMAT=DYNAMIC;
SQL
        );

        $this->execute(<<<'SQL'
CREATE TABLE `cmsPages` (
  `cmsPages_id` int(11) NOT NULL AUTO_INCREMENT,
  `instances_id` int(11) NOT NULL,
  `cmsPages_showNav` tinyint(1) NOT NULL DEFAULT 0,
  `cmsPages_visibleToGroups` varchar(1000) DEFAULT NULL,
  `cmsPages_navOrder` int(11) NOT NULL DEFAULT 999,
  `cmsPages_fontAwesome` varchar(500) DEFAULT NULL,
  `cmsPages_name` varchar(500) NOT NULL,
  `cmsPages_description` text DEFAULT NULL,
  `cmsPages_archived` tinyint(1) NOT NULL DEFAULT 0,
  `cmsPages_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `cmsPages_added` timestamp NOT NULL DEFAULT current_timestamp(),
  `cmsPages_subOf` int(11) DEFAULT NULL,
  PRIMARY KEY (`cmsPages_id`),
  KEY `cmsPages_instances_instances_id_fk` (`instances_id`),
  KEY `cmsPages_cmsPages_cmsPages_id_fk` (`cmsPages_subOf`),
  CONSTRAINT `cmsPages_cmsPages_cmsPages_id_fk` FOREIGN KEY (`cmsPages_subOf`) REFERENCES `cmsPages` (`cmsPages_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `cmsPages_instances_instances_id_fk` FOREIGN KEY (`instances_id`) REFERENCES `instances` (`instances_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC;
SQL
        );

        $this->execute(<<<'SQL'
CREATE TABLE `cmsPagesDrafts` (
  `cmsPagesDrafts_id` int(11) NOT NULL AUTO_INCREMENT,
  `cmsPages_id` int(11) NOT NULL,
  `users_userid` int(11) DEFAULT NULL,
  `cmsPagesDrafts_timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `cmsPagesDrafts_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`cmsPagesDrafts_data`)),
  `cmsPagesDrafts_changelog` text DEFAULT NULL,
  `cmsPagesDrafts_revisionID` int(11) NOT NULL,
  PRIMARY KEY (`cmsPagesDrafts_id`),
  KEY `cmsPagesDrafts_cmsPages_cmsPages_id_fk` (`cmsPages_id`),
  KEY `cmsPagesDrafts_users_users_userid_fk` (`users_userid`),
  KEY `cmsPagesDrafts_cmsPagesDrafts_timestamp_index` (`cmsPagesDrafts_timestamp`),
  CONSTRAINT `cmsPagesDrafts_cmsPages_cmsPages_id_fk` FOREIGN KEY (`cmsPages_id`) REFERENCES `cmsPages` (`cmsPages_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `cmsPagesDrafts_users_users_userid_fk` FOREIGN KEY (`users_userid`) REFERENCES `users` (`users_userid`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC;
SQL
        );

        $this->execute(<<<'SQL'
CREATE TABLE `cmsPagesViews` (
  `cmsPagesViews_id` int(11) NOT NULL AUTO_INCREMENT,
  `cmsPages_id` int(11) NOT NULL,
  `cmsPagesViews_timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `users_userid` int(11) DEFAULT NULL,
  `cmsPages_type` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`cmsPagesViews_id`),
  KEY `cmsPagesViews_cmsPages_cmsPages_id_fk` (`cmsPages_id`),
  KEY `cmsPagesViews_users_users_userid_fk` (`users_userid`),
  KEY `cmsPagesViews_cmsPagesViews_timestamp_index` (`cmsPagesViews_timestamp`),
  CONSTRAINT `cmsPagesViews_cmsPages_cmsPages_id_fk` FOREIGN KEY (`cmsPages_id`) REFERENCES `cmsPages` (`cmsPages_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `cmsPagesViews_users_users_userid_fk` FOREIGN KEY (`users_userid`) REFERENCES `users` (`users_userid`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC;
SQL
        );

        $this->execute(<<<'SQL'
CREATE TABLE `config` (
  `config_key` varchar(100) NOT NULL,
  `config_value` text DEFAULT NULL,
  PRIMARY KEY (`config_key`),
  UNIQUE KEY `config_config_key_uindex` (`config_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
SQL
        );

        $this->execute(<<<'SQL'
CREATE TABLE `crewAssignments` (
  `crewAssignments_id` int(11) NOT NULL AUTO_INCREMENT,
  `users_userid` int(11) DEFAULT NULL,
  `projects_id` int(11) NOT NULL,
  `crewAssignments_personName` varchar(500) DEFAULT NULL,
  `crewAssignments_role` varchar(500) NOT NULL,
  `crewAssignments_comment` varchar(500) DEFAULT NULL,
  `crewAssignments_deleted` tinyint(1) DEFAULT 0,
  `crewAssignments_rank` int(11) DEFAULT 99,
  PRIMARY KEY (`crewAssignments_id`),
  KEY `crewAssignments_projects_projects_id_fk` (`projects_id`),
  KEY `crewAssignments_users_users_userid_fk` (`users_userid`),
  CONSTRAINT `crewAssignments_projects_projects_id_fk` FOREIGN KEY (`projects_id`) REFERENCES `projects` (`projects_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `crewAssignments_users_users_userid_fk` FOREIGN KEY (`users_userid`) REFERENCES `users` (`users_userid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci ROW_FORMAT=DYNAMIC;
SQL
        );

        $this->execute(<<<'SQL'
CREATE TABLE `emailSent` (
  `emailSent_id` int(11) NOT NULL AUTO_INCREMENT,
  `users_userid` int(11) NOT NULL,
  `emailSent_html` longtext NOT NULL,
  `emailSent_subject` varchar(255) NOT NULL,
  `emailSent_sent` timestamp NOT NULL DEFAULT current_timestamp(),
  `emailSent_fromEmail` varchar(200) NOT NULL,
  `emailSent_fromName` varchar(200) NOT NULL,
  `emailSent_toName` varchar(200) NOT NULL,
  `emailSent_toEmail` varchar(200) NOT NULL,
  PRIMARY KEY (`emailSent_id`),
  KEY `emailSent_users_users_userid_fk` (`users_userid`),
  CONSTRAINT `emailSent_users_users_userid_fk` FOREIGN KEY (`users_userid`) REFERENCES `users` (`users_userid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci ROW_FORMAT=DYNAMIC;
SQL
        );

        $this->execute(<<<'SQL'
CREATE TABLE `emailVerificationCodes` (
  `emailVerificationCodes_id` int(11) NOT NULL AUTO_INCREMENT,
  `emailVerificationCodes_code` varchar(1000) NOT NULL,
  `emailVerificationCodes_used` tinyint(1) NOT NULL DEFAULT 0,
  `emailVerificationCodes_timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `emailVerificationCodes_valid` int(11) NOT NULL DEFAULT 1,
  `users_userid` int(11) NOT NULL,
  PRIMARY KEY (`emailVerificationCodes_id`),
  KEY `emailVerificationCodes_users_users_userid_fk` (`users_userid`),
  CONSTRAINT `emailVerificationCodes_users_users_userid_fk` FOREIGN KEY (`users_userid`) REFERENCES `users` (`users_userid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci ROW_FORMAT=DYNAMIC;
SQL
        );

        $this->execute(<<<'SQL'
CREATE TABLE `instancePositions` (
  `instancePositions_id` int(11) NOT NULL AUTO_INCREMENT,
  `instances_id` int(11) NOT NULL,
  `instancePositions_displayName` varchar(500) NOT NULL,
  `instancePositions_rank` int(11) NOT NULL DEFAULT 999,
  `instancePositions_actions` varchar(15000) DEFAULT NULL,
  `instancePositions_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `cmsPages_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`instancePositions_id`),
  KEY `instancePositions_instances_instances_id_fk` (`instances_id`),
  CONSTRAINT `instancePositions_instances_instances_id_fk` FOREIGN KEY (`instances_id`) REFERENCES `instances` (`instances_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci ROW_FORMAT=DYNAMIC;
SQL
        );

        $this->execute(<<<'SQL'
CREATE TABLE `instances` (
  `instances_id` int(11) NOT NULL AUTO_INCREMENT,
  `instances_name` varchar(200) NOT NULL,
  `instances_deleted` tinyint(1) DEFAULT 0,
  `instances_suspended` tinyint(1) NOT NULL DEFAULT 0,
  `instances_planName` varchar(500) DEFAULT NULL,
  `instances_serverNotes` text DEFAULT NULL,
  `instances_address` varchar(1000) DEFAULT NULL,
  `instances_phone` varchar(200) DEFAULT NULL,
  `instances_email` varchar(200) DEFAULT NULL,
  `instances_website` varchar(200) DEFAULT NULL,
  `instances_weekStartDates` text DEFAULT NULL,
  `instances_logo` int(11) DEFAULT NULL,
  `instances_emailHeader` int(11) DEFAULT NULL COMMENT 'A 1200x600 image to be the header on their emails',
  `instances_calendarHash` varchar(200) DEFAULT NULL,
  `instances_termsAndPayment` text DEFAULT NULL,
  `instances_quoteTerms` text DEFAULT NULL,
  `instances_storageLimit` bigint(20) NOT NULL DEFAULT 524288000 COMMENT 'In bytes - 500mb is default',
  `instances_projectLimit` int(11) NOT NULL DEFAULT 0 COMMENT '0 is unlimited',
  `instances_assetLimit` int(11) NOT NULL DEFAULT 0 COMMENT '0 is unlimited',
  `instances_userLimit` int(11) NOT NULL DEFAULT 0 COMMENT '0 is unlimited',
  `instances_storageEnabled` tinyint(1) NOT NULL DEFAULT 1,
  `instances_config_linkedDefaultDiscount` double DEFAULT 100,
  `instances_config_currency` varchar(200) NOT NULL DEFAULT 'GBP',
  `instances_cableColours` text DEFAULT NULL,
  `instances_publicConfig` text DEFAULT NULL,
  `instances_trustedDomains` text DEFAULT NULL,
  `instances_calendarConfig` text DEFAULT '{"showProjectStatus":true,"showSubProjects":true,"useCustomWeekNumbers":true,"defaultView":"dayGridMonth"}',
  `instances_billingUser` int(11) DEFAULT NULL,
  `instances_planStripeCustomerId` varchar(200) DEFAULT NULL,
  `instances_suspendedReason` varchar(200) DEFAULT NULL,
  `instances_suspendedReasonType` varchar(200) DEFAULT NULL COMMENT 'noplan = Need to setup a subscription. billing = Issue with plan, need to go to billing portal. other = Other reason.',
  PRIMARY KEY (`instances_id`),
  KEY `instancesBillingUser_users_users_userid_fk` (`instances_billingUser`),
  CONSTRAINT `instancesBillingUser_users_users_userid_fk` FOREIGN KEY (`instances_billingUser`) REFERENCES `users` (`users_userid`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci ROW_FORMAT=DYNAMIC;
SQL
        );

        $this->execute(<<<'SQL'
CREATE TABLE `locations` (
  `locations_id` int(11) NOT NULL AUTO_INCREMENT,
  `locations_name` varchar(500) NOT NULL,
  `clients_id` int(11) DEFAULT NULL,
  `instances_id` int(11) NOT NULL,
  `locations_address` text DEFAULT NULL,
  `locations_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `locations_subOf` int(11) DEFAULT NULL,
  `locations_notes` text DEFAULT NULL,
  `locations_archived` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`locations_id`),
  KEY `locations_clients_clients_id_fk` (`clients_id`),
  KEY `locations_instances_instances_id_fk` (`instances_id`),
  KEY `locations_locations_locations_id_fk` (`locations_subOf`),
  CONSTRAINT `locations_clients_clients_id_fk` FOREIGN KEY (`clients_id`) REFERENCES `clients` (`clients_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `locations_instances_instances_id_fk` FOREIGN KEY (`instances_id`) REFERENCES `instances` (`instances_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `locations_locations_locations_id_fk` FOREIGN KEY (`locations_subOf`) REFERENCES `locations` (`locations_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC;
SQL
        );

        $this->execute(<<<'SQL'
CREATE TABLE `locationsBarcodes` (
  `locationsBarcodes_id` int(11) NOT NULL AUTO_INCREMENT,
  `locations_id` int(11) NOT NULL,
  `locationsBarcodes_value` varchar(500) NOT NULL,
  `locationsBarcodes_type` varchar(500) NOT NULL,
  `locationsBarcodes_notes` text DEFAULT NULL,
  `locationsBarcodes_added` timestamp NOT NULL,
  `users_userid` int(11) DEFAULT NULL COMMENT 'Userid that added it',
  `locationsBarcodes_deleted` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`locationsBarcodes_id`),
  KEY `locationsBarcodes_users_users_userid_fk` (`users_userid`),
  KEY `locationsBarcodes_locations_locations_id_fk` (`locations_id`),
  CONSTRAINT `locationsBarcodes_locations_locations_id_fk` FOREIGN KEY (`locations_id`) REFERENCES `locations` (`locations_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `locationsBarcodes_users_users_userid_fk` FOREIGN KEY (`users_userid`) REFERENCES `users` (`users_userid`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC;
SQL
        );

        $this->execute(<<<'SQL'
CREATE TABLE `loginAttempts` (
  `loginAttempts_id` int(11) NOT NULL AUTO_INCREMENT,
  `loginAttempts_timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `loginAttempts_textEntered` varchar(500) NOT NULL,
  `loginAttempts_ip` varchar(500) DEFAULT NULL,
  `loginAttempts_blocked` tinyint(1) NOT NULL,
  `loginAttempts_successful` tinyint(1) NOT NULL,
  PRIMARY KEY (`loginAttempts_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci ROW_FORMAT=DYNAMIC;
SQL
        );

        $this->execute(<<<'SQL'
CREATE TABLE `maintenanceJobs` (
  `maintenanceJobs_id` int(11) NOT NULL AUTO_INCREMENT,
  `maintenanceJobs_assets` varchar(500) NOT NULL,
  `maintenanceJobs_timestamp_added` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `maintenanceJobs_timestamp_due` timestamp NULL DEFAULT NULL,
  `maintenanceJobs_user_tagged` varchar(500) DEFAULT NULL,
  `maintenanceJobs_user_creator` int(11) NOT NULL,
  `maintenanceJobs_user_assignedTo` int(11) DEFAULT NULL,
  `maintenanceJobs_title` varchar(500) DEFAULT NULL,
  `maintenanceJobs_faultDescription` varchar(500) DEFAULT NULL,
  `maintenanceJobs_priority` tinyint(4) NOT NULL DEFAULT 5 COMMENT '1 to 10',
  `instances_id` int(11) NOT NULL,
  `maintenanceJobs_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `maintenanceJobsStatuses_id` int(11) DEFAULT NULL,
  `maintenanceJobs_flagAssets` tinyint(1) NOT NULL DEFAULT 0,
  `maintenanceJobs_blockAssets` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`maintenanceJobs_id`),
  KEY `maintenanceJobs_users_users_userid_fk` (`maintenanceJobs_user_creator`),
  KEY `maintenanceJobs_instances_instances_id_fk` (`instances_id`),
  CONSTRAINT `maintenanceJobs_instances_instances_id_fk` FOREIGN KEY (`instances_id`) REFERENCES `instances` (`instances_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `maintenanceJobs_users_users_userid_fk` FOREIGN KEY (`maintenanceJobs_user_creator`) REFERENCES `users` (`users_userid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci ROW_FORMAT=DYNAMIC;
SQL
        );

        $this->execute(<<<'SQL'
CREATE TABLE `maintenanceJobsMessages` (
  `maintenanceJobsMessages_id` int(11) NOT NULL AUTO_INCREMENT,
  `maintenanceJobs_id` int(11) DEFAULT NULL,
  `maintenanceJobsMessages_timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `users_userid` int(11) NOT NULL,
  `maintenanceJobsMessages_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `maintenanceJobsMessages_text` text DEFAULT NULL,
  `maintenanceJobsMessages_file` int(11) DEFAULT NULL,
  PRIMARY KEY (`maintenanceJobsMessages_id`),
  KEY `maintenanceJobsMessages___files` (`maintenanceJobsMessages_file`),
  KEY `maintenanceJobsMessages_maintenanceJobs_maintenanceJobs_id_fk` (`maintenanceJobs_id`),
  CONSTRAINT `maintenanceJobsMessages___files` FOREIGN KEY (`maintenanceJobsMessages_file`) REFERENCES `s3files` (`s3files_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `maintenanceJobsMessages_maintenanceJobs_maintenanceJobs_id_fk` FOREIGN KEY (`maintenanceJobs_id`) REFERENCES `maintenanceJobs` (`maintenanceJobs_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci ROW_FORMAT=DYNAMIC;
SQL
        );

        $this->execute(<<<'SQL'
CREATE TABLE `maintenanceJobsStatuses` (
  `maintenanceJobsStatuses_id` int(11) NOT NULL AUTO_INCREMENT,
  `instances_id` int(11) DEFAULT NULL,
  `maintenanceJobsStatuses_name` varchar(200) NOT NULL,
  `maintenanceJobsStatuses_order` tinyint(1) NOT NULL DEFAULT 99,
  `maintenanceJobsStatuses_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `maintenanceJobsStatuses_showJobInMainList` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`maintenanceJobsStatuses_id`),
  KEY `maintenanceJobsStatuses_instances_instances_id_fk` (`instances_id`),
  CONSTRAINT `maintenanceJobsStatuses_instances_instances_id_fk` FOREIGN KEY (`instances_id`) REFERENCES `instances` (`instances_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci ROW_FORMAT=DYNAMIC;
SQL
        );

        $this->execute(<<<'SQL'
CREATE TABLE `manufacturers` (
  `manufacturers_id` int(11) NOT NULL AUTO_INCREMENT,
  `manufacturers_name` varchar(500) NOT NULL,
  `instances_id` int(11) DEFAULT NULL,
  `manufacturers_internalAdamRMSNote` varchar(500) DEFAULT NULL,
  `manufacturers_website` varchar(200) DEFAULT NULL,
  `manufacturers_notes` text DEFAULT NULL,
  PRIMARY KEY (`manufacturers_id`),
  KEY `manufacturers_instances_instances_id_fk` (`instances_id`),
  CONSTRAINT `manufacturers_instances_instances_id_fk` FOREIGN KEY (`instances_id`) REFERENCES `instances` (`instances_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci ROW_FORMAT=DYNAMIC;
SQL
        );

        $this->execute(<<<'SQL'
CREATE TABLE `modules` (
  `modules_id` int(11) NOT NULL AUTO_INCREMENT,
  `instances_id` int(11) NOT NULL,
  `users_userid` int(11) NOT NULL COMMENT '"Author"',
  `modules_name` varchar(500) NOT NULL,
  `modules_description` text DEFAULT NULL,
  `modules_learningObjectives` text DEFAULT NULL,
  `modules_visibleToGroups` varchar(255) DEFAULT NULL,
  `modules_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `modules_show` tinyint(1) NOT NULL DEFAULT 0,
  `modules_thumbnail` int(11) DEFAULT NULL,
  `modules_type` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`modules_id`),
  KEY `modules_instances_instances_id_fk` (`instances_id`),
  KEY `modules_users_users_userid_fk` (`users_userid`),
  KEY `modules_s3files_s3files_id_fk` (`modules_thumbnail`),
  CONSTRAINT `modules_instances_instances_id_fk` FOREIGN KEY (`instances_id`) REFERENCES `instances` (`instances_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `modules_s3files_s3files_id_fk` FOREIGN KEY (`modules_thumbnail`) REFERENCES `s3files` (`s3files_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `modules_users_users_userid_fk` FOREIGN KEY (`users_userid`) REFERENCES `users` (`users_userid`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC;
SQL
        );

        $this->execute(<<<'SQL'
CREATE TABLE `modulesSteps` (
  `modulesSteps_id` int(11) NOT NULL AUTO_INCREMENT,
  `modules_id` int(11) NOT NULL,
  `modulesSteps_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `modulesSteps_show` tinyint(1) NOT NULL DEFAULT 1,
  `modulesSteps_name` varchar(500) NOT NULL,
  `modulesSteps_type` tinyint(1) NOT NULL,
  `modulesSteps_content` longtext DEFAULT NULL,
  `modulesSteps_completionTime` int(11) DEFAULT 0,
  `modulesSteps_internalNotes` longtext DEFAULT NULL,
  `modulesSteps_order` int(11) NOT NULL DEFAULT 999,
  `modulesSteps_locked` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'When set this is a like system level step that can''t be edited',
  PRIMARY KEY (`modulesSteps_id`),
  KEY `modulesSteps_modules_modules_id_fk` (`modules_id`),
  CONSTRAINT `modulesSteps_modules_modules_id_fk` FOREIGN KEY (`modules_id`) REFERENCES `modules` (`modules_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC;
SQL
        );

        $this->execute(<<<'SQL'
CREATE TABLE `passwordResetCodes` (
  `passwordResetCodes_id` int(11) NOT NULL AUTO_INCREMENT,
  `passwordResetCodes_code` varchar(1000) NOT NULL,
  `passwordResetCodes_used` tinyint(1) NOT NULL DEFAULT 0,
  `passwordResetCodes_timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `passwordResetCodes_valid` int(11) NOT NULL DEFAULT 1,
  `users_userid` int(11) NOT NULL,
  PRIMARY KEY (`passwordResetCodes_id`),
  KEY `passwordResetCodes_users_users_userid_fk` (`users_userid`),
  CONSTRAINT `passwordResetCodes_users_users_userid_fk` FOREIGN KEY (`users_userid`) REFERENCES `users` (`users_userid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci ROW_FORMAT=DYNAMIC;
SQL
        );

        $this->execute(<<<'SQL'
CREATE TABLE `payments` (
  `payments_id` int(11) NOT NULL AUTO_INCREMENT,
  `payments_amount` int(11) NOT NULL,
  `payments_quantity` int(11) NOT NULL DEFAULT 1,
  `payments_type` tinyint(1) NOT NULL COMMENT '1 = Payment Recieved\n2 = Sales item\n3 = SubHire item\n4 = Staff cost',
  `payments_reference` varchar(500) DEFAULT NULL,
  `payments_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `payments_supplier` varchar(500) DEFAULT NULL,
  `payments_method` varchar(500) DEFAULT NULL,
  `payments_comment` varchar(500) DEFAULT NULL,
  `projects_id` int(11) NOT NULL,
  `payments_deleted` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`payments_id`),
  KEY `payments_projects_projects_id_fk` (`projects_id`),
  CONSTRAINT `payments_projects_projects_id_fk` FOREIGN KEY (`projects_id`) REFERENCES `projects` (`projects_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci ROW_FORMAT=DYNAMIC;
SQL
        );

        $this->execute(<<<'SQL'
CREATE TABLE `positions` (
  `positions_id` int(11) NOT NULL AUTO_INCREMENT,
  `positions_displayName` varchar(255) NOT NULL,
  `positions_positionsGroups` varchar(500) DEFAULT NULL,
  `positions_rank` tinyint(4) NOT NULL DEFAULT 4 COMMENT 'Rank of the position - so that the most senior position for a user is shown as their "main one". 0 is the most senior',
  PRIMARY KEY (`positions_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci ROW_FORMAT=DYNAMIC;
SQL
        );

        $this->execute(<<<'SQL'
CREATE TABLE `positionsGroups` (
  `positionsGroups_id` int(11) NOT NULL AUTO_INCREMENT,
  `positionsGroups_name` varchar(255) NOT NULL,
  `positionsGroups_actions` varchar(10000) DEFAULT NULL,
  PRIMARY KEY (`positionsGroups_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci ROW_FORMAT=DYNAMIC;
SQL
        );

        $this->execute(<<<'SQL'
CREATE TABLE `projects` (
  `projects_id` int(11) NOT NULL AUTO_INCREMENT,
  `projects_name` varchar(500) NOT NULL,
  `instances_id` int(11) NOT NULL,
  `projects_manager` int(11) NOT NULL,
  `projects_description` text DEFAULT NULL,
  `projects_created` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `clients_id` int(11) DEFAULT NULL,
  `projects_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `projects_archived` tinyint(1) NOT NULL DEFAULT 0,
  `projects_dates_use_start` timestamp NULL DEFAULT NULL,
  `projects_dates_use_end` timestamp NULL DEFAULT NULL,
  `projects_dates_deliver_start` timestamp NULL DEFAULT NULL,
  `projects_dates_deliver_end` timestamp NULL DEFAULT NULL,
  `projects_dates_finances_days` smallint(5) unsigned DEFAULT NULL,
  `projects_dates_finances_weeks` smallint(5) unsigned DEFAULT NULL,
  `projectsStatuses_id` int(11) NOT NULL,
  `locations_id` int(11) DEFAULT NULL,
  `projects_invoiceNotes` text DEFAULT NULL,
  `projects_defaultDiscount` double NOT NULL DEFAULT 0,
  `projectsTypes_id` int(11) NOT NULL,
  `projects_parent_project_id` int(11) DEFAULT NULL,
  `projects_status_follow_parent` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`projects_id`),
  KEY `projects_clients_clients_id_fk` (`clients_id`),
  KEY `projects_instances_instances_id_fk` (`instances_id`),
  KEY `projects_users_users_userid_fk` (`projects_manager`),
  KEY `projects_locations_locations_id_fk` (`locations_id`),
  KEY `projects_projectsTypes_projectsTypes_id_fk` (`projectsTypes_id`),
  KEY `projects_parent_project_id` (`projects_parent_project_id`),
  KEY `projects_projectsStatuses_id_projectsStatuses_id_fk` (`projectsStatuses_id`),
  CONSTRAINT `projects_clients_clients_id_fk` FOREIGN KEY (`clients_id`) REFERENCES `clients` (`clients_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `projects_ibfk_1` FOREIGN KEY (`projects_parent_project_id`) REFERENCES `projects` (`projects_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `projects_instances_instances_id_fk` FOREIGN KEY (`instances_id`) REFERENCES `instances` (`instances_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `projects_locations_locations_id_fk` FOREIGN KEY (`locations_id`) REFERENCES `locations` (`locations_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `projects_projectsStatuses_id_projectsStatuses_id_fk` FOREIGN KEY (`projectsStatuses_id`) REFERENCES `projectsStatuses` (`projectsStatuses_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `projects_users_users_userid_fk` FOREIGN KEY (`projects_manager`) REFERENCES `users` (`users_userid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci ROW_FORMAT=DYNAMIC;
SQL
        );

        $this->execute(<<<'SQL'
CREATE TABLE `projectsFinanceCache` (
  `projectsFinanceCache_id` int(11) NOT NULL AUTO_INCREMENT,
  `projects_id` int(11) NOT NULL,
  `projectsFinanceCache_timestamp` timestamp NOT NULL,
  `projectsFinanceCache_timestampUpdated` timestamp NULL DEFAULT NULL,
  `projectsFinanceCache_equipmentSubTotal` int(11) DEFAULT NULL,
  `projectsFinanceCache_equiptmentDiscounts` int(11) DEFAULT NULL,
  `projectsFinanceCache_equiptmentTotal` int(11) DEFAULT NULL,
  `projectsFinanceCache_salesTotal` int(11) DEFAULT NULL,
  `projectsFinanceCache_staffTotal` int(11) DEFAULT NULL,
  `projectsFinanceCache_externalHiresTotal` int(11) DEFAULT NULL,
  `projectsFinanceCache_paymentsReceived` int(11) DEFAULT NULL,
  `projectsFinanceCache_grandTotal` int(11) DEFAULT NULL,
  `projectsFinanceCache_value` int(11) DEFAULT NULL,
  `projectsFinanceCache_mass` decimal(55,5) DEFAULT NULL,
  PRIMARY KEY (`projectsFinanceCache_id`),
  KEY `projectsFinanceCache_projects_projects_id_fk` (`projects_id`),
  KEY `projectFinnaceCacheTimestamp` (`projectsFinanceCache_timestamp`),
  CONSTRAINT `projectsFinanceCache_projects_projects_id_fk` FOREIGN KEY (`projects_id`) REFERENCES `projects` (`projects_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC;
SQL
        );

        $this->execute(<<<'SQL'
CREATE TABLE `projectsNotes` (
  `projectsNotes_id` int(11) NOT NULL AUTO_INCREMENT,
  `projectsNotes_title` varchar(200) NOT NULL,
  `projectsNotes_text` text DEFAULT NULL,
  `projectsNotes_userid` int(11) NOT NULL,
  `projects_id` int(11) NOT NULL,
  `projectsNotes_deleted` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`projectsNotes_id`),
  KEY `projectsNotes_projects_projects_id_fk` (`projects_id`),
  KEY `projectsNotes_users_users_userid_fk` (`projectsNotes_userid`),
  CONSTRAINT `projectsNotes_projects_projects_id_fk` FOREIGN KEY (`projects_id`) REFERENCES `projects` (`projects_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `projectsNotes_users_users_userid_fk` FOREIGN KEY (`projectsNotes_userid`) REFERENCES `users` (`users_userid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci ROW_FORMAT=DYNAMIC;
SQL
        );

        $this->execute(<<<'SQL'
CREATE TABLE `projectsStatuses` (
  `projectsStatuses_id` int(11) NOT NULL AUTO_INCREMENT,
  `projectsStatuses_name` varchar(200) NOT NULL,
  `projectsStatuses_description` varchar(9000) NOT NULL,
  `projectsStatuses_fontAwesome` varchar(100) DEFAULT NULL,
  `projectsStatuses_foregroundColour` varchar(200) NOT NULL,
  `projectsStatuses_backgroundColour` varchar(200) NOT NULL,
  `projectsStatuses_rank` int(11) NOT NULL DEFAULT 999,
  `projectsStatuses_assetsReleased` tinyint(1) NOT NULL DEFAULT 0,
  `instances_id` int(11) NOT NULL,
  `projectsStatuses_deleted` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`projectsStatuses_id`),
  KEY `projectsStatuses_instances_instances_id_fk` (`instances_id`),
  CONSTRAINT `projectsStatuses_instances_instances_id_fk` FOREIGN KEY (`instances_id`) REFERENCES `instances` (`instances_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci ROW_FORMAT=DYNAMIC;
SQL
        );

        $this->execute(<<<'SQL'
CREATE TABLE `projectsTypes` (
  `projectsTypes_id` int(11) NOT NULL AUTO_INCREMENT,
  `projectsTypes_name` varchar(200) NOT NULL,
  `instances_id` int(11) NOT NULL,
  `projectsTypes_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `projectsTypes_config_finance` tinyint(1) NOT NULL DEFAULT 1,
  `projectsTypes_config_files` int(11) NOT NULL DEFAULT 1,
  `projectsTypes_config_assets` int(11) NOT NULL DEFAULT 1,
  `projectsTypes_config_client` int(11) NOT NULL DEFAULT 1,
  `projectsTypes_config_venue` int(11) NOT NULL DEFAULT 1,
  `projectsTypes_config_notes` int(11) NOT NULL DEFAULT 1,
  `projectsTypes_config_crew` int(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`projectsTypes_id`),
  KEY `projectsTypes_instances_instances_id_fk` (`instances_id`),
  CONSTRAINT `projectsTypes_instances_instances_id_fk` FOREIGN KEY (`instances_id`) REFERENCES `instances` (`instances_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC;
SQL
        );

        $this->execute(<<<'SQL'
CREATE TABLE `projectsVacantRoles` (
  `projectsVacantRoles_id` int(11) NOT NULL AUTO_INCREMENT,
  `projects_id` int(11) NOT NULL,
  `projectsVacantRoles_name` varchar(500) NOT NULL,
  `projectsVacantRoles_description` text DEFAULT NULL,
  `projectsVacantRoles_personSpecification` text DEFAULT NULL,
  `projectsVacantRoles_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `projectsVacantRoles_open` tinyint(1) NOT NULL DEFAULT 0,
  `projectsVacantRoles_showPublic` tinyint(1) NOT NULL DEFAULT 0,
  `projectsVacantRoles_added` timestamp NOT NULL DEFAULT current_timestamp(),
  `projectsVacantRoles_deadline` timestamp NULL DEFAULT NULL,
  `projectsVacantRoles_firstComeFirstServed` tinyint(1) NOT NULL DEFAULT 0,
  `projectsVacantRoles_fileUploads` tinyint(1) NOT NULL DEFAULT 1,
  `projectsVacantRoles_slots` int(11) NOT NULL DEFAULT 1,
  `projectsVacantRoles_slotsFilled` int(11) NOT NULL DEFAULT 0,
  `projectsVacantRoles_questions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`projectsVacantRoles_questions`)),
  `projectsVacantRoles_collectPhone` tinyint(1) NOT NULL DEFAULT 0,
  `projectsVacantRoles_privateToPM` tinyint(1) NOT NULL DEFAULT 1,
  `projectsVacantRoles_visibleToGroups` varchar(255) DEFAULT NULL,
  `projectsVacantRoles_applicationVisibleToUsers` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`projectsVacantRoles_id`),
  KEY `projectsVacantRoles_projects_projects_id_fk` (`projects_id`),
  CONSTRAINT `projectsVacantRoles_projects_projects_id_fk` FOREIGN KEY (`projects_id`) REFERENCES `projects` (`projects_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC;
SQL
        );

        $this->execute(<<<'SQL'
CREATE TABLE `projectsVacantRolesApplications` (
  `projectsVacantRolesApplications_id` int(11) NOT NULL AUTO_INCREMENT,
  `projectsVacantRoles_id` int(11) NOT NULL,
  `users_userid` int(11) NOT NULL,
  `projectsVacantRolesApplications_files` text DEFAULT NULL,
  `projectsVacantRolesApplications_phone` varchar(255) DEFAULT NULL,
  `projectsVacantRolesApplications_applicantComment` text DEFAULT NULL,
  `projectsVacantRolesApplications_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `projectsVacantRolesApplications_withdrawn` tinyint(1) NOT NULL DEFAULT 0,
  `projectsVacantRolesApplications_submitted` timestamp NOT NULL DEFAULT current_timestamp(),
  `projectsVacantRolesApplications_questionAnswers` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`projectsVacantRolesApplications_questionAnswers`)),
  `projectsVacantRolesApplications_status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '1 = Success\n2 = Rejected',
  PRIMARY KEY (`projectsVacantRolesApplications_id`),
  KEY `projectsVacantRolesApplications_projectsVacantRolesid_fk` (`projectsVacantRoles_id`),
  KEY `projectsVacantRolesApplications_users_users_userid_fk` (`users_userid`),
  CONSTRAINT `projectsVacantRolesApplications_projectsVacantRolesid_fk` FOREIGN KEY (`projectsVacantRoles_id`) REFERENCES `projectsVacantRoles` (`projectsVacantRoles_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `projectsVacantRolesApplications_users_users_userid_fk` FOREIGN KEY (`users_userid`) REFERENCES `users` (`users_userid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC;
SQL
        );

        $this->execute(<<<'SQL'
CREATE TABLE `s3files` (
  `s3files_id` int(11) NOT NULL AUTO_INCREMENT,
  `instances_id` int(11) NOT NULL,
  `s3files_path` varchar(255) DEFAULT NULL COMMENT 'NO LEADING /',
  `s3files_name` varchar(1000) DEFAULT NULL,
  `s3files_filename` varchar(255) NOT NULL,
  `s3files_extension` varchar(255) NOT NULL,
  `s3files_original_name` varchar(500) DEFAULT NULL COMMENT 'What was this file originally called when it was uploaded? For things like file attachments\n',
  `s3files_meta_size` bigint(20) NOT NULL COMMENT 'Size of the file in bytes',
  `s3files_meta_public` tinyint(1) NOT NULL DEFAULT 0,
  `s3files_shareKey` varchar(255) DEFAULT NULL,
  `s3files_meta_type` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0 = undefined\nRest are set out in corehead\n',
  `s3files_meta_subType` int(11) DEFAULT NULL COMMENT 'Depends what it is - each module that uses the file handler will be setting this for themselves',
  `s3files_meta_uploaded` timestamp NOT NULL DEFAULT current_timestamp(),
  `users_userid` int(11) DEFAULT NULL COMMENT 'Who uploaded it?',
  `s3files_meta_deleteOn` timestamp NULL DEFAULT NULL COMMENT 'Delete this file on this set date (basically if you hit delete we will kill it after say 30 days)',
  `s3files_meta_physicallyStored` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'If we have the file it''s 1 - if we deleted it it''s 0 but the "deleteOn" is set. If we lost it it''s 0 with a null "delete on"',
  PRIMARY KEY (`s3files_id`),
  KEY `s3files_instances_instances_id_fk` (`instances_id`),
  KEY `s3files_users_users_userid_fk` (`users_userid`),
  CONSTRAINT `s3files_instances_instances_id_fk` FOREIGN KEY (`instances_id`) REFERENCES `instances` (`instances_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `s3files_users_users_userid_fk` FOREIGN KEY (`users_userid`) REFERENCES `users` (`users_userid`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci ROW_FORMAT=DYNAMIC;
SQL
        );

        $this->execute(<<<'SQL'
CREATE TABLE `signupCodes` (
  `signupCodes_id` int(11) NOT NULL AUTO_INCREMENT,
  `signupCodes_name` varchar(200) NOT NULL,
  `instances_id` int(11) NOT NULL,
  `signupCodes_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `signupCodes_valid` tinyint(1) NOT NULL DEFAULT 1,
  `signupCodes_notes` text DEFAULT NULL,
  `signupCodes_role` varchar(500) NOT NULL,
  `instancePositions_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`signupCodes_id`),
  UNIQUE KEY `signupCodes_signupCodes_name_uindex` (`signupCodes_name`),
  KEY `signupCodes_instances_instances_id_fk` (`instances_id`),
  KEY `signupCodes_instancePositions_instancePositions_id_fk` (`instancePositions_id`),
  CONSTRAINT `signupCodes_instancePositions_instancePositions_id_fk` FOREIGN KEY (`instancePositions_id`) REFERENCES `instancePositions` (`instancePositions_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `signupCodes_instances_instances_id_fk` FOREIGN KEY (`instances_id`) REFERENCES `instances` (`instances_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC;
SQL
        );

        $this->execute(<<<'SQL'
CREATE TABLE `userInstances` (
  `userInstances_id` int(11) NOT NULL AUTO_INCREMENT,
  `users_userid` int(11) NOT NULL,
  `instancePositions_id` int(11) NOT NULL,
  `userInstances_extraPermissions` varchar(15000) DEFAULT NULL,
  `userInstances_label` varchar(500) DEFAULT NULL,
  `userInstances_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `signupCodes_id` int(11) DEFAULT NULL,
  `userInstances_archived` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`userInstances_id`),
  KEY `userInstances_instancePositions_instancePositions_id_fk` (`instancePositions_id`),
  KEY `userInstances_users_users_userid_fk` (`users_userid`),
  KEY `userInstances_signupCodes_signupCodes_id_fk` (`signupCodes_id`),
  CONSTRAINT `userInstances_instancePositions_instancePositions_id_fk` FOREIGN KEY (`instancePositions_id`) REFERENCES `instancePositions` (`instancePositions_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `userInstances_signupCodes_signupCodes_id_fk` FOREIGN KEY (`signupCodes_id`) REFERENCES `signupCodes` (`signupCodes_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `userInstances_users_users_userid_fk` FOREIGN KEY (`users_userid`) REFERENCES `users` (`users_userid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci ROW_FORMAT=DYNAMIC;
SQL
        );

        $this->execute(<<<'SQL'
CREATE TABLE `userModules` (
  `userModules_id` int(11) NOT NULL AUTO_INCREMENT,
  `modules_id` int(11) NOT NULL,
  `users_userid` int(11) NOT NULL,
  `userModules_stepsCompleted` varchar(1000) DEFAULT NULL,
  `userModules_currentStep` int(11) DEFAULT NULL,
  `userModules_started` timestamp NOT NULL,
  `userModules_updated` timestamp NOT NULL,
  PRIMARY KEY (`userModules_id`),
  KEY `userModules_modules_modules_id_fk` (`modules_id`),
  KEY `userModules_users_users_userid_fk` (`users_userid`),
  KEY `userModules_modulesSteps_modulesSteps_id_fk` (`userModules_currentStep`),
  CONSTRAINT `userModules_modulesSteps_modulesSteps_id_fk` FOREIGN KEY (`userModules_currentStep`) REFERENCES `modulesSteps` (`modulesSteps_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `userModules_modules_modules_id_fk` FOREIGN KEY (`modules_id`) REFERENCES `modules` (`modules_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `userModules_users_users_userid_fk` FOREIGN KEY (`users_userid`) REFERENCES `users` (`users_userid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC;
SQL
        );

        $this->execute(<<<'SQL'
CREATE TABLE `userModulesCertifications` (
  `userModulesCertifications_id` int(11) NOT NULL AUTO_INCREMENT,
  `modules_id` int(11) NOT NULL,
  `users_userid` int(11) NOT NULL,
  `userModulesCertifications_revoked` tinyint(1) NOT NULL DEFAULT 0,
  `userModulesCertifications_approvedBy` int(11) NOT NULL,
  `userModulesCertifications_approvedComment` varchar(2000) DEFAULT NULL,
  `userModulesCertifications_timestamp` timestamp NOT NULL,
  PRIMARY KEY (`userModulesCertifications_id`),
  KEY `userModulesCertifications_users_users_userid_fk` (`users_userid`),
  KEY `userModulesCertifications_users_users_userid_fk_2` (`userModulesCertifications_approvedBy`),
  KEY `userModulesCertifications_modules_modules_id_fk` (`modules_id`),
  CONSTRAINT `userModulesCertifications_modules_modules_id_fk` FOREIGN KEY (`modules_id`) REFERENCES `modules` (`modules_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `userModulesCertifications_users_users_userid_fk` FOREIGN KEY (`users_userid`) REFERENCES `users` (`users_userid`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `userModulesCertifications_users_users_userid_fk_2` FOREIGN KEY (`userModulesCertifications_approvedBy`) REFERENCES `users` (`users_userid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci ROW_FORMAT=DYNAMIC;
SQL
        );

        $this->execute(<<<'SQL'
CREATE TABLE `userPositions` (
  `userPositions_id` int(11) NOT NULL AUTO_INCREMENT,
  `users_userid` int(11) DEFAULT NULL,
  `userPositions_start` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `userPositions_end` timestamp NULL DEFAULT NULL,
  `positions_id` int(11) DEFAULT NULL COMMENT 'Can be null if you like - as long as you set the relevant other fields',
  `userPositions_displayName` varchar(255) DEFAULT NULL,
  `userPositions_extraPermissions` varchar(500) DEFAULT NULL COMMENT 'Allow a few extra permissions to be added just for this user for that exact permissions term\n',
  `userPositions_show` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`userPositions_id`),
  KEY `userPositions_positions_positions_id_fk` (`positions_id`),
  KEY `userPositions_users_users_userid_fk` (`users_userid`),
  CONSTRAINT `userPositions_positions_positions_id_fk` FOREIGN KEY (`positions_id`) REFERENCES `positions` (`positions_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `userPositions_users_users_userid_fk` FOREIGN KEY (`users_userid`) REFERENCES `users` (`users_userid`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci ROW_FORMAT=DYNAMIC;
SQL
        );

        $this->execute(<<<'SQL'
CREATE TABLE `users` (
  `users_username` varchar(200) DEFAULT NULL,
  `users_name1` varchar(100) DEFAULT NULL,
  `users_name2` varchar(100) DEFAULT NULL,
  `users_userid` int(11) NOT NULL AUTO_INCREMENT,
  `users_salty1` varchar(30) DEFAULT NULL,
  `users_password` varchar(150) DEFAULT NULL,
  `users_salty2` varchar(50) DEFAULT NULL,
  `users_hash` varchar(255) NOT NULL,
  `users_email` varchar(257) DEFAULT NULL,
  `users_created` timestamp NULL DEFAULT current_timestamp() COMMENT 'When user signed up',
  `users_notes` text DEFAULT NULL COMMENT 'Internal Notes - Not visible to user',
  `users_thumbnail` int(11) DEFAULT NULL,
  `users_changepass` tinyint(1) NOT NULL DEFAULT 0,
  `users_termsAccepted` timestamp NULL DEFAULT NULL,
  `users_selectedInstanceIDLast` int(11) DEFAULT NULL COMMENT 'What is the instance ID they most recently selected? This will be the one we use next time they login',
  `users_suspended` tinyint(1) NOT NULL DEFAULT 0,
  `users_deleted` tinyint(1) DEFAULT 0,
  `users_emailVerified` tinyint(1) NOT NULL DEFAULT 0,
  `users_social_facebook` varchar(100) DEFAULT NULL,
  `users_social_twitter` varchar(100) DEFAULT NULL,
  `users_social_instagram` varchar(100) DEFAULT NULL,
  `users_social_linkedin` varchar(100) DEFAULT NULL,
  `users_social_snapchat` varchar(100) DEFAULT NULL,
  `users_calendarHash` varchar(200) DEFAULT NULL,
  `users_widgets` varchar(500) DEFAULT NULL,
  `users_notificationSettings` text DEFAULT NULL,
  `users_assetGroupsWatching` varchar(200) DEFAULT NULL,
  `users_oauth_googleid` varchar(255) DEFAULT NULL,
  `users_oauth_microsoftid` varchar(255) DEFAULT NULL,
  `users_dark_mode` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`users_userid`),
  UNIQUE KEY `users_users_email_uindex` (`users_email`),
  UNIQUE KEY `users_users_username_uindex` (`users_username`),
  KEY `username_2` (`users_userid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci ROW_FORMAT=DYNAMIC;
SQL
        );
        $this->execute('SET FOREIGN_KEY_CHECKS=1;');
    }

    public function down(): void
    {
        $this->execute('SET FOREIGN_KEY_CHECKS=0;');
        $this->execute("DROP TABLE IF EXISTS `users`;");
        $this->execute("DROP TABLE IF EXISTS `userPositions`;");
        $this->execute("DROP TABLE IF EXISTS `userModulesCertifications`;");
        $this->execute("DROP TABLE IF EXISTS `userModules`;");
        $this->execute("DROP TABLE IF EXISTS `userInstances`;");
        $this->execute("DROP TABLE IF EXISTS `signupCodes`;");
        $this->execute("DROP TABLE IF EXISTS `s3files`;");
        $this->execute("DROP TABLE IF EXISTS `projectsVacantRolesApplications`;");
        $this->execute("DROP TABLE IF EXISTS `projectsVacantRoles`;");
        $this->execute("DROP TABLE IF EXISTS `projectsTypes`;");
        $this->execute("DROP TABLE IF EXISTS `projectsStatuses`;");
        $this->execute("DROP TABLE IF EXISTS `projectsNotes`;");
        $this->execute("DROP TABLE IF EXISTS `projectsFinanceCache`;");
        $this->execute("DROP TABLE IF EXISTS `projects`;");
        $this->execute("DROP TABLE IF EXISTS `positionsGroups`;");
        $this->execute("DROP TABLE IF EXISTS `positions`;");
        $this->execute("DROP TABLE IF EXISTS `payments`;");
        $this->execute("DROP TABLE IF EXISTS `passwordResetCodes`;");
        $this->execute("DROP TABLE IF EXISTS `modulesSteps`;");
        $this->execute("DROP TABLE IF EXISTS `modules`;");
        $this->execute("DROP TABLE IF EXISTS `manufacturers`;");
        $this->execute("DROP TABLE IF EXISTS `maintenanceJobsStatuses`;");
        $this->execute("DROP TABLE IF EXISTS `maintenanceJobsMessages`;");
        $this->execute("DROP TABLE IF EXISTS `maintenanceJobs`;");
        $this->execute("DROP TABLE IF EXISTS `loginAttempts`;");
        $this->execute("DROP TABLE IF EXISTS `locationsBarcodes`;");
        $this->execute("DROP TABLE IF EXISTS `locations`;");
        $this->execute("DROP TABLE IF EXISTS `instances`;");
        $this->execute("DROP TABLE IF EXISTS `instancePositions`;");
        $this->execute("DROP TABLE IF EXISTS `emailVerificationCodes`;");
        $this->execute("DROP TABLE IF EXISTS `emailSent`;");
        $this->execute("DROP TABLE IF EXISTS `crewAssignments`;");
        $this->execute("DROP TABLE IF EXISTS `config`;");
        $this->execute("DROP TABLE IF EXISTS `cmsPagesViews`;");
        $this->execute("DROP TABLE IF EXISTS `cmsPagesDrafts`;");
        $this->execute("DROP TABLE IF EXISTS `cmsPages`;");
        $this->execute("DROP TABLE IF EXISTS `clients`;");
        $this->execute("DROP TABLE IF EXISTS `authTokens`;");
        $this->execute("DROP TABLE IF EXISTS `auditLog`;");
        $this->execute("DROP TABLE IF EXISTS `assetsBarcodesScans`;");
        $this->execute("DROP TABLE IF EXISTS `assetsBarcodes`;");
        $this->execute("DROP TABLE IF EXISTS `assetsAssignmentsStatus`;");
        $this->execute("DROP TABLE IF EXISTS `assetsAssignments`;");
        $this->execute("DROP TABLE IF EXISTS `assets`;");
        $this->execute("DROP TABLE IF EXISTS `assetTypes`;");
        $this->execute("DROP TABLE IF EXISTS `assetGroups`;");
        $this->execute("DROP TABLE IF EXISTS `assetCategoriesGroups`;");
        $this->execute("DROP TABLE IF EXISTS `assetCategories`;");
        $this->execute("DROP TABLE IF EXISTS `analyticsEvents`;");
        $this->execute('SET FOREIGN_KEY_CHECKS=1;');
    }
}
