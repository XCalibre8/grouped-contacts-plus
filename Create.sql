-- MySQL dump 10.13  Distrib 5.6.24, for Win32 (x86)
--
-- Host: localhost    Database: knowledge
-- ------------------------------------------------------
-- Server version	5.7.7-rc-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `con_addresses`
--

DROP TABLE IF EXISTS `con_addresses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `con_addresses` (
  `ADR_ID` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'The local address ID',
  `ADR_ID_GLOBAL` int(10) unsigned DEFAULT NULL COMMENT 'The ID in the global database',
  `ADR_ID_SOURCE` int(10) unsigned DEFAULT NULL COMMENT 'The ID in the source DB',
  `ADR_SOURCE_DB_ID` int(10) unsigned DEFAULT NULL COMMENT 'The ID of the source DB',
  `ADR_LINE1` varchar(250) DEFAULT NULL COMMENT 'Address line 1',
  `ADR_LINE2` varchar(250) DEFAULT NULL COMMENT 'Address line 2',
  `ADR_POST_TOWN` varchar(100) DEFAULT NULL COMMENT 'The postal town',
  `ADR_COUNTY` varchar(100) DEFAULT NULL COMMENT 'The county',
  `ADR_CNTRY_ID` int(10) unsigned DEFAULT NULL COMMENT 'The ID link to the ISO country',
  `ADR_POSTCODE` varchar(15) DEFAULT NULL COMMENT 'The postcode',
  `ADR_UPDATED` datetime DEFAULT NULL COMMENT 'The last time the address was updated',
  `ADR_SR` bit(1) NOT NULL DEFAULT b'0' COMMENT 'Suppress/recycle indicator, exclude from searches',
  PRIMARY KEY (`ADR_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8 COMMENT='Address detail storage';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `con_contact_point_types`
--

DROP TABLE IF EXISTS `con_contact_point_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `con_contact_point_types` (
  `CPT_ID` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'The local ID of the contact point type',
  `CPT_ID_GLOBAL` int(10) unsigned DEFAULT NULL COMMENT 'The ID in the global DB',
  `CPT_ID_SOURCE` int(10) unsigned DEFAULT NULL COMMENT 'The ID in the source DB',
  `CPT_SOURCE_DB_ID` int(10) unsigned DEFAULT NULL COMMENT 'The ID of the source DB',
  `CPT_NAME` varchar(50) NOT NULL COMMENT 'The name of the contact point type',
  `CPT_DESCRIPTION` varchar(250) DEFAULT NULL COMMENT 'The description for the contact point type',
  `CPT_UPDATED` datetime DEFAULT NULL COMMENT 'When the contact point type was last updated',
  `CPT_SR` bit(1) NOT NULL DEFAULT b'0' COMMENT 'Suppress/recycle indicator, exclude from searches',
  PRIMARY KEY (`CPT_ID`),
  UNIQUE KEY `CPT_NAME_UNIQUE` (`CPT_NAME`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 COMMENT='The type for the contact point, Telephone, Mobile, Email, Skype etc';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `con_contact_points`
--

DROP TABLE IF EXISTS `con_contact_points`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `con_contact_points` (
  `CNP_ID` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'The contact point ID in the local DB',
  `CNP_ID_GLOBAL` int(10) unsigned DEFAULT NULL COMMENT 'The ID in the global DB',
  `CNP_ID_SOURCE` int(10) unsigned DEFAULT NULL COMMENT 'The ID in the source DB',
  `CNP_SOURCE_DB_ID` int(10) unsigned DEFAULT NULL COMMENT 'The ID of the source DB',
  `CNP_CPT_ID` int(10) unsigned NOT NULL COMMENT 'Link to contact point type by ID (phone, email etc)',
  `CNP_CONTACT` varchar(250) NOT NULL COMMENT 'The actual contact details',
  `CNP_UPDATED` datetime DEFAULT NULL COMMENT 'The last time the contact point was updated',
  `CNP_SR` bit(1) NOT NULL DEFAULT b'0' COMMENT 'Suppress/recycle indicator, exclude from searches',
  PRIMARY KEY (`CNP_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8 COMMENT='Non address contact points';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `con_contact_types`
--

DROP TABLE IF EXISTS `con_contact_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `con_contact_types` (
  `CTT_ID` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'The local ID',
  `CTT_ID_GLOBAL` int(10) unsigned DEFAULT NULL COMMENT 'The ID in the global DB',
  `CTT_ID_SOURCE` int(10) unsigned DEFAULT NULL COMMENT 'The ID in the source DB',
  `CTT_SOURCE_DB_ID` int(10) unsigned DEFAULT NULL COMMENT 'The ID of the source DB',
  `CTT_NAME` varchar(50) NOT NULL COMMENT 'The name for the contact type',
  `CTT_DESCRIPTION` varchar(250) DEFAULT NULL COMMENT 'The description for the contact type',
  `CTT_UPDATED` datetime DEFAULT NULL COMMENT 'The date the contact type was last updated',
  `CTT_SR` bit(1) NOT NULL DEFAULT b'0' COMMENT 'Suppress/recycle indicator, exclude from searches',
  PRIMARY KEY (`CTT_ID`),
  UNIQUE KEY `CTT_NAME_UNIQUE` (`CTT_NAME`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COMMENT='The type of the contact, Home, Work etc';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `con_counties`
--

DROP TABLE IF EXISTS `con_counties`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `con_counties` (
  `CNTY_ID` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Local ID',
  `CNTY_ID_GLOBAL` int(10) unsigned DEFAULT NULL COMMENT 'ID in the global DB',
  `CNTY_ID_SOURCE` int(10) unsigned DEFAULT NULL COMMENT 'ID in the source DB',
  `CNTY_SOURCE_DB_ID` int(10) unsigned DEFAULT NULL COMMENT 'ID of the source DB',
  `CNTY_NAME` varchar(50) NOT NULL COMMENT 'County name',
  `CNTY_UPDATED` datetime DEFAULT NULL COMMENT 'When the county record was last updated',
  `CNTY_SR` bit(1) NOT NULL DEFAULT b'0' COMMENT 'Suppress/recycle indicator, exclude from searches',
  PRIMARY KEY (`CNTY_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=42 DEFAULT CHARSET=utf8 COMMENT='Counties';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `con_countries`
--

DROP TABLE IF EXISTS `con_countries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `con_countries` (
  `CNTRY_ID` int(11) NOT NULL COMMENT 'The ISO integer country',
  `CNTRY_NAME` varchar(100) NOT NULL COMMENT 'The ISO country name',
  `CNTRY_A2C` varchar(2) NOT NULL COMMENT 'The ISO country 2 char code',
  `CNTRY_A3C` varchar(3) NOT NULL COMMENT 'The ISO country 3 char code',
  `CNTRY_UPDATED` datetime DEFAULT NULL COMMENT 'The last time the country record was updated',
  PRIMARY KEY (`CNTRY_ID`),
  UNIQUE KEY `CNTRY_ID_UNIQUE` (`CNTRY_ID`),
  UNIQUE KEY `CNTRY_NAME_UNIQUE` (`CNTRY_NAME`),
  UNIQUE KEY `CNTRY_A2C_UNIQUE` (`CNTRY_A2C`),
  UNIQUE KEY `CNTRY_A3C_UNIQUE` (`CNTRY_A3C`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='ISO country codes';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `con_people`
--

DROP TABLE IF EXISTS `con_people`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `con_people` (
  `PER_ID` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Person identifier',
  `PER_ID_GLOBAL` int(10) unsigned DEFAULT NULL COMMENT 'The persons ID in the global database',
  `PER_ID_SOURCE` int(10) unsigned DEFAULT NULL COMMENT 'The persons ID in the source database',
  `PER_SOURCE_DB_ID` int(10) unsigned DEFAULT NULL COMMENT 'The ID of the souce database',
  `PER_TIT_ID` int(10) unsigned DEFAULT NULL COMMENT 'Link to title by ID',
  `PER_FORENAMES` varchar(200) DEFAULT NULL COMMENT 'Persons forenames',
  `PER_SURNAME` varchar(100) DEFAULT NULL COMMENT 'Persons surname',
  `PER_ADR_ID` int(10) unsigned DEFAULT NULL COMMENT 'Link to persons address by ID',
  `PER_CNP_ID` int(10) unsigned DEFAULT NULL COMMENT 'Link to primary contact point by ID',
  `PER_DOB` datetime DEFAULT NULL COMMENT 'The persons date of birth',
  `PER_UPDATED` datetime DEFAULT NULL COMMENT 'Last time the person record was updated',
  `PER_SR` bit(1) NOT NULL DEFAULT b'0' COMMENT 'Suppress/recycle indicator, exclude from searches',
  PRIMARY KEY (`PER_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8 COMMENT='Stores people for the contacts system';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `con_titles`
--

DROP TABLE IF EXISTS `con_titles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `con_titles` (
  `TIT_ID` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'The title ID in the local DB',
  `TIT_ID_GLOBAL` int(10) unsigned DEFAULT NULL COMMENT 'The title ID in the global DB',
  `TIT_ID_SOURCE` int(10) unsigned DEFAULT NULL COMMENT 'The title ID in the source DB',
  `TIT_SOURCE_DB_ID` int(10) unsigned DEFAULT NULL COMMENT 'The ID of the source DB',
  `TIT_TITLE` varchar(50) NOT NULL COMMENT 'A title for a person, Mr, Miss, Lord, Admiral of the Fleet etc',
  `TIT_DESCRIPTION` varchar(250) DEFAULT NULL COMMENT 'The description of the titles meaning',
  `TIT_UPDATED` datetime DEFAULT NULL COMMENT 'When the title record was last updated',
  `TIT_SR` bit(1) NOT NULL DEFAULT b'0' COMMENT 'Suppress/recycle indicator, exclude from searches',
  PRIMARY KEY (`TIT_ID`),
  UNIQUE KEY `TIT_TITLE_UNIQUE` (`TIT_TITLE`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COMMENT='Storage of possible titles and their descriptions';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `grp_group_type_avail_links`
--

DROP TABLE IF EXISTS `grp_group_type_avail_links`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `grp_group_type_avail_links` (
  `GTA_GTP_ID` int(11) unsigned NOT NULL COMMENT 'The group type which needs specific link types',
  `GTA_LTP_ID` int(11) unsigned NOT NULL COMMENT 'The group link type available to the group type',
  `GTA_UPDATED` datetime DEFAULT NULL COMMENT 'When the availability link was last updated',
  PRIMARY KEY (`GTA_GTP_ID`,`GTA_LTP_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='What link types are available to the group type of those possible, if nothing, everything.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `grp_group_types`
--

DROP TABLE IF EXISTS `grp_group_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `grp_group_types` (
  `GTP_ID` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'The local ID for the group type',
  `GTP_ID_GLOBAL` int(10) unsigned DEFAULT NULL COMMENT 'The global ID for the group type',
  `GTP_ID_SOURCE` int(10) unsigned DEFAULT NULL COMMENT 'The ID of the group type in the source DB',
  `GTP_SOURCE_DB_ID` int(10) unsigned DEFAULT NULL COMMENT 'The ID of the group type in the source DB',
  `GTP_NAME` varchar(50) NOT NULL COMMENT 'The name of the group type',
  `GTP_DESCRIPTION` varchar(250) DEFAULT NULL COMMENT 'The description of the group type',
  `GTP_UPDATED` datetime DEFAULT NULL COMMENT 'When the group type was last updated',
  `GTP_SR` bit(1) NOT NULL DEFAULT b'0' COMMENT 'Suppress/recycle indicator, exclude from searches',
  PRIMARY KEY (`GTP_ID`),
  UNIQUE KEY `GTP_NAME_UNIQUE` (`GTP_NAME`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='Stores group types so groups can control what they can link to by type';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `grp_groups`
--

DROP TABLE IF EXISTS `grp_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `grp_groups` (
  `GRP_ID` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'The groups local ID',
  `GRP_ID_GLOBAL` int(10) unsigned DEFAULT NULL COMMENT 'The groups ID in the global DB',
  `GRP_ID_SOURCE` int(10) unsigned DEFAULT NULL COMMENT 'The groups ID in the source DB',
  `GRP_SOURCE_DB_ID` int(10) unsigned DEFAULT NULL COMMENT 'The ID of the source DB',
  `GRP_GTP_ID` int(10) unsigned NOT NULL COMMENT 'The groups type ID link',
  `GRP_NAME` varchar(50) NOT NULL COMMENT 'The name of the group',
  `GRP_DESCRIPTION` varchar(250) DEFAULT NULL COMMENT 'The groups description',
  `GRP_DOCUMENT` text COMMENT 'Unlimited size text based field for extended group information',
  `GRP_UPDATED` datetime DEFAULT NULL COMMENT 'When the group record was last updated',
  `GRP_SR` varchar(45) NOT NULL DEFAULT '0' COMMENT 'Suppress/recycle indicator, exclude from searches',
  PRIMARY KEY (`GRP_ID`),
  UNIQUE KEY `GRP_NAME_UNIQUE` (`GRP_NAME`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COMMENT='User defined groups to organise records';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `lnk_link_type_avail`
--

DROP TABLE IF EXISTS `lnk_link_type_avail`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lnk_link_type_avail` (
  `LTA_ONR_LTP_ID` int(10) unsigned NOT NULL COMMENT 'The link type ID of the linking/owner record',
  `LTA_CHD_LTP_ID` int(10) unsigned NOT NULL COMMENT 'The link type ID of the linked/child type',
  `LTA_DESCRIPTION` varchar(250) DEFAULT NULL COMMENT 'The description of what the link type allows to be linked',
  `LTA_UPDATED` datetime DEFAULT NULL COMMENT 'When the link type availability was last updated',
  `LTA_SR` bit(1) NOT NULL DEFAULT b'0' COMMENT 'Suppress/Recycle indicator, exclude from searches',
  PRIMARY KEY (`LTA_CHD_LTP_ID`,`LTA_ONR_LTP_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Table cross references what can link to what within the system';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `lnk_link_types`
--

DROP TABLE IF EXISTS `lnk_link_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lnk_link_types` (
  `LTP_ID` int(10) unsigned NOT NULL COMMENT 'The local ID of the link type',
  `LTP_ID_GLOBAL` int(10) unsigned DEFAULT NULL COMMENT 'The link types global ID',
  `LTP_ID_SOURCE` int(10) unsigned DEFAULT NULL COMMENT 'The ID of the link type in the source DB',
  `LTP_SOURCE_DB_ID` int(10) unsigned DEFAULT NULL,
  `LTP_NAME` varchar(50) NOT NULL COMMENT 'The name for the link type',
  `LTP_TABLE` varchar(100) DEFAULT NULL COMMENT 'The table the link type refers to',
  `LTP_ID_FIELD` varchar(100) DEFAULT NULL COMMENT 'The ID field name in the link table',
  `LTP_UPDATED` datetime DEFAULT NULL COMMENT 'When the link type record was last updated',
  PRIMARY KEY (`LTP_ID`),
  UNIQUE KEY `LTP_NAME_UNIQUE` (`LTP_NAME`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='A table to hold ID''s for link types and what tables and id fields they refer to';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `lnk_links`
--

DROP TABLE IF EXISTS `lnk_links`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lnk_links` (
  `LNK_ONR_LTP_ID` int(10) unsigned NOT NULL COMMENT 'The link type ID of the linking/owner record',
  `LNK_ONR_ID` int(10) unsigned NOT NULL COMMENT 'The record ID of the linking/owner record',
  `LNK_CHD_LTP_ID` int(10) unsigned NOT NULL COMMENT 'The link type ID of the linked/child record',
  `LNK_CHD_ID` int(10) unsigned NOT NULL COMMENT 'The ID of the linked/child record',
  `LNK_X_ID` int(10) unsigned DEFAULT NULL COMMENT 'An additional ID or unsigned INT field links can use',
  `LNK_X_STR` varchar(100) DEFAULT NULL COMMENT '100 string characters link types can make use of.',
  `LNK_UPDATED` datetime DEFAULT NULL COMMENT 'When the link record was last updated',
  PRIMARY KEY (`LNK_ONR_LTP_ID`,`LNK_ONR_ID`,`LNK_CHD_LTP_ID`,`LNK_CHD_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='This table holds links between system records';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pro_task_states`
--

DROP TABLE IF EXISTS `pro_task_states`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pro_task_states` (
  `TSS_ID` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'The local status ID',
  `TSS_ID_GLOBAL` int(10) unsigned DEFAULT NULL COMMENT 'The global status ID',
  `TSS_ID_SOURCE` int(10) unsigned DEFAULT NULL COMMENT 'The status ID in the source DB',
  `TSS_SOURCE_DB_ID` int(10) unsigned DEFAULT NULL COMMENT 'The ID of the source DB',
  `TSS_NAME` varchar(50) NOT NULL COMMENT 'The name of the state for selection',
  `TSS_DESCRIPTION` varchar(250) DEFAULT NULL COMMENT 'The task state description',
  `TSS_COMPLETE` bit(1) NOT NULL DEFAULT b'0' COMMENT 'Whether this status counts as the task being complete',
  `TSS_UPDATED` datetime DEFAULT NULL COMMENT 'When the task status record was last updated',
  `TSS_SR` bit(1) NOT NULL DEFAULT b'0' COMMENT 'Suppress/recycle indicator, exlcude from searches',
  PRIMARY KEY (`TSS_ID`),
  UNIQUE KEY `TSS_NAME_UNIQUE` (`TSS_NAME`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COMMENT='A table holding the possible states for tasks to be in';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pro_task_type_avail_states`
--

DROP TABLE IF EXISTS `pro_task_type_avail_states`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pro_task_type_avail_states` (
  `TTA_TST_ID` int(11) unsigned NOT NULL COMMENT 'The task type ID to set the available states for',
  `TTA_TSS_ID` int(11) unsigned NOT NULL COMMENT 'The available task status ID',
  `TTA_UPDATED` datetime DEFAULT NULL COMMENT 'When the task/state relationship was last updated',
  PRIMARY KEY (`TTA_TST_ID`,`TTA_TSS_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Assign specific states to task types to control what states are possible for each type, no assigned states will leave all states available';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pro_task_type_avail_types`
--

DROP TABLE IF EXISTS `pro_task_type_avail_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pro_task_type_avail_types` (
  `TTT_TST_ID` int(10) unsigned NOT NULL COMMENT 'The local ID of the task type which has a set list of sub types',
  `TTT_SUB_TST_ID` int(10) unsigned NOT NULL COMMENT 'The local ID of the selectable sub type',
  `TTT_UPDATED` datetime DEFAULT NULL COMMENT 'When the availability record was last updated',
  PRIMARY KEY (`TTT_TST_ID`,`TTT_SUB_TST_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Limits the selection of subtypes available under this type. If nothing available all types available if sub types supported';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pro_task_types`
--

DROP TABLE IF EXISTS `pro_task_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pro_task_types` (
  `TST_ID` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'The task type local ID',
  `TST_ID_GLOBAL` int(10) unsigned DEFAULT NULL COMMENT 'The task type global ID',
  `TST_ID_SOURCE` int(10) unsigned DEFAULT NULL COMMENT 'The task type source ID',
  `TST_SOURCE_DB_ID` int(10) unsigned DEFAULT NULL COMMENT 'The source DB ID',
  `TST_NAME` varchar(50) NOT NULL COMMENT 'The task type name',
  `TST_DESCRIPTION` varchar(250) DEFAULT NULL COMMENT 'Task type description',
  `TST_SUPPORT_SUB` bit(1) NOT NULL DEFAULT b'0' COMMENT 'Whether or not tasks of this type can have sub tasks',
  `TST_UPDATED` datetime DEFAULT NULL COMMENT 'When the task type was last updated',
  `TST_SR` bit(1) NOT NULL DEFAULT b'0' COMMENT 'Suppress/recycle indicator, exclude from searches',
  PRIMARY KEY (`TST_ID`),
  UNIQUE KEY `TST_NAME_UNIQUE` (`TST_NAME`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='Holds different types of tasks in a nested structure';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `pro_tasks`
--

DROP TABLE IF EXISTS `pro_tasks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `pro_tasks` (
  `TSK_ID` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'The local ID for the task',
  `TSK_ID_GLOBAL` int(10) unsigned DEFAULT NULL COMMENT 'The tasks global ID',
  `TSK_ID_SOURCE` int(10) unsigned DEFAULT NULL COMMENT 'The ID for the task in the source DB',
  `TSK_SOURCE_DB_ID` int(10) unsigned DEFAULT NULL COMMENT 'The ID of the tasks source DB',
  `TSK_TST_ID` int(10) unsigned NOT NULL COMMENT 'The local ID of the task type for the task',
  `TSK_TSK_ID` int(10) unsigned DEFAULT NULL COMMENT 'The parent task ID for nested tasks',
  `TSK_TITLE` varchar(100) NOT NULL COMMENT 'The name of the task, title just fits better as it can be name or something more descriptive',
  `TSK_DESCRIPTION` varchar(250) DEFAULT NULL COMMENT 'A quick description summary of the task',
  `TSK_DOCUMENT` text COMMENT 'Unlimited size text based document for task details',
  `TSK_REPEATABLE` bit(1) NOT NULL DEFAULT b'0' COMMENT 'Whether or not the task will be used often or is a one off',
  `TSK_UPDATED` datetime DEFAULT NULL COMMENT 'When the task was last updated',
  `TSK_SR` bit(1) NOT NULL DEFAULT b'0' COMMENT 'Suppress/recycle indicator, exclude from searches',
  PRIMARY KEY (`TSK_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 COMMENT='Holds tasks in a self referential nested structure';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `rqf_fulfillment_levels`
--

DROP TABLE IF EXISTS `rqf_fulfillment_levels`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rqf_fulfillment_levels` (
  `FLL_ID` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'The local ID of the fulfillment level',
  `FLL_ID_GLOBAL` int(10) unsigned DEFAULT NULL COMMENT 'The level ID in the global DB',
  `FLL_ID_SOURCE` int(10) unsigned DEFAULT NULL COMMENT 'The level ID in the source DB',
  `FLL_SOURCE_DB_ID` int(10) unsigned DEFAULT NULL COMMENT 'The ID of the source DB',
  `FLL_FLM_ID` int(10) unsigned NOT NULL COMMENT 'The local fulfillment ID',
  `FLL_LEVEL` smallint(5) unsigned DEFAULT NULL COMMENT 'The level/order of the fulfillment level.',
  `FLL_NAME` varchar(50) NOT NULL COMMENT 'The name of the level',
  `FLL_DESCRIPTION` varchar(250) DEFAULT NULL COMMENT 'A description of the level',
  `FLL_UPDATED` datetime DEFAULT NULL COMMENT 'When the level record was last updated',
  `FLL_SR` bit(1) NOT NULL DEFAULT b'0' COMMENT 'Suppress/recycle indicator, exclude from searches',
  PRIMARY KEY (`FLL_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COMMENT='Allows definition and control of multi-level fulfillments (qualifications etc)';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `rqf_fulfillment_providers`
--

DROP TABLE IF EXISTS `rqf_fulfillment_providers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rqf_fulfillment_providers` (
  `FLP_ID` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'The local ID of the fulfillment provider',
  `FLP_ID_GLOBAL` int(10) unsigned DEFAULT NULL COMMENT 'The ID in the global DB',
  `FLP_ID_SOURCE` int(10) unsigned DEFAULT NULL COMMENT 'The ID in the source DB',
  `FLP_SOURCE_DB_ID` int(10) unsigned DEFAULT NULL COMMENT 'The local ID of the source DB',
  `FLP_FLT_ID` int(11) unsigned NOT NULL COMMENT 'The local fulfillment type ID',
  `FLP_LINK_ID` int(11) unsigned NOT NULL COMMENT 'The ID of the provider record',
  `FLP_FLM_ID` int(11) unsigned NOT NULL COMMENT 'The ID of the fulfillment provided',
  `FLP_FLL_ID` int(10) unsigned DEFAULT NULL COMMENT 'The fulfillment level ID',
  `FLP_REFERENCE` varchar(250) DEFAULT NULL COMMENT 'A reference for the fulfillment, free text or governing board and certificate/qualification number',
  `FLP_ACQUIRED` datetime DEFAULT NULL COMMENT 'When the provider became able to fulfill.',
  `FLP_EXPIRES` datetime DEFAULT NULL COMMENT 'When the providers ability to fulfill expires',
  `FLP_UPDATED` datetime DEFAULT NULL COMMENT 'When the provider record was last updated',
  `FLP_SR` bit(1) NOT NULL DEFAULT b'0' COMMENT 'Suppress/recycle indicator, exclude from searches.',
  PRIMARY KEY (`FLP_ID`),
  KEY `FLP_REQSEARCH_IDX` (`FLP_FLT_ID`,`FLP_FLM_ID`,`FLP_FLL_ID`) COMMENT 'Optimises searches for a levelled fulfillment.',
  KEY `FLP_PROSEARCH_IDX` (`FLP_FLT_ID`,`FLP_LINK_ID`) COMMENT 'Optimises searches for a typed provider.'
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='Links items to fulfillments as providers.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `rqf_fulfillment_types`
--

DROP TABLE IF EXISTS `rqf_fulfillment_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rqf_fulfillment_types` (
  `FLT_ID` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'The local ID of the fulfillment type',
  `FLT_ID_GLOBAL` int(10) unsigned DEFAULT NULL COMMENT 'The ID in the global DB',
  `FLT_ID_SOURCE` int(10) unsigned DEFAULT NULL COMMENT 'The ID in the source DB',
  `FLT_SOURCE_DB_ID` int(10) unsigned DEFAULT NULL COMMENT 'The ID of the source DB',
  `FLT_NAME` varchar(50) NOT NULL COMMENT 'The name of the fulfillment type',
  `FLT_DESCRIPTION` varchar(250) DEFAULT NULL COMMENT 'The description of the fulfillment type',
  `FLT_FOR_LTP_ID` int(10) unsigned DEFAULT NULL COMMENT 'The link type which the fulfillment type applies to.',
  `FLT_UPDATED` datetime DEFAULT NULL COMMENT 'When the fulfillment type record was last updated',
  `FLT_SR` bit(1) NOT NULL DEFAULT b'0' COMMENT 'Suppress/recycle indicator, exclude from searches',
  PRIMARY KEY (`FLT_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COMMENT='Holds types for the requirement fulfillment system';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `rqf_fulfillments`
--

DROP TABLE IF EXISTS `rqf_fulfillments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rqf_fulfillments` (
  `FLM_ID` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'The local ID of the fulfillment',
  `FLM_ID_GLOBAL` int(10) unsigned DEFAULT NULL COMMENT 'The global ID of the fulfillment',
  `FLM_ID_SOURCE` int(10) unsigned DEFAULT NULL COMMENT 'The ID of the fulfillment in the source DB',
  `FLM_SOURCE_DB_ID` int(10) unsigned DEFAULT NULL COMMENT 'The local ID of the source DB',
  `FLM_FLT_ID` int(10) unsigned DEFAULT NULL COMMENT 'The ID of the fulfillment type',
  `FLM_NAME` varchar(50) NOT NULL COMMENT 'The name for the fulfillment',
  `FLM_DESCRIPTION` varchar(250) DEFAULT NULL COMMENT 'The fulfillments description',
  `FLM_MODE` int(10) unsigned DEFAULT NULL COMMENT 'What mode the fulfillment should use. 0=Provider. (Link ID ignored). 1=Task State. (Link ID = Task ID). 2=Fulfillment Chain. (Link ID = Fulfillment ID)',
  `FLM_LINK_ID` int(10) unsigned DEFAULT NULL COMMENT 'The ID of the task or chained fulfillment dependent on mode.',
  `FLM_UPDATED` datetime DEFAULT NULL COMMENT 'When the fulfillment was last updated',
  `FLM_SR` bit(1) NOT NULL DEFAULT b'0' COMMENT 'Suppress/recycle indicator, exclude from searches',
  PRIMARY KEY (`FLM_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='Holds fulfillments for the requirement fulfillment system.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `rqf_requirements`
--

DROP TABLE IF EXISTS `rqf_requirements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rqf_requirements` (
  `REQ_ID` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'The local ID of the requirement',
  `REQ_ID_GLOBAL` int(11) unsigned DEFAULT NULL COMMENT 'The ID of the requirement in the global DB',
  `REQ_ID_SOURCE` int(11) unsigned DEFAULT NULL COMMENT 'The ID of the requirement in the source DB',
  `REQ_SOURCE_DB_ID` int(11) unsigned DEFAULT NULL COMMENT 'The local ID of the source DB',
  `REQ_ONR_LTP_ID` int(10) unsigned NOT NULL COMMENT 'The link type that has the requirement',
  `REQ_ONR_ID` int(10) unsigned NOT NULL COMMENT 'The ID of the link record of type that has the requirement',
  `REQ_CHD_LTP_ID` int(10) unsigned NOT NULL COMMENT 'The link type the requirement applies to',
  `REQ_FLT_ID` int(10) unsigned DEFAULT NULL COMMENT 'The fulfillment type required.',
  `REQ_FLM_ID` int(10) unsigned NOT NULL COMMENT 'The fulfillment required',
  `REQ_MIN_FLL_ID` int(10) unsigned DEFAULT NULL COMMENT 'The minimum level the provider must provide at to meet criteria',
  `REQ_COUNT` int(10) unsigned DEFAULT NULL COMMENT 'How many records must provide the fulfillment, 0 for all of them.',
  `REQ_UPDATED` datetime DEFAULT NULL COMMENT 'When the requirement was last updated.',
  `REQ_SR` bit(1) NOT NULL DEFAULT b'0' COMMENT 'Suppress/recycle indicator, exclude from searches.',
  PRIMARY KEY (`REQ_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='Allows records supported by the link system to have requirements for fulfillment.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sas_aspect_elements`
--

DROP TABLE IF EXISTS `sas_aspect_elements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sas_aspect_elements` (
  `ASE_ID` int(10) unsigned NOT NULL COMMENT 'The ID in the local DB',
  `ASE_ID_GLOBAL` int(10) unsigned DEFAULT NULL COMMENT 'The ID in the GLOBAL DB',
  `ASE_ID_SOURCE` int(10) unsigned DEFAULT NULL COMMENT 'The ID in the source DB',
  `ASE_SOURCE_DB_ID` int(10) unsigned DEFAULT NULL COMMENT 'The local ID of the source DB',
  `ASE_ASP_ID` int(10) unsigned DEFAULT NULL COMMENT 'The aspect this element belongs to',
  `ASE_ASE_ID` int(10) unsigned DEFAULT NULL COMMENT 'The parent element in a multi level object type',
  `ASE_LEVEL` smallint(5) unsigned DEFAULT NULL COMMENT 'The elements level. Level 0 are the object types ''fields''. If the element can have multiples of a sub-element then that element will have elements linked to it at level 1. e.g. Object Type Person could have level 0 elements of Name, Parents and Children. Parents and Children could each have level 1 Elements of Person.',
  `ASE_VARIANT_ASE_ID` int(10) unsigned DEFAULT NULL COMMENT 'Filled if this element is inherited from a variant aspects element',
  `ASE_NO` smallint(5) unsigned NOT NULL COMMENT 'Number for ordering of elements. Can be set on variant elements',
  `ASE_NAME` varchar(50) NOT NULL COMMENT 'The name of the element',
  `ASE_DESCRIPTION` varchar(250) DEFAULT NULL COMMENT 'The description of the elements purpose',
  `ASE_HIDDEN` bit(1) NOT NULL DEFAULT b'0' COMMENT 'Prevents the element from being displayed by default. Can be set on variants',
  `ASE_REQUIRED` bit(1) NOT NULL DEFAULT b'0' COMMENT 'Indicates the element is required for the aspect to the saved/complete, defaults storage field to not null.',
  `ASE_CDT_ID` int(10) unsigned NOT NULL COMMENT 'A link to the elements core data type',
  `ASE_EDITMASK` varchar(30) DEFAULT NULL COMMENT 'Allows an edit mask to be applied to data entry for the element',
  `ASE_LOOKUP_ASP_ID` int(10) unsigned DEFAULT NULL COMMENT 'The aspect ID to get lookup values from',
  `ASE_LOOKUP_ASE_ID` int(10) unsigned DEFAULT NULL COMMENT 'The key element ID to get lookup values from.',
  `ASE_LOOKUP_KEY_ASE_ID` int(10) unsigned DEFAULT NULL COMMENT 'If the ASE_ID is a table data type, the key element of that element to get values from',
  `ASE_LOOKUP_LEVEL` int(10) unsigned DEFAULT NULL COMMENT 'The level of the lookup element',
  `ASE_LOOKUP_DISPLAY_ASE_ID` int(10) unsigned DEFAULT NULL COMMENT 'The element values to display to the user. Leave blank for a popup grid selection form',
  `ASE_LOOKUP_ONLY` bit(1) NOT NULL DEFAULT b'0' COMMENT 'Whether the user can enter values other than those in the lookup table',
  `ASE_STORED` bit(1) NOT NULL DEFAULT b'0' COMMENT 'Whether or not this element is to be stored in the parent table (whether an object type or another element) as a field',
  `ASE_UPDATED` datetime DEFAULT NULL COMMENT 'When the element was last updated, for update/synch purposes',
  PRIMARY KEY (`ASE_ID`),
  UNIQUE KEY `ASE_NAME_UNIQUE` (`ASE_ASP_ID`,`ASE_LEVEL`,`ASE_NAME`) COMMENT 'Ensures the element name is unique for it''s aspect and level (prevent duplicate field names in tables)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='The elements of the system aspect.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sas_aspects`
--

DROP TABLE IF EXISTS `sas_aspects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sas_aspects` (
  `ASP_ID` int(10) unsigned NOT NULL COMMENT 'The ID in the local database',
  `ASP_ID_GLOBAL` int(10) unsigned DEFAULT NULL COMMENT 'The ID in the GLOBAL database',
  `ASP_ID_SOURCE` int(10) unsigned DEFAULT NULL COMMENT 'The ID in the source database',
  `ASP_SOURCE_DB_ID` int(10) unsigned DEFAULT NULL COMMENT 'The ID of the source database',
  `ASP_SYS_ID` int(10) unsigned DEFAULT NULL COMMENT 'The system ID this record is an aspect of',
  `ASP_VARIANT_ASP_ID` int(10) unsigned DEFAULT NULL COMMENT 'Filled if this aspect is a variant of another aspect (allows hiding and adding elements of/to the base aspect)',
  `ASP_NAME` varchar(50) NOT NULL COMMENT 'The aspect name',
  `ASP_DESCRIPTION` varchar(250) DEFAULT NULL COMMENT 'The description of the aspects purpose/what it hodls at a high level',
  `ASP_ELEMENTCOUNT` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'The number of level 1 elements in the aspect',
  `ASP_STORED` bit(1) NOT NULL DEFAULT b'0' COMMENT 'Whether or not the aspect is stored in a table',
  `ASP_UPDATED` datetime DEFAULT NULL COMMENT 'The last time the aspect was updated, for update/synch purposes',
  PRIMARY KEY (`ASP_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Storage for the definitions of the systems key components';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sas_core_data_types`
--

DROP TABLE IF EXISTS `sas_core_data_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sas_core_data_types` (
  `CDT_ID` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'The ID in the local database',
  `CDT_ID_GLOBAL` int(10) unsigned DEFAULT NULL COMMENT 'The ID in the GLOBAL database',
  `CDT_ID_SOURCE` int(10) unsigned DEFAULT NULL COMMENT 'The ID in the source database',
  `CDT_SOURCE_DB_ID` int(10) unsigned DEFAULT NULL COMMENT 'The local ID of the source database',
  `CDT_SYS_ID` int(10) unsigned DEFAULT NULL COMMENT 'The SYSTEMS record the core data type belongs to',
  `CDT_NAME` varchar(50) NOT NULL COMMENT 'The name of the core data type',
  `CDT_DESCRIPTION` varchar(250) DEFAULT NULL COMMENT 'The description of what the data type stores',
  `CDT_LINK_SYS_ID` int(10) unsigned DEFAULT NULL COMMENT 'The system to look for the aspect and element to link to in (if linking to an aspect and element).',
  `CDT_ASP_ID` int(10) unsigned DEFAULT NULL COMMENT 'The core data type is a link to an ASPECT, this is the link ID',
  `CDT_ASE_ID` int(10) unsigned DEFAULT NULL COMMENT 'The core data type is a link to an ASPECT_ELEMENT, this is the elements link ID',
  `CDT_UPDATED` datetime DEFAULT NULL COMMENT 'The last time the type was updated, for update/synch purposes',
  PRIMARY KEY (`CDT_ID`),
  UNIQUE KEY `CDT_NAME_UNIQUE` (`CDT_NAME`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Lists the computer data types available for program use. Storage_data_types links back to this to provide available types depending on the storage_engine of the database.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sas_core_storage_type_maps`
--

DROP TABLE IF EXISTS `sas_core_storage_type_maps`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sas_core_storage_type_maps` (
  `STM_ID` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'The ID of the mapping',
  `STM_ID_GLOBAL` int(10) unsigned DEFAULT NULL COMMENT 'The ID of the mapping in the global DB',
  `STM_ID_SOURCE` int(10) unsigned DEFAULT NULL COMMENT 'The ID of the mapping in the source DB',
  `STM_SOURCE_DB_ID` int(10) unsigned DEFAULT NULL COMMENT 'The ID of the source DB',
  `STM_CDT_ID` int(10) unsigned NOT NULL COMMENT 'The core data type ID',
  `STM_SDT_ID` int(10) unsigned DEFAULT NULL COMMENT 'The storage data type id',
  `STM_BRACKET_VALUE` varchar(15) DEFAULT NULL COMMENT 'A fixed value to place in brackets during SQL generation',
  `STM_UPDATED` datetime DEFAULT NULL COMMENT 'When the record was last updated',
  PRIMARY KEY (`STM_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='This table stores the storage data types relevant to each core data type, if the core type isn''t an aspect or element.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sas_database_access`
--

DROP TABLE IF EXISTS `sas_database_access`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sas_database_access` (
  `DBA_ID` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'The local ID of the access record',
  `DBA_ID_GLOBAL` int(10) unsigned DEFAULT NULL COMMENT 'The ID of the access record in the GLOBAL DB',
  `DBA_ID_SOURCE` int(10) unsigned DEFAULT NULL COMMENT 'The ID of the access record in the source DB',
  `DBA_SOURCE_DB_ID` int(10) unsigned DEFAULT NULL COMMENT 'Link to the local ID of the source DB',
  `DBA_DB_ID` int(10) unsigned DEFAULT NULL COMMENT 'The local database this access record is for',
  `DBA_NAME` varchar(50) NOT NULL COMMENT 'The name of the acccess account',
  `DBA_DESCRIPTION` varchar(250) DEFAULT NULL COMMENT 'The description of the access account',
  `DBA_USERNAME` varchar(50) DEFAULT NULL COMMENT 'The username to use to access the database',
  `DBA_PASSWORD` varchar(200) DEFAULT NULL COMMENT 'The password for the database. MySQL can authenticate on SHA256 so either store this hashed or encrypted for everyone''s sake!',
  `DBA_CAN_READ` bit(1) NOT NULL DEFAULT b'0' COMMENT 'Whether or not the user has select and execute privelages',
  `DBA_CAN_EDIT` bit(1) NOT NULL DEFAULT b'0' COMMENT 'Whether or not the user has insert/update privelages',
  `DBA_CAN_CREATE` bit(1) NOT NULL DEFAULT b'0' COMMENT 'Whether the user has create privelages',
  `DBA_CAN_DROP` bit(1) NOT NULL DEFAULT b'0' COMMENT 'Whether or not the user has drop privelages\n',
  `DBA_CAN_USERS` bit(1) NOT NULL DEFAULT b'0' COMMENT 'Can this access manage users on the database?',
  `DBA_UPDATED` datetime DEFAULT NULL COMMENT 'The last time the access record was updated, for update/synch purposes',
  PRIMARY KEY (`DBA_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='A list of user names and (secure) passwords which can be used to connect to external DB''s.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sas_databases`
--

DROP TABLE IF EXISTS `sas_databases`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sas_databases` (
  `DB_ID` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'The local ID of the DB',
  `DB_ID_GLOBAL` int(10) unsigned DEFAULT NULL COMMENT 'The DB''s ID in the GLOBAL database',
  `DB_ID_SOURCE` int(10) unsigned DEFAULT NULL COMMENT 'The DB''s ID in the source database',
  `DB_SOURCE_DB_ID` int(10) unsigned DEFAULT NULL COMMENT 'The ID of the source DB in the local database',
  `DB_NAME` varchar(50) NOT NULL COMMENT 'The name by which the database is known',
  `DB_DESCRIPTION` varchar(250) DEFAULT NULL COMMENT 'Description of what the database holds and/or is for',
  `DB_STT_ID` int(10) unsigned DEFAULT NULL COMMENT 'The storage type ID',
  `DB_HOST` varchar(100) NOT NULL COMMENT 'The host name/address to connect to',
  `DB_PORT` int(10) unsigned DEFAULT NULL COMMENT 'The port to connect on, AFAIK MySQL only',
  `DB_DEFAULT_DB` varchar(100) DEFAULT NULL COMMENT 'The default database/catalog to connect to on the DB server',
  `DB_UPDATED` datetime DEFAULT NULL COMMENT 'The last time the DB record was updated, for update/synch purposes',
  PRIMARY KEY (`DB_ID`),
  UNIQUE KEY `DB_NAME_UNIQUE` (`DB_NAME`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='Lists other databases this database is aware of. e.g. The GLOBAL DB and source DBs and/or DBs which contain data tables mapped to SYSTEMS and ASPECTS held in this one.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sas_fields`
--

DROP TABLE IF EXISTS `sas_fields`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sas_fields` (
  `FLD_ID` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'The local field ID',
  `FLD_ID_GLOBAL` int(10) unsigned DEFAULT NULL COMMENT 'The ID of the field in the GLOBAL DB',
  `FLD_ID_SOURCE` int(10) unsigned DEFAULT NULL COMMENT 'The ID of the field in the source DB',
  `FLD_SOURCE_DB_ID` int(10) unsigned DEFAULT NULL COMMENT 'The local ID of the source DB',
  `FLD_TBL_ID` int(11) NOT NULL COMMENT 'The link to the fields table by its ID',
  `FLD_ASE_ID` int(10) unsigned DEFAULT NULL COMMENT 'The element the field holds data for',
  `FLD_NAME` varchar(50) NOT NULL COMMENT 'The field name for the field',
  `FLD_NO` int(11) unsigned NOT NULL COMMENT 'The fields number for ordering purposes',
  `FLD_SDT_ID` int(10) unsigned NOT NULL COMMENT 'The storage data type ID for this field',
  `FLD_SIZE` int(10) unsigned DEFAULT NULL COMMENT 'The size of the field if size is supported/required.',
  `FLD_TITLE` varchar(50) DEFAULT NULL COMMENT 'The display title for the field',
  `FLD_PRIMARY_KEY` bit(1) NOT NULL DEFAULT b'0' COMMENT 'Whether or not the field is part of the primary key',
  `FLD_NOT_NULL` bit(1) NOT NULL DEFAULT b'0' COMMENT 'Whether or not the field allows nulls',
  `FLD_UNIQUE` bit(1) NOT NULL DEFAULT b'0' COMMENT 'Marker if the field needs to contain unique values',
  `FLD_UNSIGNED` bit(1) NOT NULL DEFAULT b'0' COMMENT 'Indicates the field is unsigned (larger, positive only numbers)',
  `FLD_ZEROFILL` bit(1) NOT NULL DEFAULT b'0' COMMENT 'Whether or not to fill numeric values with zero''s',
  `FLD_AUTOINC` bit(1) NOT NULL DEFAULT b'0' COMMENT 'Whether or not the field needs to auto increment',
  `FLD_DEFAULT` varchar(50) DEFAULT NULL,
  `FLD_UPDATED` datetime DEFAULT NULL COMMENT 'When the field was last updated',
  PRIMARY KEY (`FLD_ID`),
  UNIQUE KEY `FLD_NO_UNIQUE` (`FLD_TBL_ID`,`FLD_NO`) COMMENT 'Unique field no per table',
  UNIQUE KEY `IDX_FLD_UNIQUE` (`FLD_TBL_ID`,`FLD_NAME`) COMMENT 'This index ensures the field name is unique per table'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='The fields table stores the fields defined in each of the system tables from the tables table. Doesn''t record when updated, fields will be parsed if the table is updated.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sas_protocols`
--

DROP TABLE IF EXISTS `sas_protocols`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sas_protocols` (
  `PTC_ID` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'The local protocol ID',
  `PTC_ID_GLOBAL` int(10) unsigned DEFAULT NULL COMMENT 'The ID in the GLOBAL DB',
  `PTC_ID_SOURCE` int(10) unsigned DEFAULT NULL COMMENT 'The ID in the source DB',
  `PTC_SOURCE_DB_ID` int(10) unsigned DEFAULT NULL COMMENT 'The ID of the local source DB',
  `PTC_NAME` varchar(50) NOT NULL COMMENT 'The "user friendly" name of the protocol',
  `PTC_DESCRIPTION` varchar(250) DEFAULT NULL COMMENT 'The description of the protocol',
  `PTC_ZEOS` varchar(25) DEFAULT NULL COMMENT 'The name of the protocol for Zeos connections',
  `PTC_UPDATED` datetime DEFAULT NULL COMMENT 'The last update of the record, for synch purposes',
  PRIMARY KEY (`PTC_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='The list of protocols available to storage types';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sas_storage_data_types`
--

DROP TABLE IF EXISTS `sas_storage_data_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sas_storage_data_types` (
  `SDT_ID` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'The ID in the local DB',
  `SDT_ID_GLOBAL` int(10) unsigned DEFAULT NULL COMMENT 'The ID in the GLOBAL DB',
  `SDT_ID_SOURCE` int(10) unsigned DEFAULT NULL COMMENT 'The ID in the source DB',
  `SDT_SOURCE_DB_ID` int(10) unsigned DEFAULT NULL COMMENT 'The local ID of the source DB',
  `SDT_STT_ID` int(10) unsigned DEFAULT NULL COMMENT 'The storage type that this storage data type belongs to',
  `SDT_NAME` varchar(50) NOT NULL COMMENT 'The name for the storage data type',
  `SDT_DESCRIPTION` varchar(250) DEFAULT NULL COMMENT 'The description of how the storage data type works',
  `SDT_SIZE_REQUIRED` bit(1) NOT NULL DEFAULT b'0' COMMENT 'Whether or not the data type requires a size parameter',
  `SDT_SQL_TEXT` varchar(30) NOT NULL COMMENT 'The text to use for SQL generation',
  `SDT_SUPPORT_BRACKETS` bit(1) NOT NULL COMMENT 'Whether or not the data type supports a bracketed specifier',
  `SDT_REQUIRE_BRACKETS` bit(1) NOT NULL COMMENT 'Whether or not the data type requires a bracketed specifier',
  `SDT_MINSIZE` int(10) unsigned DEFAULT NULL COMMENT 'The minimum size for the data type',
  `SDT_MAXSIZE` int(10) unsigned DEFAULT NULL COMMENT 'The maximum size for the data type',
  `SDT_SUPPORT_PK` bit(1) NOT NULL DEFAULT b'1' COMMENT 'Whether or not the type can be in the primary key',
  `SDT_SUPPORT_NN` bit(1) NOT NULL DEFAULT b'1' COMMENT 'Whether or not the type supports the not null indicator',
  `SDT_SUPPORT_UQ` bit(1) NOT NULL DEFAULT b'1' COMMENT 'Whether or not the type supports a unique marker/index',
  `SDT_SUPPORT_UN` bit(1) NOT NULL DEFAULT b'0' COMMENT 'Whether or not the type can be unsigned ',
  `SDT_SUPPORT_ZF` bit(1) NOT NULL DEFAULT b'0' COMMENT 'Whether or not the type can be zero filled',
  `SDT_SUPPORT_AI` bit(1) NOT NULL DEFAULT b'1' COMMENT 'Whether or not the type can be autoincremented',
  `SDT_SUPPORT_DEFAULT` bit(1) NOT NULL DEFAULT b'1' COMMENT 'Whether or not the type can have a default value',
  `SDT_UPDATED` datetime DEFAULT NULL COMMENT 'The last time the storage data type was updated, for update/synch purposes',
  PRIMARY KEY (`SDT_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='The data types supported by the storage engine.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sas_storage_types`
--

DROP TABLE IF EXISTS `sas_storage_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sas_storage_types` (
  `STT_ID` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'The ID in the ocal database',
  `STT_ID_GLOBAL` int(10) unsigned DEFAULT NULL COMMENT 'The ID in the GLOBAL database',
  `STT_ID_SOURCE` int(10) unsigned DEFAULT NULL COMMENT 'The ID in the source database',
  `STT_SOURCE_DB_ID` int(10) unsigned DEFAULT NULL COMMENT 'The local ID of the source DB',
  `STT_NAME` varchar(50) NOT NULL COMMENT 'The name for the storage type',
  `STT_DESCRIPTION` varchar(250) DEFAULT NULL COMMENT 'The description of the storage type',
  `STT_PTC_ID` int(10) unsigned DEFAULT NULL COMMENT 'The ID of the protocol to use for this storage type',
  `STT_PATH` varchar(250) DEFAULT NULL COMMENT 'The path to the default starting directory or engine dll',
  `STT_UPDATED` datetime DEFAULT NULL COMMENT 'The last time the record was updated, for update and synch purposes',
  PRIMARY KEY (`STT_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='Defines supported storage methods, e.g. MySQL, MSSQL, Ini files, XML files etc.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sas_system_databases`
--

DROP TABLE IF EXISTS `sas_system_databases`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sas_system_databases` (
  `SDB_SYS_ID` int(10) unsigned NOT NULL COMMENT 'The local ID of the system which is present in another database',
  `SDB_DB_ID` int(10) unsigned NOT NULL COMMENT 'The local ID of the external database which contains the system',
  `SDB_ALT_PREFIX` varchar(5) DEFAULT NULL COMMENT 'If the alternate database has a different prefix for the system tables enter it here',
  PRIMARY KEY (`SDB_SYS_ID`,`SDB_DB_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='A list of databases containing systems defined by this structure. Does not include the local database, whcih is indicated by the SYS_LOCAL flag in the systems table';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sas_system_link_systems`
--

DROP TABLE IF EXISTS `sas_system_link_systems`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sas_system_link_systems` (
  `SLS_ID` int(10) unsigned NOT NULL COMMENT 'The ID in the local DB',
  `SLS_ID_GLOBAL` int(10) unsigned DEFAULT NULL COMMENT 'The ID in the GLOBAL DB',
  `SLS_ID_SOURCE` int(10) unsigned DEFAULT NULL COMMENT 'The ID in the source DB',
  `SLS_SOURCE_DB_ID` int(10) unsigned DEFAULT NULL COMMENT 'The local ID of the source DB',
  `SLS_SYS_ID` int(10) unsigned NOT NULL COMMENT 'The ID of the system with the link',
  `SLS_LINK_SYS_ID` int(10) unsigned NOT NULL COMMENT 'The ID of the linked system',
  `SLS_REQUIRED` bit(1) NOT NULL DEFAULT b'0' COMMENT 'Whether or not the linked system is required by the linking system',
  `SLS_UPDATED` datetime DEFAULT NULL COMMENT 'When the record was last updated',
  PRIMARY KEY (`SLS_ID`),
  UNIQUE KEY `IDX_SLS_UNIQUE` (`SLS_SYS_ID`,`SLS_LINK_SYS_ID`) COMMENT 'This index ensures a system doesn''t list dependencies/links more than once'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Defines links between systems purely on a system to system basis, as well as whether the other system is required. The actual links are defined on aspects and/or elements.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sas_systems`
--

DROP TABLE IF EXISTS `sas_systems`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sas_systems` (
  `SYS_ID` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'The local ID for the system',
  `SYS_ID_GLOBAL` int(10) unsigned DEFAULT NULL COMMENT 'The ID for the system in the global database',
  `SYS_ID_SOURCE` int(10) unsigned DEFAULT NULL COMMENT 'The id for the system in the source database',
  `SYS_SOURCE_DB_ID` int(10) unsigned DEFAULT NULL COMMENT 'The ID of the source database',
  `SYS_NAME` varchar(50) NOT NULL COMMENT 'The name for the system',
  `SYS_DESCRIPTION` varchar(250) DEFAULT NULL COMMENT 'The description of what the system does',
  `SYS_PREFIX` varchar(5) DEFAULT NULL COMMENT 'Tables created as part of this system will have this prefix if assigned',
  `SYS_LOCAL` bit(1) NOT NULL DEFAULT b'0' COMMENT 'Whether or not the system is present in the local DB',
  `SYS_UPDATED` datetime DEFAULT NULL COMMENT 'When the system was last updated in the current database, for comparison/synch with global/source.',
  PRIMARY KEY (`SYS_ID`),
  UNIQUE KEY `SYS_ID_UNIQUE` (`SYS_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='The names and ID''s of any defined systems, provides collating table for the systems structure. System ID 0 will define the systems system. The TABLES table for system ID 0 will therefore tell you all tables involved in SYSTEMS.	';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sas_tables`
--

DROP TABLE IF EXISTS `sas_tables`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sas_tables` (
  `TBL_ID` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'The local ID for the table',
  `TBL_ID_GLOBAL` int(11) unsigned DEFAULT NULL COMMENT 'The ID for the table in the GLOBAL database',
  `TBL_ID_SOURCE` int(11) unsigned DEFAULT NULL COMMENT 'The ID for the table in the source database',
  `TBL_SOURCE_DB_ID` int(11) unsigned DEFAULT NULL COMMENT 'The local ID of the source database',
  `TBL_ASP_ID` int(11) unsigned DEFAULT NULL COMMENT 'The ID of the aspect which this table is part of',
  `TBL_ASE_ID` int(10) unsigned DEFAULT NULL COMMENT 'If this table is for an element rather than an aspect, this is where the ID goes',
  `TBL_PARENT_TBL_ID` int(11) unsigned DEFAULT NULL COMMENT 'The ID of the parent table if this table is for sub-records',
  `TBL_PARENT_KEY_FLD_ID` int(10) unsigned DEFAULT NULL COMMENT 'The ID of the parent tables key field',
  `TBL_NAME` varchar(50) NOT NULL COMMENT 'The name of the table',
  `TBL_PREFIX` varchar(5) DEFAULT NULL COMMENT 'Fields belonging to this table will start with this prefix if it''s populated',
  `TBL_USE_IDENTITY` bit(1) NOT NULL DEFAULT b'1' COMMENT 'Automatically add an ID field to the table',
  `TBL_SUPPORT_GLOBAL` bit(1) NOT NULL DEFAULT b'1' COMMENT 'Automatically add an ID_GLOBAL field to the table',
  `TBL_SUPPORT_SOURCE` bit(1) NOT NULL DEFAULT b'1' COMMENT 'Automatically add ID_SOURCE and SOURCE_DB_ID fields to the table',
  `TBL_FIELDCOUNT` int(11) NOT NULL DEFAULT '0' COMMENT 'The number of fields in the table',
  `TBL_UPDATED` datetime DEFAULT NULL COMMENT 'The date and time the table or it''s fields were updated for update/synch with global and source',
  PRIMARY KEY (`TBL_ID`),
  UNIQUE KEY `TBL_ID_UNIQUE` (`TBL_ID`),
  KEY `TBL_PARENT_TBL_IDX` (`TBL_PARENT_TBL_ID`) COMMENT 'Search index for parent table linking'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='The tables table defines the tables used by defined systems. The tables description can be found in its aspect definition';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sch_schedule_items`
--

DROP TABLE IF EXISTS `sch_schedule_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sch_schedule_items` (
  `SCI_ID` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'The local schedule item ID',
  `SCI_ID_GLOBAL` int(10) unsigned DEFAULT NULL COMMENT 'The ID in the global DB',
  `SCI_ID_SOURCE` int(10) unsigned DEFAULT NULL COMMENT 'The ID in the source DB',
  `SCI_SOURCE_DB_ID` int(10) unsigned DEFAULT NULL COMMENT 'The local ID of the source DB',
  `SCI_SCT_ID` int(10) unsigned DEFAULT NULL COMMENT 'The schedule type id',
  `SCI_START` datetime DEFAULT NULL COMMENT 'The start date/time of the scheduled occurence',
  `SCI_END` datetime DEFAULT NULL COMMENT 'The end date/time of the scheduled occurrence',
  `SCI_BREAKS` datetime DEFAULT NULL COMMENT 'The number of hours allocated for breaks within the scheduled time range.',
  `SCI_SCP_ID` int(10) unsigned DEFAULT NULL COMMENT 'The local ID of the schedule pattern which the item was generated from or to apply.',
  `SCI_SPI_ID` int(10) unsigned DEFAULT NULL COMMENT 'The ID of the schedule pattern item this relates to, also indicates if the schedule has been generated.',
  `SCI_ORIGIN_SCI_ID` int(10) unsigned DEFAULT NULL COMMENT 'The local ID of the schedule item this one was copied from.',
  `SCI_SUPERCEDES_SCI_ID` int(10) unsigned DEFAULT NULL COMMENT 'The local ID of the schedule item this one replaces',
  `SCI_SUPERCEDED_SCI_ID` int(10) unsigned DEFAULT NULL COMMENT 'The local ID of the schedule which replaces this one.',
  `SCI_TGT_ID` int(10) unsigned DEFAULT NULL COMMENT 'The link IDto the related target',
  `SCI_UPDATED` datetime DEFAULT NULL COMMENT 'When the schedule was last updated',
  `SCI_SR` bit(1) NOT NULL DEFAULT b'0' COMMENT 'Suppress/recycle indicator, exclude from searches.',
  PRIMARY KEY (`SCI_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='Allows linkable items to be scheduled.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sch_schedule_pattern_items`
--

DROP TABLE IF EXISTS `sch_schedule_pattern_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sch_schedule_pattern_items` (
  `SPI_ID` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'The local pattern item ID',
  `SPI_ID_GLOBAL` int(10) unsigned DEFAULT NULL COMMENT 'The global ID of the pattern item',
  `SPI_ID_SOURCE` int(10) unsigned DEFAULT NULL COMMENT 'The pattern items ID in the source DB',
  `SPI_SOURCE_DB_ID` int(10) unsigned DEFAULT NULL COMMENT 'The local ID of the source DB',
  `SPI_SCP_ID` int(10) unsigned NOT NULL COMMENT 'The schedule pattern ID',
  `SPI_NUMBER` smallint(5) unsigned DEFAULT NULL COMMENT 'Fairly useless order for day mode, otherwise day of week, day of month etc',
  `SPI_START` time DEFAULT NULL COMMENT 'The start time of the item segment',
  `SPI_END` time DEFAULT NULL COMMENT 'The end time of the item segment',
  `SPI_BREAKS` smallint(5) unsigned DEFAULT NULL COMMENT 'How many hours of breaks are allowed for within the range',
  `SPI_UPDATED` datetime DEFAULT NULL COMMENT 'When the pattern item was last updated',
  `SPI_SR` bit(1) NOT NULL DEFAULT b'0' COMMENT 'Suppress/recycle indicator, exclude from searches',
  PRIMARY KEY (`SPI_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Stores sets of start and end times for use within patterns for those which implement them rather than linking to sub patterns';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sch_schedule_patterns`
--

DROP TABLE IF EXISTS `sch_schedule_patterns`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sch_schedule_patterns` (
  `SCP_ID` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'The local ID of the schedule pattern',
  `SCP_ID_GLOBAL` int(10) unsigned DEFAULT NULL COMMENT 'The ID in the global database',
  `SCP_ID_SOURCE` int(10) unsigned DEFAULT NULL COMMENT 'The ID in the source DB',
  `SCP_SOURCE_DB_ID` int(10) unsigned DEFAULT NULL COMMENT 'The local ID of the source DB',
  `SCP_NAME` varchar(50) NOT NULL COMMENT 'The name of the schedule pattern',
  `SCP_DESCRIPTION` varchar(250) DEFAULT NULL COMMENT 'The description of the schedule pattern.',
  `SCP_MODE` tinyint(3) unsigned DEFAULT NULL COMMENT 'The mode of the schedule pattern, 1 = Day, 2 = Week, 3 = Month, 4 = Year etc if required',
  `SCP_SWITCHES` smallint(5) unsigned DEFAULT NULL COMMENT 'Used to indicate various things for all modes apart from day. Days of week for week mode, 2nd Monday, Last day of month for month mode etc',
  `SCP_FREQUENCY` smallint(5) unsigned DEFAULT NULL COMMENT 'Apply pattern every <frequency> <mode>s for <duration> <mode>s. eg. Pattern "Shift Work", Every 4 Days Schedule 4 Days',
  `SCP_DURATION` smallint(5) unsigned DEFAULT NULL COMMENT 'Apply pattern every <frequency> <mode>s for <duration> <mode>s. eg. Pattern "Shift Work", Every 4 Days Schedule 4 Days',
  `SCP_SUB_SCP_ID` int(10) unsigned DEFAULT NULL COMMENT 'The inner pattern for a range, must of of a lower mode. Allows creation of an annual pattern which applies a month pattern which uses a week pattern etc',
  `SCP_UPDATED` datetime DEFAULT NULL COMMENT 'When the schedule pattern was last updated',
  `SCP_SR` bit(1) NOT NULL DEFAULT b'0' COMMENT 'Suppress/recycle indicator, exclude from searches',
  PRIMARY KEY (`SCP_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Allows systematic patterns to be created for generating schedule items or to apply to date ranges.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sch_schedule_type_avail_links`
--

DROP TABLE IF EXISTS `sch_schedule_type_avail_links`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sch_schedule_type_avail_links` (
  `SCA_SCT_ID` int(10) unsigned NOT NULL COMMENT 'The local ID of the schedule type',
  `SCA_LTP_ID` int(10) unsigned NOT NULL COMMENT 'The local ID of the link type',
  `SCA_UPDATED` datetime DEFAULT NULL COMMENT 'When the availability record was updated',
  PRIMARY KEY (`SCA_SCT_ID`,`SCA_LTP_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Which link types are allowed for schedule types';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sch_schedule_types`
--

DROP TABLE IF EXISTS `sch_schedule_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sch_schedule_types` (
  `SCT_ID` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'The local schedule type ID',
  `SCT_ID_GLOBAL` int(10) unsigned DEFAULT NULL COMMENT 'The ID of the type in the global DB',
  `SCT_ID_SOURCE` int(10) unsigned DEFAULT NULL COMMENT 'The ID of the type in the source DB',
  `SCT_SOURCE_DB_ID` int(10) unsigned DEFAULT NULL COMMENT 'The local ID of the source DB',
  `SCT_NAME` varchar(50) NOT NULL COMMENT 'The name of the schedule type',
  `SCT_DESCRIPTION` varchar(250) DEFAULT NULL COMMENT 'The description of the types purpose',
  `SCT_COLOUR` varchar(8) DEFAULT NULL COMMENT 'The RGBA colour value as a varchar',
  `SCT_UPDATED` datetime DEFAULT NULL COMMENT 'When the type record was last updated.',
  `SCT_SR` bit(1) NOT NULL DEFAULT b'0' COMMENT 'Suppress/recycle indicator, exclude from searches.',
  PRIMARY KEY (`SCT_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='Holds names, descriptions and colours for types of schedule items, key for the schedule link type availability.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sch_targets`
--

DROP TABLE IF EXISTS `sch_targets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sch_targets` (
  `TGT_ID` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'The local ID of the target',
  `TGT_ID_GLOBAL` int(10) unsigned DEFAULT NULL COMMENT 'The ID of the target in the global ID',
  `TGT_ID_SOURCE` int(10) unsigned DEFAULT NULL COMMENT 'The ID of the target in the source DB',
  `TGT_SOURCE_DB_ID` int(10) unsigned DEFAULT NULL COMMENT 'The local ID of the source DB',
  `TGT_WHEN` datetime DEFAULT NULL COMMENT 'When the target it set for',
  `TGT_UPDATED` datetime DEFAULT NULL COMMENT 'When the target record was last updated',
  `TGT_SR` bit(1) NOT NULL DEFAULT b'0' COMMENT 'Suppress/recycle indicator, exclude from searches',
  PRIMARY KEY (`TGT_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Stores specific target date/times for deadlines etc';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `usr_access_areas`
--

DROP TABLE IF EXISTS `usr_access_areas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `usr_access_areas` (
  `ACA_ID` int(10) unsigned NOT NULL COMMENT 'The local access area ID',
  `ACA_ID_GLOBAL` int(10) unsigned DEFAULT NULL COMMENT 'The ID in the global DB',
  `ACA_ID_SOURCE` int(10) unsigned DEFAULT NULL COMMENT 'The ID in the source DB',
  `ACA_SOURCE_DB_ID` int(10) unsigned DEFAULT NULL COMMENT 'The local ID of the source DB',
  `ACA_NAME` varchar(50) NOT NULL COMMENT 'The access area name',
  `ACA_DESCRIPTION` varchar(250) DEFAULT NULL COMMENT 'The description of the access area',
  `ACA_UPDATED` datetime DEFAULT NULL COMMENT 'When the access area was last updated',
  PRIMARY KEY (`ACA_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Defines secureable areas';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `usr_role_access`
--

DROP TABLE IF EXISTS `usr_role_access`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `usr_role_access` (
  `RAC_ROL_ID` int(10) unsigned NOT NULL COMMENT 'The role ID for which access is being defined',
  `RAC_ACA_ID` int(10) unsigned NOT NULL COMMENT 'The access area for which access is being defined',
  `RAC_SEARCHVIEW` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT 'The Search/View rights for the role and area, 0 = None, 1 = Linked, 2 = All',
  `RAC_NEWMOD` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT 'The New/Modify rights for the role and area, 0 = None, 1 = Linked, 2 = All',
  `RAC_REMOVE` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT 'The removal rights for the role and area, 0 = None, 1 = Linked, 2 = All',
  `RAC_CONFIG` bit(1) NOT NULL DEFAULT b'0' COMMENT 'Whether or not the user can configure the area',
  `RAC_UPDATED` datetime DEFAULT NULL COMMENT 'When the role access record was last updated',
  PRIMARY KEY (`RAC_ROL_ID`,`RAC_ACA_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Defines role rights to access areas';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `usr_roles`
--

DROP TABLE IF EXISTS `usr_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `usr_roles` (
  `ROL_ID` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'The local role ID',
  `ROL_ID_GLOBAL` int(10) unsigned DEFAULT NULL COMMENT 'The role ID in the global DB',
  `ROL_ID_SOURCE` int(10) unsigned DEFAULT NULL COMMENT 'The role ID in the source DB',
  `ROL_SOURCE_DB_ID` int(10) unsigned DEFAULT NULL COMMENT 'The local ID of the source DB',
  `ROL_NAME` varchar(50) NOT NULL COMMENT 'The role name',
  `ROL_DESCRIPTION` varchar(250) DEFAULT NULL COMMENT 'The role description',
  `ROL_UPDATED` datetime DEFAULT NULL COMMENT 'When the role was last updated',
  `ROL_SR` bit(1) NOT NULL DEFAULT b'0' COMMENT 'Suppress/recycle indicator, exclude from searches',
  PRIMARY KEY (`ROL_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='Defines user roles which can be used to control systems access';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `usr_user_roles`
--

DROP TABLE IF EXISTS `usr_user_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `usr_user_roles` (
  `URL_USR_ID` int(10) unsigned NOT NULL COMMENT 'The local user ID',
  `URL_ROL_ID` int(10) unsigned NOT NULL COMMENT 'The local role ID',
  `URL_UPDATED` datetime DEFAULT NULL COMMENT 'When the role was assigned/updated',
  PRIMARY KEY (`URL_USR_ID`,`URL_ROL_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Assigns roles to users';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `usr_users`
--

DROP TABLE IF EXISTS `usr_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `usr_users` (
  `USR_ID` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'The local user ID',
  `USR_ID_GLOBAL` int(10) unsigned DEFAULT NULL COMMENT 'The ID of the user in the global DB',
  `USR_ID_SOURCE` int(10) unsigned DEFAULT NULL COMMENT 'The ID of the user in the source DB',
  `USR_SOURCE_DB_ID` int(10) unsigned DEFAULT NULL COMMENT 'The local ID of the source DB',
  `USR_USERNAME` varchar(50) NOT NULL COMMENT 'The user name',
  `USR_PASSHASH` varchar(100) NOT NULL COMMENT 'The hashed password',
  `USR_UPDATED` datetime DEFAULT NULL COMMENT 'When the user record was last updated',
  `USR_SR` bit(1) NOT NULL DEFAULT b'0' COMMENT 'Suppress/recycle indicator, exclude from searches',
  PRIMARY KEY (`USR_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COMMENT='User accounts';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `web_account_activations`
--

DROP TABLE IF EXISTS `web_account_activations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `web_account_activations` (
  `WAA_WAC_ID` int(10) unsigned NOT NULL COMMENT 'The ID of the account awaiting activation',
  `WAA_TOKEN` varchar(100) NOT NULL COMMENT 'The token needed to activate the account',
  `WAA_ISSUED` datetime NOT NULL COMMENT 'When the validation token was issued, for expiry checks',
  PRIMARY KEY (`WAA_WAC_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Stores tokens to activate registered accounts';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `web_accounts`
--

DROP TABLE IF EXISTS `web_accounts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `web_accounts` (
  `WAC_ID` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'The local DB unique identifier.',
  `WAC_ID_GLOBAL` int(10) unsigned DEFAULT NULL COMMENT 'The ID of the record in the global DB',
  `WAC_ID_SOURCE` int(10) unsigned DEFAULT NULL COMMENT 'The ID in the source DB',
  `WAC_SOURCE_DB_ID` int(10) unsigned DEFAULT NULL COMMENT 'The ID of the source DB',
  `WAC_USERNAME` varchar(50) DEFAULT NULL COMMENT 'The username on the account',
  `WAC_EMAIL` varchar(150) DEFAULT NULL COMMENT 'The email address linked to the account',
  `WAC_PASSHASH` varchar(100) NOT NULL COMMENT 'The accounts password, hashed.',
  `WAC_ACTIVE` bit(1) NOT NULL COMMENT 'Whether or not the account is active and can be logged in to.',
  `WAC_PER_ID` int(10) unsigned DEFAULT NULL COMMENT 'The local ID of the person record for the account',
  `WAC_USR_ID` int(10) unsigned DEFAULT NULL COMMENT 'The user account ID for the web account.',
  `WAC_UPDATED` datetime DEFAULT NULL COMMENT 'When the record was last updated in this DB.',
  `WAC_SR` bit(1) NOT NULL DEFAULT b'0' COMMENT 'Suppress/recycle indicator, exclude from searches',
  PRIMARY KEY (`WAC_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COMMENT='Stores log in account information for users with accounts.';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `web_logins`
--

DROP TABLE IF EXISTS `web_logins`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `web_logins` (
  `WEL_TOKEN` varchar(36) NOT NULL COMMENT 'The UUID token issued on log in',
  `WEL_WAC_ID` int(10) unsigned NOT NULL COMMENT 'The logged in web account ID',
  `WEL_CREATED` datetime NOT NULL COMMENT 'The log in date and time',
  `WEL_EXPIRES` datetime NOT NULL COMMENT 'When the log in expires, updates on access',
  PRIMARY KEY (`WEL_TOKEN`),
  UNIQUE KEY `WEL_TOKEN_UNIQUE` (`WEL_TOKEN`),
  UNIQUE KEY `WEL_WAC_ID_UNIQUE` (`WEL_WAC_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Stores account log in token';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping routines for database 'knowledge'
--
/*!50003 DROP FUNCTION IF EXISTS `fn_con_contact_type_name` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE FUNCTION `fn_con_contact_type_name`(
  P_CTT_ID INT UNSIGNED) RETURNS varchar(50) CHARSET utf8
BEGIN
  DECLARE vName VARCHAR(50);

  SELECT
    CTT_NAME
  FROM
    con_contact_types
  WHERE
    CTT_ID = P_CTT_ID
  INTO vName;
RETURN IFNULL(vName,'(None)');
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `fn_con_fullname` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE FUNCTION `fn_con_fullname`(
  P_PER_ID INT UNSIGNED) RETURNS varchar(400) CHARSET utf8
BEGIN
  DECLARE vTitle VARCHAR(50);
  DECLARE vForenames VARCHAR(250);
  DECLARE vSurname VARCHAR(100);
  DECLARE vFullName VARCHAR(400);

  SET vFullName = '';

  SELECT
    TIT_TITLE,
    PER_FORENAMES,
    PER_SURNAME
  FROM
    con_people
    LEFT OUTER JOIN con_titles ON TIT_ID = PER_TIT_ID
  WHERE
    PER_ID = P_PER_ID
  INTO vTitle,vForenames,vSurname;

  IF (vTitle IS NOT NULL) and (LENGTH(vTitle) > 0) THEN
    SET vFullName = vTitle;
  END IF;

  IF (vForenames IS NOT NULL) and (LENGTH(vForeNames) > 0) THEN
    IF LENGTH(vFullName) > 0 THEN
      SET vFullName = concat(vFullName,' ');
	  END IF;
	  SET vFullName = concat(vFullName,vForeNames);
  END IF;

  IF (vSurname IS NOT NULL) and (LENGTH(vSurname) > 0) THEN
    IF LENGTH(vFullName) > 0 THEN
      SET vFullName = concat(vFullName,' ');
	  END IF;
	  SET vFullName = concat(vFullName,vSurname);
  END IF;

  IF vFullName = '' THEN
    SET vFullName  = 'None';
  END IF;
RETURN vFullName;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `fn_con_full_contact_point` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE FUNCTION `fn_con_full_contact_point`(
  P_CNP_ID INT UNSIGNED) RETURNS varchar(303) CHARSET utf8
BEGIN
  DECLARE vFullContact VARCHAR(303);
  DECLARE vMethod VARCHAR(50);
  DECLARE vContact VARCHAR(250);

  SET vFullContact = '';

  IF NOT EXISTS(
    SELECT CNP_ID
    FROM con_contact_points
    WHERE CNP_ID = P_CNP_ID) THEN
    SET vFullContact = 'None';
  ELSE
    SELECT
      CPT_NAME,
      CNP_CONTACT
    FROM
      con_contact_points
      LEFT OUTER JOIN con_contact_point_types ON CPT_ID = CNP_CPT_ID
    WHERE
      CNP_ID = P_CNP_ID
    INTO vMethod,vContact;

    IF (vMethod IS NOT NULL) and (LENGTH(vMethod) > 0) THEN
      IF LENGTH(vFullContact) > 0 THEN
        SET vFullContact = concat(vFullContact,' ');
	  END IF;
	  SET vFullContact = concat(vFullContact,vMethod);
    END IF;

    IF (vContact IS NOT NULL) and (LENGTH(vContact) > 0) THEN
      IF LENGTH(vFullContact) > 0 THEN
        SET vFullContact = concat(vFullContact,': ');
	  END IF;
	  SET vFullContact = concat(vFullContact,vContact);
    END IF;
  END IF;
RETURN vFullContact;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `fn_con_full_link_contact_point` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE FUNCTION `fn_con_full_link_contact_point`(
  P_LINK_TYPE INT UNSIGNED,
  P_LINK INT UNSIGNED,
  P_CNP_ID INT UNSIGNED) RETURNS varchar(450) CHARSET utf8
BEGIN
  DECLARE vFullContact VARCHAR(450);
  DECLARE vType VARCHAR(50);
  DECLARE vSpeakTo VARCHAR(100);
  DECLARE vMethod VARCHAR(50);
  DECLARE vContact VARCHAR(250);

  SET vFullContact = '';

  IF NOT EXISTS(
    SELECT CNP_ID
    FROM con_contact_points
    WHERE CNP_ID = P_CNP_ID) THEN
    SET vFullContact = 'None';
  ELSE
    SELECT
      CTT_NAME,
      LNK_X_STR,
      CPT_NAME,
      CNP_CONTACT
    FROM
      con_contact_points
      JOIN lnk_links ON LNK_CHD_LTP_ID = 3 AND LNK_CHD_ID = CNP_ID
        AND LNK_ONR_LTP_ID = P_LINK_TYPE AND LNK_ONR_ID = P_LINK
      LEFT OUTER JOIN con_contact_types ON CTT_ID = LNK_X_ID
      LEFT OUTER JOIN con_contact_point_types ON CPT_ID = CNP_CPT_ID
    WHERE
      CNP_ID = P_CNP_ID
    INTO vType,vSpeakTo,vMethod,vContact;

    IF (vType IS NOT NULL) and (length(vType) > 0) THEN
      SET vFullContact = vType;
    END IF;

    IF (vMethod IS NOT NULL) and (LENGTH(vMethod) > 0) THEN
      IF LENGTH(vFullContact) > 0 THEN
        SET vFullContact = concat(vFullContact,' ');
	  END IF;
	  SET vFullContact = concat(vFullContact,vMethod);
    END IF;

    IF (vContact IS NOT NULL) and (LENGTH(vContact) > 0) THEN
      IF LENGTH(vFullContact) > 0 THEN
        SET vFullContact = concat(vFullContact,': ');
	    END IF;
	    SET vFullContact = concat(vFullContact,vContact);
    END IF;

    IF (vSpeakTo IS NOT NULL) and (LENGTH(vSpeakTo) > 0) THEN
      IF LENGTH(vFullContact) > 0 THEN
        SET vFullContact = concat(vFullContact,' Speak To ');
	    END IF;
      SET vFullContact = concat(vFullContact,vSpeakTo);
	  END IF;
  END IF;
RETURN vFullContact;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `fn_con_sep_address` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE FUNCTION `fn_con_sep_address`(
  P_ADR_ID INT UNSIGNED,
  P_SEPARATOR VARCHAR(10)) RETURNS varchar(1125) CHARSET utf8
BEGIN
  DECLARE vFullAddr VARCHAR(1125);
  DECLARE vLine1 VARCHAR(250);
  DECLARE vLine2 VARCHAR(250);
  DECLARE vPostTown VARCHAR(100);
  DECLARE vCounty VARCHAR(100);
  DECLARE vCountry VARCHAR(100);
  DECLARE vPostCode VARCHAR(15);

  SET vFullAddr = '';

  IF NOT EXISTS(
    SELECT ADR_ID
    FROM con_addresses
    WHERE ADR_ID = P_ADR_ID) THEN
    SET vFullAddr = 'None';
  ELSE
    SELECT
      ADR_LINE1,
      ADR_LINE2,
      ADR_POST_TOWN,
      ADR_COUNTY,
      CNTRY_A3C,
      ADR_POSTCODE
    FROM
      con_addresses
      LEFT OUTER JOIN con_countries ON CNTRY_ID = ADR_CNTRY_ID
    WHERE
      ADR_ID = P_ADR_ID
    INTO vLine1,vLine2,vPostTown,vCounty,vCountry,vPostCode;

    IF (vLine1 IS NOT NULL) and (LENGTH(vLine1) > 0) THEN
      IF LENGTH(vFullAddr) > 0 THEN
        SET vFullAddr = concat(vFullAddr,P_SEPARATOR);
	  END IF;
	  SET vFullAddr = concat(vFullAddr,vLine1);
    END IF;

    IF (vLine2 IS NOT NULL) and (LENGTH(vLine2) > 0) THEN
      IF LENGTH(vFullAddr) > 0 THEN
        SET vFullAddr = concat(vFullAddr,P_SEPARATOR);
	  END IF;
	  SET vFullAddr = concat(vFullAddr,vLine2);
    END IF;

    IF (vPostTown IS NOT NULL) and (LENGTH(vPostTown) > 0) THEN
      IF LENGTH(vFullAddr) > 0 THEN
        SET vFullAddr = concat(vFullAddr,P_SEPARATOR);
	  END IF;
	  SET vFullAddr = concat(vFullAddr,vPostTown);
    END IF;

    IF (vCounty IS NOT NULL) and (LENGTH(vCounty) > 0) THEN
      IF LENGTH(vFullAddr) > 0 THEN
        SET vFullAddr = concat(vFullAddr,P_SEPARATOR);
	  END IF;
	  SET vFullAddr = concat(vFullAddr,vCounty);
    END IF;

    IF (vCountry IS NOT NULL) and (LENGTH(vCountry) > 0) THEN
      IF LENGTH(vFullAddr) > 0 THEN
        SET vFullAddr = concat(vFullAddr,P_SEPARATOR);
	  END IF;
	  SET vFullAddr = concat(vFullAddr,vCountry);
    END IF;

    IF (vPostCode IS NOT NULL) and (LENGTH(vPostCode) > 0) THEN
      IF LENGTH(vFullAddr) > 0 THEN
        SET vFullAddr = concat(vFullAddr,P_SEPARATOR);
      END IF;
	  SET vFullAddr = concat(vFullAddr,vPostCode);
    END IF;
  END IF;
RETURN vFullAddr;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `fn_con_sep_link_address` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE FUNCTION `fn_con_sep_link_address`(
  P_LINK_TYPE INT UNSIGNED,
  P_LINK INT UNSIGNED,
  P_ADR_ID INT UNSIGNED,
  P_SEPARATOR VARCHAR(10)) RETURNS varchar(1125) CHARSET utf8
BEGIN
  DECLARE vFullAddr VARCHAR(1125);
  DECLARE vType VARCHAR(50);
  DECLARE vCareOf VARCHAR(100);
  DECLARE vLine1 VARCHAR(250);
  DECLARE vLine2 VARCHAR(250);
  DECLARE vPostTown VARCHAR(100);
  DECLARE vCounty VARCHAR(100);
  DECLARE vCountry VARCHAR(100);
  DECLARE vPostCode VARCHAR(15);

  SET vFullAddr = '';

  IF NOT EXISTS(
    SELECT ADR_ID
    FROM con_addresses
    WHERE ADR_ID = P_ADR_ID) THEN
    SET vFullAddr = 'None';
  ELSE
    SELECT
      CTT_NAME,
      LNK_X_STR,
      ADR_LINE1,
      ADR_LINE2,
      ADR_POST_TOWN,
      ADR_COUNTY,
      CNTRY_A3C,
      ADR_POSTCODE
    FROM
      con_addresses
      JOIN lnk_links ON LNK_CHD_LTP_ID = 2 AND LNK_CHD_ID = ADR_ID
        AND LNK_ONR_LTP_ID = P_LINK_TYPE AND LNK_ONR_ID = P_LINK
      LEFT OUTER JOIN con_contact_types ON CTT_ID = LNK_X_ID
      LEFT OUTER JOIN con_countries ON CNTRY_ID = ADR_CNTRY_ID
    WHERE
      ADR_ID = P_ADR_ID
    INTO vType,vCareOf,vLine1,vLine2,vPostTown,vCounty,vCountry,vPostCode;

    IF (vCareOf IS NOT NULL) and (length(vCareOf) > 0) THEN
      SET vFullAddr = concat(vFullAddr,'C/O ',vCareOf);
    END IF;

    IF (vLine1 IS NOT NULL) and (LENGTH(vLine1) > 0) THEN
      IF LENGTH(vFullAddr) > 0 THEN
        SET vFullAddr = concat(vFullAddr,P_SEPARATOR);
	  END IF;
	  SET vFullAddr = concat(vFullAddr,vLine1);
    END IF;

    IF (vLine2 IS NOT NULL) and (LENGTH(vLine2) > 0) THEN
      IF LENGTH(vFullAddr) > 0 THEN
        SET vFullAddr = concat(vFullAddr,P_SEPARATOR);
	  END IF;
	  SET vFullAddr = concat(vFullAddr,vLine2);
    END IF;

    IF (vPostTown IS NOT NULL) and (LENGTH(vPostTown) > 0) THEN
      IF LENGTH(vFullAddr) > 0 THEN
        SET vFullAddr = concat(vFullAddr,P_SEPARATOR);
	  END IF;
	  SET vFullAddr = concat(vFullAddr,vPostTown);
    END IF;

    IF (vCounty IS NOT NULL) and (LENGTH(vCounty) > 0) THEN
      IF LENGTH(vFullAddr) > 0 THEN
        SET vFullAddr = concat(vFullAddr,P_SEPARATOR);
	  END IF;
	  SET vFullAddr = concat(vFullAddr,vCounty);
    END IF;

    IF (vCountry IS NOT NULL) and (LENGTH(vCountry) > 0) THEN
      IF LENGTH(vFullAddr) > 0 THEN
        SET vFullAddr = concat(vFullAddr,P_SEPARATOR);
	  END IF;
	  SET vFullAddr = concat(vFullAddr,vCountry);
    END IF;

    IF (vPostCode IS NOT NULL) and (LENGTH(vPostCode) > 0) THEN
      IF LENGTH(vFullAddr) > 0 THEN
        SET vFullAddr = concat(vFullAddr,P_SEPARATOR);
      END IF;
	  SET vFullAddr = concat(vFullAddr,vPostCode);
    END IF;

	IF (vType IS NOT NULL) and (length(vType) > 0) THEN
      SET vFullAddr = concat(vType,': ',vFullAddr);
    END IF;
  END IF;
RETURN vFullAddr;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `fn_pro_state_complete` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE FUNCTION `fn_pro_state_complete`(
  P_TSS_ID INT UNSIGNED) RETURNS bit(1)
BEGIN
  DECLARE vComplete BIT;
  SELECT
    TSS_COMPLETE
  FROM
    pro_task_states
  WHERE
    TSS_ID = P_TSS_ID
  INTO vComplete;
  RETURN IFNULL(vComplete,0);
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `fn_pro_state_name` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE FUNCTION `fn_pro_state_name`(
  P_TSS_ID INT UNSIGNED) RETURNS varchar(50) CHARSET utf8
BEGIN
  DECLARE vName VARCHAR(50);
  SELECT
    TSS_NAME
  FROM
    pro_task_states
  WHERE
    TSS_ID = P_TSS_ID
  INTO vName;
  RETURN vName;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `fn_sch_schedule_type_name` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE FUNCTION `fn_sch_schedule_type_name`(
  P_SCT_ID INT UNSIGNED) RETURNS varchar(50) CHARSET utf8
BEGIN
  DECLARE vName VARCHAR(50);
  SELECT
    SCT_NAME
  FROM
    sch_schedule_types
  WHERE
    SCT_ID = P_SCT_ID
  INTO vName;

RETURN vName;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_con_addedit_link_address` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE PROCEDURE `sp_con_addedit_link_address`(
  IN P_LINK_TYPE INT UNSIGNED,
  IN P_LINK_ID INT UNSIGNED,
  INOUT P_ADR_ID INT UNSIGNED,
  IN P_ADL_CTT_ID INT UNSIGNED,
  IN P_ADL_CARE_OF VARCHAR(100),
  IN P_ADR_LINE1 VARCHAR(250),
  IN P_ADR_LINE2 VARCHAR(250),
  IN P_ADR_POST_TOWN VARCHAR(100),
  IN P_ADR_COUNTY VARCHAR(100),
  IN P_ADR_CNTRY_ID INT,
  IN P_ADR_POSTCODE VARCHAR(15),
  OUT P_RETURN_CODE INT,
  OUT P_RETURN_MSG VARCHAR(1000))
BEGIN
  /*XCal - Supported Return Codes
    -1 = Link Type Not Supported
    1 = Added Address and Link
    2 = Existing record(s) updated
    3 = Address updated/ignored. Link Added
  */
  DECLARE vADR_LINE1 VARCHAR(250);
  DECLARE vADR_LINE2 VARCHAR(250);
  DECLARE vADR_POST_TOWN VARCHAR(100);
  DECLARE vADR_COUNTY VARCHAR(100);
  DECLARE vADR_CNTRY_ID INT;
  DECLARE vADR_POSTCODE VARCHAR(15);

  /*XCal - First let's validate that the link type is allowed*/
  IF EXISTS(
    SELECT LTA_SR
    FROM lnk_link_type_avail
    WHERE LTA_ONR_LTP_ID = P_LINK_TYPE
    AND LTA_CHD_LTP_ID = 2)
  THEN
    /*XCal - If the address is being created and there are details add and link it*/
    IF (P_ADR_ID = NULL) OR (P_ADR_ID = 0) THEN
      IF (P_ADR_LINE1 <> '') OR (P_ADR_LINE2 <> '')
        OR (P_ADR_POST_TOWN <> '') OR (P_ADR_COUNTY <> '') OR (P_ADR_POSTCODE <> '')
	  THEN
        INSERT INTO con_addresses (
          ADR_LINE1,ADR_LINE2,ADR_POST_TOWN,
          ADR_COUNTY,ADR_CNTRY_ID,ADR_POSTCODE,ADR_UPDATED)
        VALUES (
          P_ADR_LINE1,P_ADR_LINE2,P_ADR_POST_TOWN,
          P_ADR_COUNTY,P_ADR_CNTRY_ID,P_ADR_POSTCODE,current_timestamp());

        SET P_ADR_ID = LAST_INSERT_ID();
        INSERT INTO lnk_links (
          LNK_ONR_LTP_ID,LNK_ONR_ID,LNK_CHD_LTP_ID,LNK_CHD_ID,LNK_X_ID,LNK_X_STR,LNK_UPDATED)
	    VALUES (
          P_LINK_TYPE,P_LINK_ID,2,P_ADR_ID,P_ADL_CTT_ID,P_ADL_CARE_OF,current_timestamp());

        SET P_RETURN_CODE = 1;
        SET P_RETURN_MSG = 'Address and Link records added.';
  	  END IF;
    ELSE /*XCal - If we're linking or updating an existing address */
      SELECT
        ADR_LINE1,ADR_LINE2,ADR_POST_TOWN,
        ADR_COUNTY,ADR_CNTRY_ID,ADR_POSTCODE
	  FROM
        con_addresses
  	  WHERE
        ADR_ID = P_ADR_ID
	  INTO vADR_LINE1,vADR_LINE2,vADR_POST_TOWN,
        vADR_COUNTY,vADR_CNTRY_ID,vADR_POSTCODE;

      /*XCal - Only update the address if it's been changed */
      IF (vADR_LINE1 <> P_ADR_LINE1) OR (vADR_LINE2 <> P_ADR_LINE2) OR
        (vADR_POST_TOWN <> P_ADR_POST_TOWN) OR (vADR_COUNTY <> P_ADR_COUNTY) OR
        (vADR_CNTRY_ID <> P_ADR_CNTRY_ID) OR (vADR_POSTCODE <> P_ADR_POSTCODE)
      THEN
        UPDATE con_addresses SET
          ADR_LINE1 = P_ADR_LINE1,
          ADR_LINE2 = P_ADR_LINE2,
          ADR_POST_TOWN = P_ADR_POST_TOWN,
          ADR_COUNTY = P_ADR_COUNTY,
          ADR_CNTRY_ID = P_ADR_CNTRY_ID,
          ADR_POSTCODE = P_ADR_POSTCODE,
          ADR_UPDATED = current_timestamp()
        WHERE
          ADR_ID = P_ADR_ID;
		SET P_RETURN_CODE = 2;
		SET P_RETURN_MSG = 'Existing address updated.';
	  ELSE
        SET P_RETURN_MSG = 'Address not updated, no changes detected.';
      END IF;

      /*XCal - Update the link record if it exists to show when it was last confirmed,
      add the link record if it doesn't exist */
      IF EXISTS(
        SELECT LNK_UPDATED
        FROM lnk_links
        WHERE LNK_ONR_LTP_ID = P_LINK_TYPE
        AND LNK_ONR_ID = P_LINK_ID
        AND LNK_CHD_LTP_ID = 2
        AND LNK_CHD_ID = P_ADR_ID)
      THEN
        UPDATE lnk_links SET
          LNK_X_ID = P_ADL_CTT_ID,
          LNK_X_STR = P_ADL_CARE_OF,
          LNK_UPDATED = current_timestamp()
        WHERE LNK_ONR_LTP_ID = P_LINK_TYPE
          AND LNK_ONR_ID = P_LINK_ID
          AND LNK_CHD_LTP_ID = 2
          AND LNK_CHD_ID = P_ADR_ID;

		SET P_RETURN_CODE = 2;
        IF LENGTH(P_RETURN_MSG) > 0 THEN
          SET P_RETURN_MSG = CONCAT(P_RETURN_MSG,' ');
		ELSE
          SET P_RETURN_MSG = '';
		END IF;
		SET P_RETURN_MSG = CONCAT(P_RETURN_MSG,'Existing Link Record Updated.');
	  ELSE
        INSERT INTO lnk_links (
          LNK_ONR_LTP_ID,
          LNK_ONR_ID,
          LNK_CHD_LTP_ID,
          LNK_CHD_ID,
          LNK_X_ID,
          LNK_X_STR,
          LNK_UPDATED)
  	    VALUES (
          P_LINK_TYPE,
          P_LINK_ID,
          2,
          P_ADR_ID,
          P_ADL_CTT_ID,
          P_ADL_CARE_OF,
          current_timestamp());

		SET P_RETURN_CODE = 3;
        IF LENGTH(P_RETURN_MSG) > 0 THEN
          SET P_RETURN_MSG = CONCAT(P_RETURN_MSG,' ');
		ELSE
          SET P_RETURN_MSG = '';
		END IF;
		SET P_RETURN_MSG = CONCAT(P_RETURN_MSG,' Link record added.');
      END IF;
    END IF;
  ELSE
    SET P_RETURN_CODE = -1;
    SET P_RETURN_MSG = 'The requested link type is not allowed';
  END IF;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_con_addedit_link_contact_point` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE PROCEDURE `sp_con_addedit_link_contact_point`(
  IN P_LINK_TYPE INT UNSIGNED,
  IN P_LINK_ID INT UNSIGNED,
  INOUT P_CNP_ID INT UNSIGNED,
  IN P_CPL_CTT_ID INT UNSIGNED,
  IN P_CPL_SPEAK_TO VARCHAR(100),
  IN P_CNP_CPT_ID INT UNSIGNED,
  IN P_CNP_CONTACT VARCHAR(250),
  OUT P_RETURN_CODE INT,
  OUT P_RETURN_MSG VARCHAR(1000))
BEGIN
  /*XCal - Supported Return Codes
    -1 = Link type not supported
    1 = Contact point and link added
    2 = Existing records updated
    3 = Contact point updated/ignored, link added
  */
  DECLARE vCNP_CPT_ID INT UNSIGNED;
  DECLARE vCNP_CONTACT VARCHAR(250);

  /*XCal - First we'll ensure the link to link type is supported*/
  IF EXISTS(
    SELECT
      LTA_SR
	FROM
      lnk_link_type_avail
	WHERE
      LTA_ONR_LTP_ID = P_LINK_TYPE
      AND LTA_CHD_LTP_ID = 3)
  THEN
    /*XCal - If the contact point is being created and there are details add and link it*/
    IF (P_CNP_ID = NULL) OR (P_CNP_ID = 0) THEN
      IF (P_CNP_CONTACT <> '') THEN
        INSERT INTO con_contact_points (
          CNP_CPT_ID,CNP_CONTACT,CNP_UPDATED)
        VALUES (
          P_CNP_CPT_ID,P_CNP_CONTACT,current_timestamp());

	    SET P_CNP_ID = LAST_INSERT_ID();
        INSERT INTO lnk_links (
          LNK_ONR_LTP_ID,LNK_ONR_ID,LNK_CHD_LTP_ID,LNK_CHD_ID,LNK_X_ID,LNK_X_STR,LNK_UPDATED)
	    VALUES (
          P_LINK_TYPE,P_LINK_ID,3,P_CNP_ID,P_CPL_CTT_ID,P_CPL_SPEAK_TO,current_timestamp());

        SET P_RETURN_CODE = 1;
        SET P_RETURN_MSG = 'Contact point and link records added.';
  	  END IF;
    ELSE /*XCal - If we're linking or updating an existing an existing contact point */
      SELECT
        CNP_CPT_ID,CNP_CONTACT
	  FROM
        con_contact_points
	  WHERE
        CNP_ID = P_CNP_ID
	  INTO vCNP_CPT_ID,vCNP_CONTACT;

      /*XCal - Only update the contact point if it's been changed */
      IF (vCNP_CPT_ID <> P_CNP_CPT_ID) OR
	  (vCNP_CONTACT <> P_CNP_CONTACT) THEN
        UPDATE con_contact_points SET
          CNP_CPT_ID = P_CNP_CPT_ID,
          CNP_CONTACT = P_CNP_CONTACT,
          CNP_UPDATED = current_timestamp()
        WHERE
          CNP_ID = P_CNP_ID;

        SET P_RETURN_CODE = 2;
        SET P_RETURN_MSG = 'Existing contact point updated.';
	  ELSE
        SET P_RETURN_MSG = 'Contact point not updated, no changes detected.';
      END IF;

      /*XCal - Update the link record if it exists to show when it was last confirmed,
      add the link record if it doesn't exist */
      IF EXISTS(
        SELECT LNK_UPDATED
        FROM lnk_links
        WHERE LNK_ONR_LTP_ID = P_LINK_TYPE
        AND LNK_ONR_ID = P_LINK_ID
        AND LNK_CHD_LTP_ID = 3
        AND LNK_CHD_ID = P_CNP_ID)
	  THEN
        UPDATE lnk_links SET
          LNK_X_ID = P_CPL_CTT_ID,
          LNK_X_STR = P_CPL_SPEAK_TO,
          LNK_UPDATED = current_timestamp()
        WHERE LNK_ONR_LTP_ID = P_LINK_TYPE
          AND LNK_ONR_ID = P_LINK_ID
          AND LNK_CHD_LTP_ID = 3
          AND LNK_CHD_ID = P_CNP_ID;

		SET P_RETURN_CODE = 2;
        IF LENGTH(P_RETURN_MSG) > 0 THEN
          SET P_RETURN_MSG = CONCAT(P_RETURN_MSG,' ');
		ELSE
          SET P_RETURN_MSG = '';
		END IF;
		SET P_RETURN_MSG = CONCAT(P_RETURN_MSG,'Existing Link Record Updated.');
	  ELSE
        INSERT INTO lnk_links (
          LNK_ONR_LTP_ID,
          LNK_ONR_ID,
          LNK_CHD_LTP_ID,
          LNK_CHD_ID,
          LNK_X_ID,
          LNK_X_STR,
          LNK_UPDATED)
	    VALUES (
          P_LINK_TYPE,
          P_LINK_ID,
          3,
          P_CNP_ID,
          P_CPL_CTT_ID,
          P_CPL_SPEAK_TO,
          current_timestamp());

		SET P_RETURN_CODE = 3;
        IF LENGTH(P_RETURN_MSG) > 0 THEN
          SET P_RETURN_MSG = CONCAT(P_RETURN_MSG,' ');
		ELSE
          SET P_RETURN_MSG = '';
		END IF;
		SET P_RETURN_MSG = CONCAT(P_RETURN_MSG,' Link record added.');
      END IF;
    END IF;
  ELSE
    SET P_RETURN_CODE = -1;
    SET P_RETURN_MSG = 'The requested link type is not allowed.';
  END IF;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_con_addedit_link_person_only` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE PROCEDURE `sp_con_addedit_link_person_only`(
  IN P_LINK_TYPE INT UNSIGNED,
  IN P_LINK_ID INT UNSIGNED,
  INOUT P_PER_ID INT UNSIGNED,
  IN P_PER_TIT_ID INT UNSIGNED,
  IN P_PER_FORENAMES VARCHAR(200),
  IN P_PER_SURNAME VARCHAR(100),
  OUT P_RETURN_CODE INT,
  OUT P_RETURN_MSG VARCHAR(1000))
BEGIN
  /*XCal - Supported Return Codes
    -1 = Link Type Not Supported
    1 = Added Person and Link
    2 = Existing record(s) updated
    3 = Person updated/ignored. Link Added
  */
  DECLARE vPER_TIT_ID INT UNSIGNED;
  DECLARE vPER_FORENAMES VARCHAR(200);
  DECLARE vPER_SURNAME VARCHAR(100);

  /*XCal - First we'll ensure the link to link type is supported*/
  IF EXISTS(
    SELECT
      LTA_SR
	FROM
      lnk_link_type_avail
	WHERE
      LTA_ONR_LTP_ID = P_LINK_TYPE
      AND LTA_CHD_LTP_ID = 1)
  THEN
    /*XCal - If the person is being created and there are details add and link it*/
    IF (P_PER_ID = NULL) OR (P_PER_ID = 0) THEN
      IF (P_PER_FORENAMES <> '') OR (P_PER_SURNAME <> '') THEN
        INSERT INTO con_people (
          PER_TIT_ID,PER_FORENAMES,PER_SURNAME,PER_UPDATED)
        VALUES (
          P_PER_TIT_ID,P_PER_FORENAMES,P_PER_SURNAME,current_timestamp());

  	    SET P_PER_ID = LAST_INSERT_ID();
        INSERT INTO lnk_links (
          LNK_ONR_LTP_ID,LNK_ONR_ID,LNK_CHD_LTP_ID,LNK_CHD_ID,LNK_UPDATED)
	    VALUES (
          P_LINK_TYPE,P_LINK_ID,1,P_PER_ID,current_timestamp());

          SET P_RETURN_CODE = 1;
          SET P_RETURN_MSG = 'Person and Link records added.';
	  END IF;
    ELSE /*XCal - If we're linking or updating an existing person */
      SELECT
        PER_TIT_ID,PER_FORENAMES,PER_SURNAME
	  FROM
        con_people
  	  WHERE
        PER_ID = P_PER_ID
   	  INTO vPER_TIT_ID,vPER_FORENAMES,vPER_SURNAME;

      /*XCal - Only update the person if it's been changed */
      IF (vPER_TIT_ID <> P_PER_TIT_ID) OR
	  (vPER_FORENAMES <> P_PER_FORENAMES) OR (vPER_SURNAME <> P_PER_SURNAME) THEN
        UPDATE con_people SET
          PER_TIT_ID = P_PER_TIT_ID,
          PER_FORENAMES = P_PER_FORENAMES,
          PER_SURNAME = P_PER_SURNAME,
          PER_UPDATED = current_timestamp()
        WHERE
          PER_ID = P_PER_ID;
	    SET P_RETURN_CODE = 2;
	    SET P_RETURN_MSG = 'Existing person updated.';
	  ELSE
	    SET P_RETURN_MSG = 'Person not updated, no changes detected.';
	  END IF;

      /*XCal - Update the link record if it exists to show when it was last confirmed,
      add the link record if it doesn't exist */
      IF EXISTS(
        SELECT LNK_UPDATED
        FROM lnk_links
        WHERE LNK_ONR_LTP_ID = P_LINK_TYPE
        AND LNK_ONR_ID = P_LINK_ID
        AND LNK_CHD_LTP_ID = 1
        AND LNK_CHD_ID = P_PER_ID)
	  THEN
        UPDATE lnk_links SET
          LNK_UPDATED = current_timestamp()
        WHERE LNK_ONR_LTP_ID = P_LINK_TYPE
          AND LNK_ONR_ID = P_LINK_ID
          AND LNK_CHD_LTP_ID = 1
          AND LNK_CHD_ID = P_PER_ID;

		SET P_RETURN_CODE = 2;
        IF LENGTH(P_RETURN_MSG) > 0 THEN
          SET P_RETURN_MSG = CONCAT(P_RETURN_MSG,' ');
		ELSE
          SET P_RETURN_MSG = '';
		END IF;
		SET P_RETURN_MSG = CONCAT(P_RETURN_MSG,'Existing Link Record Updated.');
	  ELSE
        INSERT INTO lnk_links (
          LNK_ONR_LTP_ID,
          LNK_ONR_ID,
          LNK_CHD_LTP_ID,
          LNK_CHD_ID_ID,
          PLN_UPDATED)
	    VALUES (
          P_LINK_TYPE,
          P_LINK_ID,
          1,
          P_PER_ID,
          current_timestamp());

		SET P_RETURN_CODE = 3;
        IF LENGTH(P_RETURN_MSG) > 0 THEN
          SET P_RETURN_MSG = CONCAT(P_RETURN_MSG,' ');
		ELSE
          SET P_RETURN_MSG = '';
		END IF;
		SET P_RETURN_MSG = CONCAT(P_RETURN_MSG,' Link record added.');
      END IF;
    END IF;
  ELSE
    SET P_RETURN_CODE = -1;
    SET P_RETURN_MSG = 'The requested link type is not allowed.';
  END IF;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_con_addedit_person_full` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE PROCEDURE `sp_con_addedit_person_full`(
  INOUT P_PER_ID INT UNSIGNED,
  IN P_PER_TIT_ID INT UNSIGNED,
  IN P_PER_FORENAMES VARCHAR(200),
  IN P_PER_SURNAME VARCHAR(100),
  IN P_PER_DOB DATETIME,
  INOUT P_PER_ADR_ID INT UNSIGNED,
  INOUT P_PER_CNP_ID INT UNSIGNED,
  IN P_ADL_CTT_ID INT UNSIGNED,
  IN P_ADL_CARE_OF VARCHAR(100),
  IN P_ADR_LINE1 VARCHAR(250),
  IN P_ADR_LINE2 VARCHAR(250),
  IN P_ADR_POST_TOWN VARCHAR(100),
  IN P_ADR_COUNTY VARCHAR(100),
  IN P_ADR_CNTRY_ID INT UNSIGNED,
  IN P_ADR_POSTCODE VARCHAR(15),
  IN P_CPL_CTT_ID INT UNSIGNED,
  IN P_CPL_SPEAK_TO VARCHAR(100),
  IN P_CNP_CPT_ID INT UNSIGNED,
  IN P_CNP_CONTACT VARCHAR(250),
  OUT P_RETURN_CODE INT,
  OUT P_RETURN_MSG VARCHAR(1000))
BEGIN
  /*XCal - Supported Return Codes
    -1 = Name parameters not populated
    0 = No changes detected, nothing updated
    1 = Added Person and both records
    2 = Added Person and Address
    3 = Added Person and Contact Point
    4 = Add Address and Contact Point
    5 = Added Address Only
    6 = Added Contact Point Only
    7 = Just updates to existing records
  */
  DECLARE vPER_TIT_ID INT UNSIGNED;
  DECLARE vPER_FORENAMES VARCHAR(200);
  DECLARE vPER_SURNAME VARCHAR(100);
  DECLARE vPER_DOB DATETIME;
  DECLARE vPER_ADR_ID INT UNSIGNED;
  DECLARE vPER_CNP_ID INT UNSIGNED;
  DECLARE vADR_CARE_OF VARCHAR(250);
  DECLARE vADR_LINE1 VARCHAR(250);
  DECLARE vADR_LINE2 VARCHAR(250);
  DECLARE vADR_POST_TOWN VARCHAR(100);
  DECLARE vADR_COUNTY VARCHAR(100);
  DECLARE vADR_CNTRY_ID INT UNSIGNED;
  DECLARE vADR_POSTCODE VARCHAR(15);
  DECLARE vCNP_CPT_ID INT UNSIGNED;
  DECLARE vCNP_CONTACT VARCHAR(250);
  DECLARE vChangedSomething BIT;
  DECLARE vAddedPerson BIT;
  DECLARE vAddedAddress BIT;
  DECLARE vAddedContactPoint BIT;

  /*XCal - Meanwhile... lets only do this if they've specified a name */
  IF (length(trim(P_PER_FORENAMES)) > 0) OR (length(trim(P_PER_SURNAME)) > 0) THEN
    SET vChangedSomething = 0;
    /*XCal - Add or edit the address if has detaisl or details changed*/
    IF (P_PER_ADR_ID = NULL) OR (P_PER_ADR_ID = 0) THEN
      IF (P_ADL_CARE_OF <> '') OR (P_ADR_LINE1 <> '') OR (P_ADR_LINE2 <> '')
      OR (P_ADR_POST_TOWN <> '') OR (P_ADR_COUNTY <> '') OR (P_ADR_POSTCODE <> '') THEN
        INSERT INTO con_addresses (
          ADR_LINE1,ADR_LINE2,ADR_POST_TOWN,
          ADR_COUNTY,ADR_CNTRY_ID,ADR_POSTCODE,ADR_UPDATED)
        VALUES (
          P_ADR_LINE1,P_ADR_LINE2,P_ADR_POST_TOWN,
          P_ADR_COUNTY,P_ADR_CNTRY_ID,P_ADR_POSTCODE,current_timestamp());

	    SET P_PER_ADR_ID = LAST_INSERT_ID();
        SET vAddedAddress = 1;
	  END IF;
    ELSE
      SELECT
       ADR_LINE1,ADR_LINE2,ADR_POST_TOWN,
        ADR_COUNTY,ADR_CNTRY_ID,ADR_POSTCODE
	  FROM
        con_addresses
	  WHERE
        ADR_ID = P_PER_ADR_ID
      INTO vADR_LINE1,vADR_LINE2,vADR_POST_TOWN,
        vADR_COUNTY,vADR_CNTRY_ID,vADR_POSTCODE;

      IF (vADR_LINE1 <> P_ADR_LINE1) OR (vADR_LINE2 <> P_ADR_LINE2) OR
      (vADR_POST_TOWN <> P_ADR_POST_TOWN) OR (vADR_COUNTY <> P_ADR_COUNTY) OR
      (vADR_CNTRY_ID <> P_ADR_CNTRY_ID) OR (vADR_POSTCODE <> P_ADR_POSTCODE) THEN
        UPDATE con_addresses SET
          ADR_LINE1 = P_ADR_LINE1,
          ADR_LINE2 = P_ADR_LINE2,
          ADR_POST_TOWN = P_ADR_POST_TOWN,
          ADR_COUNTY = P_ADR_COUNTY,
          ADR_CNTRY_ID = P_ADR_CNTRY_ID,
          ADR_POSTCODE = P_ADR_POSTCODE,
          ADR_UPDATED = current_timestamp()
        WHERE
          ADR_ID = P_PER_ADR_ID;
		SET vChangedSomething = 1;
      END IF;
    END IF;

    /*XCal - Add or edit the contact point if it has details or has changed */
    IF (P_PER_CNP_ID = NULL) OR (P_PER_CNP_ID = 0) THEN
      IF P_CNP_CONTACT <> '' THEN
        INSERT INTO con_contact_points (
          CNP_CPT_ID,CNP_CONTACT,CNP_UPDATED)
        VALUES (
          P_CNP_CPT_ID,P_CNP_CONTACT,current_timestamp());

	    SET P_PER_CNP_ID = LAST_INSERT_ID();
        SET vAddedContactPoint = 1;
      END IF;
    ELSE
      SELECT CNP_CPT_ID,CNP_CONTACT
      FROM
        con_contact_points
      WHERE
        CNP_ID = P_PER_CNP_ID
      INTO vCNP_CPT_ID,vCNP_CONTACT;

      IF (vCNP_CPT_ID <> P_CNP_CPT_ID) OR (vCNP_CONTACT <> P_CNP_CONTACT) THEN
        UPDATE con_contact_points SET
          CNP_CPT_ID = P_CNP_CPT_ID,
          CNP_CONTACT = P_CNP_CONTACT,
          CNP_UPDATED = current_timestamp()
	    WHERE CNP_ID = P_PER_CNP_ID;
        SET vChangedSomething = 1;
      END IF;
    END IF;

    /*XCal - Add or edit the person if it has details or has changed*/
    IF (P_PER_ID = NULL) OR (P_PER_ID = 0) THEN
      INSERT INTO con_people (
        PER_TIT_ID,PER_FORENAMES,PER_SURNAME,PER_DOB,PER_ADR_ID,
        PER_CNP_ID,PER_UPDATED)
	  VALUES (
        P_PER_TIT_ID,P_PER_FORENAMES,P_PER_SURNAME,P_PER_DOB,P_PER_ADR_ID,
        P_PER_CNP_ID,current_timestamp());
	  SET P_PER_ID = LAST_INSERT_ID();
      SET vAddedPerson = 1;
    ELSE
      SELECT PER_TIT_ID,PER_FORENAMES,PER_SURNAME,PER_DOB,PER_ADR_ID,PER_CNP_ID
      FROM con_people
      WHERE PER_ID = P_PER_ID
      INTO vPER_TIT_ID,vPER_FORENAMES,vPER_SURNAME,vPER_DOB,vPER_ADR_ID,vPER_CNP_ID;

      IF (vPER_TIT_ID <> P_PER_TIT_ID) OR (vPER_FORENAMES <> P_PER_FORENAMES) OR
        (vPER_SURNAME <> P_PER_SURNAME) OR (vPER_DOB <> P_PER_DOB) OR
        (vPER_ADR_ID <> P_PER_ADR_ID) OR (vPER_CNP_ID <> P_PER_CNP_ID) OR
        (vAddedAddress = 1) OR (vAddedContactPoint = 1)
	  THEN
        UPDATE con_people SET
          PER_TIT_ID = P_PER_TIT_ID,
          PER_FORENAMES = P_PER_FORENAMES,
          PER_SURNAME = P_PER_SURNAME,
          PER_DOB = P_PER_DOB,
          PER_ADR_ID = P_PER_ADR_ID,
          PER_CNP_ID = P_PER_CNP_ID,
          PER_UPDATED = current_timestamp()
	    WHERE
          PER_ID = P_PER_ID;
          SET vChangedSomething = 1;
	  END IF;
    END IF;

    /*XCal - If we have an address add or update the link */
    IF (P_PER_ADR_ID IS NOT NULL) AND (P_PER_ADR_ID <> 0) THEN
      /*XCal - Only add the link if the system supports linking addresses to people. If not primary address only*/
      IF EXISTS(
        SELECT
          LTA_SR
		FROM
          lnk_link_type_avail
		WHERE
          LTA_ONR_LTP_ID = 1
          AND LTA_CHD_LTP_ID = 2)
	  THEN
        IF EXISTS(
          SELECT LNK_UPDATED
          FROM lnk_links
          WHERE LNK_ONR_LTP_ID = 1
          AND LNK_ONR_ID = P_PER_ID
          AND LNK_CHD_LTP_ID = 2
          AND LNK_CHD_ID = P_PER_ADR_ID)
	    THEN
          UPDATE lnk_links SET
            LNK_X_ID = P_ADL_CTT_ID,
            LNK_X_STR = P_ADL_CARE_OF,
            LNK_UPDATED = current_timestamp()
          WHERE
            LNK_ONR_LTP_ID = 1
            AND LNK_ONR_ID = P_PER_ID
            AND LNK_CHD_LTP_ID = 2
            AND LNK_CHD_ID = P_PER_ADR_ID;

		  SET vChangedSomething = 1;
	    ELSE
          INSERT INTO lnk_links (
            LNK_ONR_LTP_ID,
            LNK_ONR_ID,
            LNK_CHD_LTP_ID,
            LNK_CHD_ID,
            LNK_X_ID,
            LNK_X_STR,
            LNK_UPDATED)
	      VALUES (
            1,
            P_PER_ID,
            2,
            P_PER_ADR_ID,
            P_ADL_CTT_ID,
            P_ADL_CARE_OF,
            current_timestamp());

		  SET vChangedSomething = 1;
        END IF;
	  END IF;
    END IF;

    /*XCal - If we have a contact add or update the link*/
    IF (P_PER_CNP_ID IS NOT NULL) AND (P_PER_CNP_ID <> 0) THEN
    /*XCal - Only add the link if the system supports the link type*/
      IF EXISTS(
        SELECT
          LTA_SR
        FROM
          lnk_link_type_avail
        WHERE
          LTA_ONR_LTP_ID = 1
          AND LTA_CHD_LTP_ID = 3)
	  THEN
        IF EXISTS(
          SELECT LNK_UPDATED
          FROM lnk_links
          WHERE LNK_ONR_LTP_ID = 1
          AND LNK_ONR_ID = P_PER_ID
          AND LNK_CHD_LTP_ID = 3
          AND LNK_CHD_ID = P_PER_CNP_ID)
	    THEN
          UPDATE lnk_links SET
            LNK_X_ID = P_CPL_CTT_ID,
            LNK_X_STR = P_CPL_SPEAK_TO,
		    LNK_UPDATED = current_timestamp()
          WHERE LNK_ONR_LTP_ID = 1
            AND LNK_ONR_ID = P_PER_ID
            AND LNK_CHD_ID = 3
            AND LNK_CHD_ID = P_PER_CNP_ID;

          SET vChangedSomething = 1;
	    ELSE
          INSERT INTO lnk_links (
            LNK_ONR_LTP_ID,
            LNK_ONR_ID,
            LNK_CHD_LTP_ID,
            LNK_CHD_ID,
            LNK_X_ID,
            LNK_X_STR,
            LNK_UPDATED)
	      VALUES (
            1,
            P_PER_ID,
            3,
            P_PER_CNP_ID,
            P_CPL_CTT_ID,
            P_CPL_SPEAK_TO,
            current_timestamp());

		  SET vChangedSomething = 1;
        END IF;
	  END IF;
    END IF;

    IF (vAddedAddress = 1) AND (vAddedContactPoint = 1) AND (vAddedPerson = 1) THEN
      SET P_RETURN_CODE = 1;
      SET P_RETURN_MSG = 'Person, address and contact point added.';
	ELSEIF (vAddedAddress = 1) AND (vAddedPerson = 1) THEN
      SET P_RETURN_CODE = 2;
      SET P_RETURN_MSG = 'Person and address added.';
	ELSEIF (vAddedContactPoint = 1) AND (vAddedPerson = 1) THEN
      SET P_RETURN_CODE = 3;
      SET P_RETURN_MSG = 'Person and contact point added.';
	ELSEIF (vAddedAddress = 1) AND (vAddedContactPoint = 1) THEN
      SET P_RETURN_CODE = 4;
      SET P_RETURN_MSG = 'Address and contact point added.';
	ELSEIF (vAddedAddress = 1) THEN
      SET P_RETURN_CODE = 5;
      SET P_RETURN_MSG = 'Address added.';
	ELSEIF (vAddedContactPoint = 1) THEN
      SET P_RETURN_CODE = 6;
      SET P_RETURN_MSG = 'Contact point added.';
	ELSEIF (vChangedSomething = 1) THEN
      SET P_RETURN_CODE = 7;
      SET P_RETURN_MSG = 'No additions, existing records/links updated.';
	ELSE
      SET P_RETURN_CODE = 0;
      SET P_RETURN_MSG = 'No changes detected, nothing updated.';
	END IF;
  ELSE
    SET P_RETURN_CODE = -1;
    SET P_RETURN_MSG = 'No name values supplied, request ignored.';
  END IF;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_con_rem_contact_point_type` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE PROCEDURE `sp_con_rem_contact_point_type`(
  IN P_CPT_ID INT UNSIGNED,
  OUT P_RETURN_CODE INT,
  OUT P_RETURN_MSG VARCHAR(100))
BEGIN
  /*XCal - Supported return values
    -1 = Contact method ID not present to remove
    1 = Contact method deleted
    2 = Contact method in use, suppressed
  */
  IF EXISTS(
    SELECT
      CPT_ID
	FROM
      con_contact_point_types
	WHERE
      CPT_ID = P_CPT_ID)
  THEN
    IF NOT EXISTS(
      SELECT
        CNP_ID
	  FROM
        con_contact_points
	  WHERE
        CNP_CPT_ID = P_CPT_ID)
	THEN
      DELETE FROM con_contact_point_types
      WHERE CPT_ID = P_CPT_ID;

      SET P_RETURN_CODE = 1;
      SET P_RETURN_MSG = 'Contact method deleted.';
    ELSE
      UPDATE con_contact_point_types SET
        CPT_SR = 1,
        CPT_UPDATED = current_timestamp()
	  WHERE CPT_ID = P_CPT_ID;

	  SET P_RETURN_CODE = 2;
      SET P_RETURN_MSG = 'Contact method in use, record suppressed from searches.';
    END IF;
  ELSE
    SET P_RETURN_CODE = -1;
    SET P_RETURN_MSG = 'No record present to remove.';
  END IF;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_con_rem_contact_type` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE PROCEDURE `sp_con_rem_contact_type`(
  IN P_CTT_ID INT UNSIGNED,
  OUT P_RETURN_CODE INT,
  OUT P_RETURN_MSG VARCHAR(100))
BEGIN
  /*XCal - Supported return values
    -1 = Contact type ID not present to remove
    1 = Contact type deleted
    2 = Contact type in use, suppressed
  */
  IF EXISTS(
    SELECT
      CTT_ID
	FROM
      con_contact_types
	WHERE
      CTT_ID = P_CTT_ID)
  THEN
    IF NOT EXISTS(
      SELECT
        LNK_X_ID
	  FROM
        lnk_links
	  WHERE
        LNK_CHD_LTP_ID IN (2,3)
        AND LNK_X_ID = P_CTT_ID)
	THEN
      DELETE FROM con_contact_types
      WHERE CTT_ID = P_CTT_ID;

      SET P_RETURN_CODE = 1;
      SET P_RETURN_MSG = 'Contact type deleted.';
    ELSE
      UPDATE con_contact_types SET
        CTT_SR = 1,
        CTT_UPDATED = current_timestamp()
	  WHERE CTT_ID = P_CTT_ID;

	  SET P_RETURN_CODE = 2;
      SET P_RETURN_MSG = 'Contact type in use, record suppressed from searches.';
    END IF;
  ELSE
    SET P_RETURN_CODE = -1;
    SET P_RETURN_MSG = 'No record present to remove.';
  END IF;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_con_rem_title` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE PROCEDURE `sp_con_rem_title`(
  IN P_TIT_ID INT UNSIGNED,
  OUT P_RETURN_CODE INT,
  OUT P_RETURN_MSG VARCHAR(100))
BEGIN
  /*XCal - Supported return values
    -1 = Title ID not present to remove
    1 = Title ID deleted
    2 = Title ID in use, suppressed
  */
  IF EXISTS(
    SELECT
      TIT_ID
	FROM
      con_titles
	WHERE
      TIT_ID = P_TIT_ID)
  THEN
    IF NOT EXISTS(
      SELECT
        PER_TIT_ID
	  FROM
        con_people
	  WHERE
        PER_TIT_ID = P_TIT_ID)
	THEN
      DELETE FROM con_titles
      WHERE TIT_ID = P_TIT_ID;

      SET P_RETURN_CODE = 1;
      SET P_RETURN_MSG = 'Title deleted.';
    ELSE
      UPDATE con_titles SET
        TIT_SR = 1,
        TIT_UPDATED = current_timestamp()
	  WHERE TIT_ID = P_TIT_ID;

	  SET P_RETURN_CODE = 2;
      SET P_RETURN_MSG = 'Title in use, record suppressed from searches.';
    END IF;
  ELSE
    SET P_RETURN_CODE = -1;
    SET P_RETURN_MSG = 'No record present to remove.';
  END IF;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_grp_addedit_link_group_nodoc` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE PROCEDURE `sp_grp_addedit_link_group_nodoc`(
  IN P_LINK_TYPE INT UNSIGNED,
  IN P_LINK_ID INT UNSIGNED,
  INOUT P_GRP_ID INT UNSIGNED,
  IN P_GRP_GTP_ID INT UNSIGNED,
  IN P_GRP_NAME VARCHAR(50),
  IN P_GRP_DESCRIPTION VARCHAR(250),
  OUT P_RETURN_CODE INT,
  OUT P_RETURN_MSG VARCHAR(1000))
BEGIN
  /*XCal - Supported Return Codes
    -1 = Link Type Not Supported
    1 = Added Group and Link
    2 = Existing record(s) updated
    3 = Group updated/ignored. Link Added
  */
  DECLARE vGRP_GTP_ID INT UNSIGNED;
  DECLARE vGRP_NAME VARCHAR(50);
  DECLARE vGRP_DESCRIPTION VARCHAR(250);

  /*XCal - First let's validate that the link type is allowed*/
  IF EXISTS(
    SELECT LTA_SR
    FROM lnk_link_type_avail
    WHERE LTA_ONR_LTP_ID = P_LINK_TYPE
    AND LTA_CHD_LTP_ID = 4)
  THEN
    /*XCal - If the group is being created and there are details add and link it*/
    IF (P_GRP_ID = NULL) OR (P_GRP_ID = 0) THEN
      IF (P_GRP_NAME <> '') OR (P_GRP_DESCRIPTION <> '') THEN
        INSERT INTO grp_groups (
          GRP_GTP_ID,GRP_NAME,GRP_DESCRIPTION,GRP_UPDATED)
        VALUES (
          P_GRP_GTP_ID,P_GRP_NAME,P_GRP_DESCRIPTION,current_timestamp());

	    SET P_GRP_ID = LAST_INSERT_ID();
        INSERT INTO lnk_links (
          LNK_ONR_LTP_ID,LNK_ONR_ID,LNK_CHD_LTP_ID,LNK_CHD_ID,LNK_UPDATED)
	    VALUES (
          P_LINK_TYPE,P_LINK_ID,4,P_GRP_ID,current_timestamp());

        SET P_RETURN_CODE = 1;
        SET P_RETURN_MSG = 'Group and link added.';
	  END IF;
    ELSE /*XCal - If we're linking or updating an existing group */
      SELECT
        GRP_GTP_ID,GRP_NAME,GRP_DESCRIPTION
	  FROM
        grp_groups
  	  WHERE
        GRP_ID = P_GRP_ID
	  INTO vGRP_GTP_ID,vGRP_NAME,vGRP_DESCRIPTION;

      /*XCal - Only update the group if it's been changed */
      IF (vGRP_GTP_ID <> P_GRP_GTP_ID) OR
	    (vGRP_NAME <> P_GRP_NAME) OR (vGRP_DESCRIPTION <> P_GRP_DESCRIPTION)
	  THEN
        UPDATE grp_groups SET
          GRP_GTP_ID = P_GRP_GTP_ID,
          GRP_NAME = P_GRP_NAME,
          GRP_DESCRIPTION = P_GRP_DESCRIPTION,
          GRP_UPDATED = current_timestamp()
        WHERE
          GRP_ID = P_GRP_ID;

		SET P_RETURN_CODE = 2;
		SET P_RETURN_MSG = 'Existing group updated.';
	  ELSE
        SET P_RETURN_MSG = 'Group not updated, no changes detected.';
      END IF;

      /*XCal - Update the link record if it exists to show when it was last confirmed,
      add the link record if it doesn't exist */
      IF EXISTS(
        SELECT LNK_UPDATED
        FROM lnk_links
        WHERE LNK_ONR_LTP_ID = P_LINK_TYPE
        AND LNK_ONR_ID = P_LINK_ID
        AND LNK_CHD_LTP_ID = 4
        AND LNK_CHD_ID = P_GRP_ID)
	  THEN
        UPDATE lnk_links SET
          LNK_UPDATED = current_timestamp()
        WHERE LNK_ONR_LTP_ID = P_LINK_TYPE
          AND LNK_ONR_ID = P_LINK_ID
          AND LNK_CHD_LTP_ID = 4
          AND LNK_CHD_ID = P_GRP_ID;

		SET P_RETURN_CODE = 2;
        IF LENGTH(P_RETURN_MSG) > 0 THEN
          SET P_RETURN_MSG = CONCAT(P_RETURN_MSG,' ');
		ELSE
          SET P_RETURN_MSG = '';
		END IF;
		SET P_RETURN_MSG = CONCAT(P_RETURN_MSG,'Existing Link Record Updated.');
	  ELSE
        INSERT INTO lnk_links (
          LNK_ONR_LTP_ID,
          LNK_ONR_ID,
          LNK_CHD_LTP_ID,
          LNK_CHD_ID,
          LNK_UPDATED)
	    VALUES (
          P_LINK_TYPE,
          P_LINK_ID,
          4,
          P_GRP_ID,
          current_timestamp());

		SET P_RETURN_CODE = 3;
        IF LENGTH(P_RETURN_MSG) > 0 THEN
          SET P_RETURN_MSG = CONCAT(P_RETURN_MSG,' ');
		ELSE
          SET P_RETURN_MSG = '';
		END IF;
		SET P_RETURN_MSG = CONCAT(P_RETURN_MSG,' Link record added.');
      END IF;
    END IF;
  ELSE
    SET P_RETURN_CODE = -1;
    SET P_RETURN_MSG = 'The requested link type is not allowed.';
  END IF;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_grp_rem_group` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE PROCEDURE `sp_grp_rem_group`(
  IN P_GRP_ID INT UNSIGNED,
  OUT P_RETURN_CODE INT,
  OUT P_RETURN_MSG VARCHAR(100))
BEGIN
  /*XCal - Supported return values
    -1 = Group ID not present to remove
    1 = Group deleted
    2 = Group in use, suppressed
  */
  DECLARE vUnlinkCode INT;
  DECLARE vUnlinkMsg VARCHAR(100);

  IF EXISTS(
    SELECT
      GRP_ID
	FROM
      grp_groups
	WHERE
      GRP_ID = P_GRP_ID)
  THEN
    IF NOT EXISTS(
      SELECT
        LNK_CHD_ID
	  FROM
        lnk_links
	  WHERE
        LNK_CHD_LTP_ID = 4
        AND LNK_CHD_ID = P_GRP_ID)
	THEN
      DELETE FROM grp_groups
      WHERE GRP_ID = P_GRP_ID;

      CALL sp_lnk_rem_onr_links(4,P_GRP_ID,vUnlinkCode,vUnlinkMsg);

      SET P_RETURN_CODE = 1;
      SET P_RETURN_MSG = CONCAT('Group deleted.',' ',vUnlinkMsg);
    ELSE
      UPDATE grp_groups SET
        GRP_SR = 1,
        GRP_UPDATED = current_timestamp()
	  WHERE GRP_ID = P_GRP_ID;

	  SET P_RETURN_CODE = 2;
      SET P_RETURN_MSG = 'Group in use, record suppressed from searches.';
    END IF;
  ELSE
    SET P_RETURN_CODE = -1;
    SET P_RETURN_MSG = 'No record present to remove.';
  END IF;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_grp_rem_group_type` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE PROCEDURE `sp_grp_rem_group_type`(
  IN P_GTP_ID INT UNSIGNED,
  OUT P_RETURN_CODE INT,
  OUT P_RETURN_MSG VARCHAR(100))
BEGIN
  /*XCal - Supported return values
    -1 = Group Type ID not present to remove
    1 = Group Type deleted
    2 = Group Type in use, suppressed
  */
  IF EXISTS(
    SELECT
      GTP_ID
	FROM
      grp_group_types
	WHERE
      GTP_ID = P_GTP_ID)
  THEN
    IF NOT EXISTS(
      SELECT
        GRP_ID
	  FROM
        grp_groups
	  WHERE
        GRP_GTP_ID = P_GTP_ID)
	THEN
      DELETE FROM grp_group_types
      WHERE GTP_ID = P_GTP_ID;

      SET P_RETURN_CODE = 1;
      SET P_RETURN_MSG = 'Group type deleted.';
    ELSE
      UPDATE grp_group_types SET
        GTP_SR = 1,
        GTP_UPDATED = current_timestamp()
	  WHERE GTP_ID = P_GTP_ID;

	  SET P_RETURN_CODE = 2;
      SET P_RETURN_MSG = 'Group type in use, record suppressed from searches.';
    END IF;
  ELSE
    SET P_RETURN_CODE = -1;
    SET P_RETURN_MSG = 'No record present to remove.';
  END IF;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_link_existing` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE PROCEDURE `sp_link_existing`(
  IN P_ONR_LTP_ID INT UNSIGNED,
  IN P_ONR_ID INT UNSIGNED,
  IN P_CHD_LTP_ID INT UNSIGNED,
  IN P_CHD_ID INT UNSIGNED,
  IN P_X_ID INT UNSIGNED,
  IN P_X_STR VARCHAR(100),
  OUT P_RETURN_CODE INT,
  OUT P_RETURN_MSG VARCHAR(1000))
BEGIN
  /*XCal - Supported result codes
    -1 - Link type not allowed
    1 - Link added
    2 - Link updated
  */
  /*XCal - Validate that the link to link type is available*/
  IF EXISTS(
    SELECT
      LTA_SR
	FROM
      lnk_link_type_avail
	WHERE
      LTA_ONR_LTP_ID = P_ONR_LTP_ID
      AND LTA_CHD_LTP_ID = P_CHD_LTP_ID)
  THEN
    IF EXISTS(
      SELECT LNK_UPDATED
      FROM lnk_links
      WHERE LNK_ONR_LTP_ID = P_ONR_LTP_ID
      AND LNK_ONR_ID = P_ONR_ID
      AND LNK_CHD_LTP_ID = P_CHD_LTP_ID
      AND LNK_CHD_ID = P_CHD_ID)
	THEN
      UPDATE lnk_links SET
        LNK_X_ID = P_X_ID,
        LNK_X_STR = P_X_STR,
        LNK_UPDATED = current_timestamp()
      WHERE LNK_ONR_LTP_ID = P_ONR_LTP_ID
        AND LNK_ONR_ID = P_ONR_ID
        AND LNK_CHD_LTP_ID = P_CHD_LTP_ID
        AND LNK_CHD_ID = LNK_CHD_ID;

	  SET P_RETURN_CODE = 2;
      SET P_RETURN_MSG = 'The existing link record was updated';
	ELSE
      INSERT INTO lnk_links (
        LNK_ONR_LTP_ID,
        LNK_ONR_ID,
        LNK_CHD_LTP_ID,
        LNK_CHD_ID,
        LNK_X_ID,
        LNK_X_STR,
        LNK_UPDATED)
	  VALUES (
        P_ONR_LTP_ID,
        P_ONR_ID,
        P_CHD_LTP_ID,
        P_CHD_ID,
        P_X_ID,
        P_X_STR,
        current_timestamp());

      SET P_RETURN_CODE = 1;
      SET P_RETURN_MSG = 'The link was added';
    END IF;
  ELSE
    SET P_RETURN_CODE = -1;
    SET P_RETURN_MSG = 'The requested link type is not allowed';
  END IF;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_lnk_check_links` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE PROCEDURE `sp_lnk_check_links`(
  IN P_LTP_ID INT UNSIGNED,
  IN P_ID INT UNSIGNED,
  OUT P_LINKS INT UNSIGNED,
  OUT P_LINKED INT UNSIGNED)
BEGIN
  /*XCal - Returns counts of links from the item in P_LINKS and to the item in P_LINKED*/
  SELECT
    COUNT(LNK_ONR_ID)
  FROM
    lnk_links
  WHERE
    LNK_ONR_LTP_ID = P_LTP_ID
    AND LNK_ONR_ID = P_ID
  INTO P_LINKS;

  SELECT
    COUNT(LNK_CHD_ID)
  FROM
    lnk_links
  WHERE
    LNK_CHD_LTP_ID = P_LTP_ID
    AND LNK_CHD_ID = P_ID
  INTO P_LINKED;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_lnk_rem_link_item` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE PROCEDURE `sp_lnk_rem_link_item`(
  IN P_ONR_LTP_ID INT UNSIGNED,
  IN P_ONR_ID INT UNSIGNED,
  IN P_CHD_LTP_ID INT UNSIGNED,
  IN P_CHD_ID INT UNSIGNED,
  OUT P_RETURN_CODE INT,
  OUT P_RETURN_MSG VARCHAR(100))
BEGIN
  /* XCal - Supported return codes
    -1 - Link not present to remove
    1 - Link and item removed
    2 - Link removed, item in use elsewhere
    3 - Link removed, item type not supported for removal
  */
  DECLARE vChildLinkedCount INT;
  DECLARE vChildLinksCount INT;

  IF EXISTS (
    SELECT
      LNK_X_ID
	FROM
      lnk_links
	WHERE
      LNK_ONR_LTP_ID = P_ONR_LTP_ID
      AND LNK_ONR_ID = P_ONR_ID
      AND LNK_CHD_LTP_ID = P_CHD_LTP_ID
      AND LNK_CHD_ID = P_CHD_ID)
  THEN
    /*XCal - We'll delete our link first to keep the linked count simple*/
    DELETE FROM lnk_links
    WHERE
      LNK_ONR_LTP_ID = P_ONR_LTP_ID
      AND LNK_ONR_ID = P_ONR_ID
      AND LNK_CHD_LTP_ID = P_CHD_LTP_ID
      AND LNK_CHD_ID = P_CHD_ID;

    CALL sp_lnk_check_links(P_CHD_LTP_ID,P_CHD_ID,vChildLinksCount,vChildLinkedCount);

    IF vChildLinkedCount + vChildLinksCount > 0 THEN
      SET P_RETURN_CODE = 2;
      SET P_RETURN_MSG = 'Link removed, item in use elsewhere.';
    ELSE
      CASE P_CHD_LTP_ID
        WHEN 1 THEN
          DELETE FROM con_people WHERE PER_ID = P_CHD_ID;
        WHEN 2 THEN
          DELETE FROM con_addresses WHERE ADR_ID = P_CHD_ID;
        WHEN 3 THEN
          DELETE FROM con_contact_points WHERE CNP_ID = P_CHD_ID;
		WHEN 4 THEN
          DELETE FROM grp_groups WHERE GRP_ID = P_CHD_ID;
		WHEN 5 THEN
          DELETE FROM pro_tasks WHERE TSK_ID = P_CHD_ID;
		ELSE SET P_RETURN_CODE = 3;
	  END CASE;
      IF P_RETURN_CODE = 3 THEN
        SET P_RETURN_MSG = 'Link removed, item does not support removal through linking system.';
	  ELSE
        SET P_RETURN_CODE = 1;
        SET P_RETURN_MSG = 'Link and linked item removed.';
      END IF;
    END IF;
  ELSE
    SET P_RETURN_CODE = -1;
    SET P_RETURN_MSG = 'The requested link was not found to remove.';
  END IF;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_lnk_rem_onr_links` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE PROCEDURE `sp_lnk_rem_onr_links`(
  IN P_ONR_LTP_ID INT UNSIGNED,
  IN P_ONR_ID INT UNSIGNED,
  OUT P_RETURN_CODE INT,
  OUT P_RETURN_MSG VARCHAR(100))
BEGIN
  /* XCal - Supported return codes
    0 - No links to remove
    1 - Links removed, all records kept
    FUTURE
    2 - Links removed, in use records kept, out of use records removed
    3 - Links and all records removed
  */
  IF EXISTS(
    SELECT LNK_X_ID
	FROM lnk_links
    WHERE LNK_ONR_LTP_ID = P_ONR_LTP_ID
    AND LNK_ONR_ID = P_ONR_ID)
  THEN
    DELETE FROM
      lnk_links
	WHERE
      LNK_ONR_LTP_ID = P_ONR_LTP_ID
      AND LNK_ONR_ID = P_ONR_ID;

    SET P_RETURN_CODE = 1;
    SET P_RETURN_MSG = 'All links removed, records remain.';
  ELSE
    SET P_RETURN_CODE = 0;
    SET P_RETURN_MSG = 'No links to remove.';
  END IF;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_pro_addedit_link_task_nodoc` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE PROCEDURE `sp_pro_addedit_link_task_nodoc`(
  IN P_LINK_TYPE INT UNSIGNED,
  IN P_LINK_ID INT UNSIGNED,
  INOUT P_TSK_ID INT UNSIGNED,
  IN P_TSK_TST_ID INT UNSIGNED,
  IN P_TSK_TITLE VARCHAR(100),
  IN P_TSK_DESCRIPTION VARCHAR(250),
  OUT P_RETURN_CODE INT,
  OUT P_RETURN_MSG VARCHAR(1000))
BEGIN
  /*XCal - Supported Return Codes
    -1 = Link Type Not Supported
    1 = Added Task and Link
    2 = Existing record(s) updated
    3 = Task updated/ignored. Link Added
  */
  DECLARE vTSK_TST_ID INT UNSIGNED;
  DECLARE vTSK_TITLE VARCHAR(100);
  DECLARE vTSK_DESCRIPTION VARCHAR(250);

  /*XCal - First let's validate that the link type is allowed*/
  IF EXISTS(
    SELECT LTA_SR
    FROM lnk_link_type_avail
    WHERE LTA_ONR_LTP_ID = P_LINK_TYPE
    AND LTA_CHD_LTP_ID = 5)
  THEN
    /*XCal - If the task is being created and there are details add and link it*/
    IF (P_TSK_ID = NULL) OR (P_TSK_ID = 0) THEN
      IF (P_TSK_TITLE <> '') OR (P_TSK_DESCRIPTION <> '') THEN
        INSERT INTO pro_tasks (
          TSK_TST_ID,TSK_TITLE,TSK_DESCRIPTION,TSK_UPDATED)
        VALUES (
          P_TSK_TST_ID,P_TSK_TITLE,P_TSK_DESCRIPTION,current_timestamp());

	    SET P_TSK_ID = LAST_INSERT_ID();
        INSERT INTO lnk_links (
          LNK_ONR_LTP_ID,LNK_ONR_ID,LNK_CHD_LTP_ID,LNK_CHD_ID,LNK_UPDATED)
	    VALUES (
          P_LINK_TYPE,P_LINK_ID,5,P_TSK_ID,current_timestamp());

        SET P_RETURN_CODE = 1;
        SET P_RETURN_MSG = 'Task and link added.';
	  END IF;
    ELSE /*XCal - If we're linking or updating an existing task */
      SELECT
        TSK_TST_ID,TSK_TITLE,TSK_DESCRIPTION
	  FROM
        pro_tasks
  	  WHERE
        TSK_ID = P_TSK_ID
	  INTO vTSK_TST_ID,vTSK_TITLE,vTSK_DESCRIPTION;

      /*XCal - Only update the group if it's been changed */
      IF (vTSK_TST_ID <> P_TSK_TST_ID) OR
	    (vTSK_TITLE <> P_TSK_TITLE) OR (vTSK_DESCRIPTION <> P_TSK_DESCRIPTION)
	  THEN
        UPDATE pro_tasks SET
          TSK_TST_ID = P_TSK_TST_ID,
          TSK_TITLE = P_TSK_TITLE,
          TSK_DESCRIPTION = P_TSK_DESCRIPTION,
          TSK_UPDATED = current_timestamp()
        WHERE
          TSK_ID = P_TSK_ID;

		SET P_RETURN_CODE = 2;
		SET P_RETURN_MSG = 'Existing task updated.';
	  ELSE
        SET P_RETURN_MSG = 'Task not updated, no changes detected.';
      END IF;

      /*XCal - Update the link record if it exists to show when it was last confirmed,
      add the link record if it doesn't exist */
      IF EXISTS(
        SELECT LNK_UPDATED
        FROM lnk_links
        WHERE LNK_ONR_LTP_ID = P_LINK_TYPE
        AND LNK_ONR_ID = P_LINK_ID
        AND LNK_CHD_LTP_ID = 5
        AND LNK_CHD_ID = P_TSK_ID)
	  THEN
        UPDATE lnk_links SET
          LNK_UPDATED = current_timestamp()
        WHERE LNK_ONR_LTP_ID = P_LINK_TYPE
          AND LNK_ONR_ID = P_LINK_ID
          AND LNK_CHD_LTP_ID = 5
          AND LNK_CHD_ID = P_TSK_ID;

		SET P_RETURN_CODE = 2;
        IF LENGTH(P_RETURN_MSG) > 0 THEN
          SET P_RETURN_MSG = CONCAT(P_RETURN_MSG,' ');
		ELSE
          SET P_RETURN_MSG = '';
		END IF;
		SET P_RETURN_MSG = CONCAT(P_RETURN_MSG,'Existing Link Record Updated.');
	  ELSE
        INSERT INTO lnk_links (
          LNK_ONR_LTP_ID,
          LNK_ONR_ID,
          LNK_CHD_LTP_ID,
          LNK_CHD_ID,
          LNK_UPDATED)
	    VALUES (
          P_LINK_TYPE,
          P_LINK_ID,
          5,
          P_TSK_ID,
          current_timestamp());

		SET P_RETURN_CODE = 3;
        IF LENGTH(P_RETURN_MSG) > 0 THEN
          SET P_RETURN_MSG = CONCAT(P_RETURN_MSG,' ');
		ELSE
          SET P_RETURN_MSG = '';
		END IF;
		SET P_RETURN_MSG = CONCAT(P_RETURN_MSG,' Link record added.');
      END IF;
    END IF;
  ELSE
    SET P_RETURN_CODE = -1;
    SET P_RETURN_MSG = 'The requested link type is not allowed.';
  END IF;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_pro_mod_linked` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE PROCEDURE `sp_pro_mod_linked`(
  IN P_TSK_ID INT UNSIGNED,
  IN P_LINK_TYPE INT UNSIGNED,
  IN P_LINK_ID INT UNSIGNED,
  IN P_TSS_ID INT UNSIGNED,
  IN P_NOTE VARCHAR(100),
  OUT P_RETURN_CODE INT,
  OUT P_RETURN_MSG VARCHAR(200))
BEGIN
  /*XCal - Supported Return Codes
    -1 = Link Type Not Supported
    -2 = Link doesn't exist
    0 = No changes to update
    1 = Link Updated
  */
  DECLARE vState INT UNSIGNED;
  DECLARE vNote VARCHAR(100);

  /*XCal - First let's validate that the link type is allowed*/
  IF EXISTS(
    SELECT LTA_SR
    FROM lnk_link_type_avail
    WHERE LTA_ONR_LTP_ID = 5
    AND LTA_CHD_LTP_ID = P_LINK_TYPE)
  THEN
    IF EXISTS(
      SELECT LNK_ONR_ID
      FROM lnk_links
      WHERE LNK_ONR_LTP_ID = 5
      AND LNK_ONR_ID = P_TSK_ID
      AND LNK_CHD_LTP_ID = P_LINK_TYPE
      AND LNK_CHD_ID = P_LINK_ID)
	THEN
      SELECT
        IFNULL(LNK_X_ID,0),IFNULL(LNK_X_STR,'')
	  FROM
        lnk_links
      WHERE LNK_ONR_LTP_ID = 5
        AND LNK_ONR_ID = P_TSK_ID
        AND LNK_CHD_LTP_ID = P_LINK_TYPE
        AND LNK_CHD_ID = P_LINK_ID
	  INTO vState,vNote;

      /*XCal - Only update the link if it's been changed */
      IF (vState <> P_TSS_ID) OR
	    (vNote <> P_NOTE)
	  THEN
        UPDATE lnk_links SET
          LNK_X_ID = P_TSS_ID,
          LNK_X_STR = P_NOTE,
          LNK_UPDATED = current_timestamp()
        WHERE LNK_ONR_LTP_ID = 5
          AND LNK_ONR_ID = P_TSK_ID
          AND LNK_CHD_LTP_ID = P_LINK_TYPE
          AND LNK_CHD_ID = P_LINK_ID;

		SET P_RETURN_CODE = 1;
		SET P_RETURN_MSG = 'Task link updated.';
	  ELSE
        SET P_RETURN_CODE = 0;
        SET P_RETURN_MSG = 'Task link not updated, no changes detected.';
      END IF;
    ELSE
      SET P_RETURN_CODE = -2;
      SET P_RETURN_MSG = 'The requested link does not exist.';
    END IF;
  ELSE
    SET P_RETURN_CODE = -1;
    SET P_RETURN_MSG = 'The requested link type is not allowed.';
  END IF;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_pro_remcheck_subtasks` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE PROCEDURE `sp_pro_remcheck_subtasks`(
  IN P_TSK_ID INT UNSIGNED,
  OUT P_RETURN_CODE INT)
BEGIN
  /*XCal - Supported return values
    -1 = Task ID not present to check
    0 = Task has no subtasks
    1 = Task has subtasks but none are linked to
    2 = Task has subtasks which are linked to (in use)
  */
  DECLARE vChildTask INT UNSIGNED;
  DECLARE vCheckCode INT;
  DECLARE vLinks INT;
  DECLARE vLinked INT;

  IF EXISTS(
    SELECT
      TSK_ID
	  FROM
      pro_tasks
	  WHERE
      TSK_ID = P_TSK_ID)
  THEN
    IF EXISTS(
      SELECT
        TSK_ID
	    FROM
        pro_tasks
	    WHERE
        TSK_TSK_ID = P_TSK_ID)
	  THEN
      SELECT 0,1
      INTO vChildTask,P_RETURN_CODE;
      /*XCal - We know it has subtasks but what matters is whether they're in use*/
      GetChildTasks: LOOP
        SELECT
          MIN(TSK_ID)
        FROM
          pro_tasks
        WHERE
          TSK_TSK_ID = P_TSK_ID
          AND TSK_ID > vChildTask
        INTO vChildTask;

        IF vChildTask IS NULL THEN
          LEAVE GetChildTasks;
        ELSE
          CALL sp_lnk_check_links(5,vChildTask,vLinks,vLinked);

          IF vLinked > 0 THEN
            SET P_RETURN_CODE = 2;
          ELSE
            CALL sp_pro_remcheck_subtasks(vChildTask,vCheckCode);
            IF vCheckCode > P_RETURN_CODE THEN
              SET P_RETURN_CODE = vCheckCode;
            END IF;
          END IF;
        END IF;
        IF P_RETURN_CODE > 1 THEN
          LEAVE GetChildTasks;
        END IF;
      END LOOP GetChildTasks;
    ELSE
      /*XCal - No child tasks*/
      SET P_RETURN_CODE = 0;
    END IF;
  ELSE
    /*XCal - Requested task not present*/
    SET P_RETURN_CODE = -1;
  END IF;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_pro_rem_state` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE PROCEDURE `sp_pro_rem_state`(
  IN P_TSS_ID INT UNSIGNED,
  OUT P_RETURN_CODE INT,
  OUT P_RETURN_MSG VARCHAR(100))
BEGIN
  /*XCal - Supported return values
    -1 = State ID not present to remove
    1 = State deleted
    2 = State in use, suppressed
  */
  IF EXISTS(
    SELECT
      TSS_ID
	FROM
      pro_task_states
	WHERE
      TSS_ID = P_TSS_ID)
  THEN
    IF NOT EXISTS(
      SELECT
        LNK_X_ID
	  FROM
        lnk_links
	  WHERE
        LNK_CHD_LTP_ID = 5
        AND LNK_X_ID = P_TSS_ID)
	THEN
      DELETE FROM pro_task_states
      WHERE TSS_ID = P_TSS_ID;

      SET P_RETURN_CODE = 1;
      SET P_RETURN_MSG = 'Project/task state deleted.';
    ELSE
      UPDATE pro_task_states SET
        TSS_SR = 1,
        TSS_UPDATED = current_timestamp()
	  WHERE TSS_ID = P_TSS_ID;

	  SET P_RETURN_CODE = 2;
      SET P_RETURN_MSG = 'Project/task state in use, record suppressed from searches.';
    END IF;
  ELSE
    SET P_RETURN_CODE = -1;
    SET P_RETURN_MSG = 'No record present to remove.';
  END IF;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_pro_rem_task` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE PROCEDURE `sp_pro_rem_task`(
  IN P_TSK_ID INT UNSIGNED,
  OUT P_RETURN_CODE INT,
  OUT P_RETURN_MSG VARCHAR(100))
BEGIN
  /*XCal - Supported return values
    -1 = Task ID not present to remove
    1 = Task deleted
    2 = Task suppressed
    3 = Task and subtasks deleted
    4 = Task and subtasks suppressed
  */
  DECLARE vChildCheck INT;
  DECLARE vUnlinkCode INT;
  DECLARE vUnlinkMsg VARCHAR(100);
  DECLARE vChildTask INT UNSIGNED;
  DECLARE vLinks INT UNSIGNED;
  DECLARE vLinked INT UNSIGNED;
  DECLARE vRemCode INT;
  DECLARE vRemMsg VARCHAR(100);
  DECLARE vSuppressCount INT UNSIGNED;

  IF EXISTS(
    SELECT
      TSK_ID
	  FROM
      pro_tasks
	  WHERE
      TSK_ID = P_TSK_ID)
  THEN
    CALL sp_lnk_check_links(5,P_TSK_ID,vLinks,vLinked);
    CALL sp_pro_remcheck_subtasks(P_TSK_ID,vChildCheck);
    IF (vLinked = 0) AND (vChildCheck < 2) THEN
      CASE vChildCheck
        WHEN 0 THEN
          DELETE FROM pro_tasks
          WHERE TSK_ID = P_TSK_ID;

          CALL sp_lnk_rem_onr_links(5,P_TSK_ID,vUnlinkCode,vUnlinkMsg);

          SET P_RETURN_CODE = 1;
          SET P_RETURN_MSG = CONCAT('ProTask deleted.',' ',vUnlinkMsg);
        WHEN 1 THEN
          SELECT 0 INTO vChildTask;
          GetChildTasks: LOOP
            SELECT
              MIN(TSK_ID)
            FROM
              pro_tasks
            WHERE
              TSK_ID > vChildTask
            INTO vChildTask;

            IF vChildTask IS NULL THEN
              LEAVE GetChildTasks;
            ELSE
              CALL sp_lnk_rem_onr_links(5,vChildTask,vUnlinkCode,vUnlinkMsg);
              CALL sp_pro_rem_task(vChildTask,vRemCode,vRemMsg);
            END IF;
          END LOOP GetChildTasks;
          CALL sp_lnk_rem_onr_links(5,P_TSK_ID,vUnlinkCode,vUnlinkMsg);
          SET P_RETURN_CODE = 3;
          SET P_RETURN_MSG = CONCAT('ProTask and sub-tasks deleted.',' ',vUnlinkMsg);
      END CASE;
    ELSE
      UPDATE pro_tasks SET
        TSK_SR = 1,
        TSK_UPDATED = current_timestamp()
      WHERE TSK_ID = P_TSK_ID;

      IF vChildCheck > 0 THEN
        CALL sp_pro_suppress_subtasks(P_TSK_ID,vSuppressCount);
        SET P_RETURN_CODE = 4;
        SET P_RETURN_MSG = CONCAT('ProTask in use, record and ',CAST(vSuppressCount as CHAR),' sub-tasks suppressed.');
      ELSE
        SET P_RETURN_CODE = 2;
        SET P_RETURN_MSG = 'ProTask in use, record suppressed.';
      END IF;
    END IF;
  ELSE
    SET P_RETURN_CODE = -1;
    SET P_RETURN_MSG = 'No record present to remove.';
  END IF;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_pro_rem_task_type` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE PROCEDURE `sp_pro_rem_task_type`(
  IN P_TST_ID INT UNSIGNED,
  OUT P_RETURN_CODE INT,
  OUT P_RETURN_MSG VARCHAR(100))
BEGIN
  /*XCal - Supported return values
    -1 = Task Type ID not present to remove
    1 = Task Type deleted
    2 = Task Type in use, suppressed
  */
  IF EXISTS(
    SELECT
      TST_ID
	FROM
      pro_task_types
	WHERE
      TST_ID = P_TST_ID)
  THEN
    IF NOT EXISTS(
      SELECT
        TSK_ID
	  FROM
        pro_tasks
	  WHERE
        TSK_TST_ID = P_TST_ID)
	THEN
      DELETE FROM pro_task_types
      WHERE TST_ID = P_TST_ID;

      SET P_RETURN_CODE = 1;
      SET P_RETURN_MSG = 'Project/task type deleted.';
    ELSE
      UPDATE pro_task_types SET
        TST_SR = 1,
        TST_UPDATED = current_timestamp()
	  WHERE TST_ID = P_TST_ID;

	  SET P_RETURN_CODE = 2;
      SET P_RETURN_MSG = 'Project/task type in use, record suppressed from searches.';
    END IF;
  ELSE
    SET P_RETURN_CODE = -1;
    SET P_RETURN_MSG = 'No record present to remove.';
  END IF;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_pro_suppress_subtasks` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE PROCEDURE `sp_pro_suppress_subtasks`(
  IN P_TSK_ID INT UNSIGNED,
  OUT P_SUPPRESSED INT UNSIGNED)
BEGIN
  /*XCal - Supported return values
    -1 = Parent Task ID not present
    >= 0 = The total count of subtasks which were suppressed
  */
  DECLARE vChildTask INT UNSIGNED;
  DECLARE vChildSuppressed INT UNSIGNED;
  DECLARE vCheckCode INT;
  DECLARE vLinks INT;
  DECLARE vLinked INT;

  IF EXISTS(
    SELECT
      TSK_ID
	  FROM
      pro_tasks
	  WHERE
      TSK_ID = P_TSK_ID)
  THEN
    SELECT 0,0
    INTO vChildTask,P_SUPPRESSED;

    GetChildTasks: LOOP
      SELECT
        MIN(TSK_ID)
      FROM
        pro_tasks
      WHERE
        TSK_TSK_ID = P_TSK_ID
        AND TSK_ID > vChildTask
      INTO vChildTask;

      IF vChildTask IS NULL THEN
        LEAVE GetChildTasks;
      ELSE
        UPDATE
          pro_tasks
        SET
          TSK_SR = 1,
          TSK_UPDATED = current_timestamp()
        WHERE
          TSK_ID = vChildTask;

        CALL sp_pro_suppress_subtasks(vChildTask,vChildSuppressed);
        SET P_SUPPRESSED = P_SUPPRESSED + 1 + vChildSuppressed;
      END IF;
    END LOOP GetChildTasks;
  ELSE
    /*XCal - Requested task not present*/
    SET P_SUPPRESSED = -1;
  END IF;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_rqf_addedit_fulfill_level` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE PROCEDURE `sp_rqf_addedit_fulfill_level`(
  INOUT P_FLL_ID INT UNSIGNED,
  IN P_FLL_FLM_ID INT UNSIGNED,
  INOUT P_FLL_LEVEL SMALLINT UNSIGNED,
  IN P_FLL_NAME VARCHAR(50),
  IN P_FLL_DESCRIPTION VARCHAR(250),
  OUT P_RETURN_CODE INT,
  OUT P_RETURN_MSG VARCHAR(200))
BEGIN
  /*XCal - Supported return codes
  -1 = The fulfillment the level is for does not exist or is suppressed
  1 = Fulfillment level added as requested
  2 = Fulfillment level added, requested level adjusted
  3 = Fulfillment level modified as requested
  4 = Fulfillment level modified, requested level adjusted
  */
  DECLARE vMaxLvl SMALLINT UNSIGNED;
  DECLARE vOldLvl SMALLINT UNSIGNED;
  DECLARE vLvlChanged BIT;
  SET vLvlChanged = 0;

  IF EXISTS(
    SELECT
      FLM_ID
	FROM
      rqf_fulfillments
	WHERE
      FLM_ID = P_FLL_FLM_ID
      AND FLM_SR = 0)
  THEN
    SELECT
      COUNT(FLL_ID)+1
    FROM
      rqf_fulfillment_levels
	WHERE
      FLL_FLM_ID = P_FLL_FLM_ID
	INTO vMaxLvl;

    IF vMaxLvl < P_FLL_LEVEL THEN
      SET P_FLL_LEVEL = vMaxLvl,
        vLvlChanged = 1;
	END IF;

    IF P_FLL_LEVEL = 0 THEN
      SET P_FLL_LEVEL = 1,
        vLvlChanged = 1;
	END IF;

    IF (P_FLL_ID IS NULL) OR (P_FLL_ID = 0) THEN
      UPDATE rqf_fulfillment_levels SET
        FLL_LEVEL = FLL_LEVEL+1
	  WHERE
        FLL_FLM_ID = P_FLL_FLM_ID
        AND FLL_LEVEL >= P_FLL_LEVEL;

      INSERT INTO rqf_fulfillment_levels (
        FLL_FLM_ID,
        FLL_LEVEL,
        FLL_NAME,
        FLL_DESCRIPTION,
        FLL_UPDATED)
	  VALUES (
        P_FLL_FLM_ID,
        P_FLL_LEVEL,
        P_FLL_NAME,
        P_FLL_DESCRIPTION,
        current_timestamp());

      SET P_FLL_ID = LAST_INSERT_ID();

      SET P_RETURN_MSG = 'Fulfillment level added';
      IF vLvlChanged = 0 THEN
        SET P_RETURN_CODE = 1;
	  ELSE
        SET P_RETURN_CODE = 2,
          P_RETURN_MSG = CONCAT(P_RETURN_MSG,', requested level adjusted');
	  END IF;
    ELSE
      SELECT
        FLL_LEVEL
      FROM
        rqf_fulfillment_levels
	  WHERE
        FLL_ID = P_FLL_ID
	  INTO vOldLvl;

      IF vOldLvl <> P_FLL_LEVEL THEN
        IF vOldLvl > P_FLL_LEVEL THEN
          /*XCal - Move Up, including target */
          UPDATE rqf_fulfillment_levels SET
            FLL_LEVEL = FLL_LEVEL+1
		  WHERE
            FLL_FLM_ID = P_FLL_FLM_ID
            AND FLL_LEVEL >= P_FLL_LEVEL
            AND FLL_LEVEL < vOldLvl;
        ELSE
          /*XCal - Move Down, including target*/
          UPDATE rqf_fulfillment_levels SET
            FLL_LEVEL = FLL_LEVEL-1
		  WHERE
            FLL_FLM_ID = P_FLL_FLM_ID
            AND FLL_LEVEL <= P_FLL_LEVEL
            AND FLL_LEVEL > vOldLvl;
        END IF;
      END IF;

      UPDATE rqf_fulfillment_levels SET
        FLL_LEVEL = P_FLL_LEVEL,
        FLL_NAME = P_FLL_NAME,
        FLL_DESCRIPTION = P_FLL_DESCRIPTION,
        FLL_UPDATED = current_timestamp()
	  WHERE
        FLL_ID = P_FLL_ID;

      SET P_RETURN_MSG = 'Fulfillment level updated';
	  IF vLvlChanged = 0 THEN
        SET P_RETURN_CODE = 3;
	  ELSE
        SET P_RETURN_CODE = 4,
          P_RETURN_MSG = CONCAT(P_RETURN_MSG,', requested level adjusted');
	  END IF;
    END IF;
  ELSE
    SET P_RETURN_CODE = -1,
      P_RETURN_MSG = 'The fulfillment requested does not exist or is suppressed pending removal';
  END IF;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_rqf_addedit_provider` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE PROCEDURE `sp_rqf_addedit_provider`(
  INOUT P_FLP_ID INT UNSIGNED,
  IN P_FLT_ID INT UNSIGNED,
  IN P_LINK_ID INT UNSIGNED,
  IN P_FLM_ID INT UNSIGNED,
  IN P_FLL_ID INT UNSIGNED,
  IN P_REFERENCE VARCHAR(250),
  IN P_ACQUIRED DATETIME,
  IN P_EXPIRES DATETIME,
  OUT P_RETURN_CODE INT,
  OUT P_RETURN_MSG VARCHAR(200))
BEGIN
  /*XCal - Supported return codes
  -1 = Provider not added/modified, supporting type not present (FLT,FLM,FLL)
  0 = Provider checked, no changes to update
  1 = Provider added
  2 = Provider updated
  */
  DECLARE vFLT INT UNSIGNED;
  DECLARE vLink INT UNSIGNED;
  DECLARE vFLM INT UNSIGNED;
  DECLARE vFLL INT UNSIGNED;
  DECLARE vRef VARCHAR(250);
  DECLARE vAcq DATETIME;
  DECLARE vExp DATETIME;

  IF EXISTS(SELECT FLT_ID FROM rqf_fulfillment_types WHERE FLT_ID = P_FLT_ID)
    AND EXISTS(SELECT FLM_ID FROM rqf_fulfillments WHERE FLM_ID = P_FLM_ID)
    AND EXISTS(SELECT FLL_ID FROM rqf_fulfillment_levels WHERE FLL_ID = P_FLL_ID)
  THEN
    IF (P_FLP_ID IS NULL) OR (P_FLP_ID = 0) THEN
      INSERT INTO rqf_fulfillment_providers (
        FLP_FLT_ID,
        FLP_LINK_ID,
        FLP_FLM_ID,
        FLP_FLL_ID,
        FLP_REFERENCE,
        FLP_ACQUIRED,
        FLP_EXPIRES,
        FLP_UPDATED)
	  VALUES (
        P_FLT_ID,
        P_LINK_ID,
        P_FLM_ID,
        P_FLL_ID,
        P_REFERENCE,
        P_ACQUIRED,
        P_EXPIRES,
        current_timestamp());

      SET P_FLP_ID = LAST_INSERT_ID(),
        P_RETURN_CODE = 1,
        P_RETURN_MSG = 'Provider added';
    ELSE
      SELECT
        FLP_FLT_ID,
        FLP_LINK_ID,
        FLP_FLM_ID,
        FLP_FLL_ID,
        FLP_REFERENCE,
        IFNULL(FLP_ACQUIRED,0),
        IFNULL(FLP_EXPIRES,0)
	  FROM
        rqf_fulfillment_providers
	  WHERE
        FLP_ID = P_FLP_ID
	  INTO
        vFLT,vLink,vFLM,vFLL,vRef,vAcq,vExp;

      IF (vFLT <> P_FLT_ID) OR (vLink <> P_LINK_ID) OR (vFLM <> P_FLM_ID)
        OR (vRef <> P_REFERENCE) OR (vAcq <> IFNULL(P_ACQUIRED,0))
        OR (vExp <> IFNULL(P_EXPIRES,0))
	  THEN
        UPDATE rqf_fulfillment_providers SET
          FLP_FLT_ID = P_FLT_ID,
          FLP_LINK_ID = P_LINK_ID,
          FLP_FLM_ID = P_FLM_ID,
          FLP_FLL_ID = P_FLL_ID,
          FLP_REFERENCE = P_REFERENCE,
          FLP_ACQUIRED = P_ACQUIRED,
          FLP_EXPIRES = P_EXPIRES,
          FLP_UPDATED = current_timestamp()
		WHERE
          FLP_ID = P_FLP_ID;

		SET P_RETURN_CODE = 2,
          P_RETURN_MSG = 'Provider record updated';
      ELSE
        SET P_RETURN_CODE = 0,
          P_RETURN_MSG = 'No changes detected to update';
      END IF;
    END IF;
  ELSE
    SET P_RETURN_CODE = -1,
      P_RETURN_MSG = 'No action taken, requested type, item or level does not exist';
  END IF;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_rqf_addedit_requirement` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE PROCEDURE `sp_rqf_addedit_requirement`(
  INOUT P_REQ_ID INT UNSIGNED,
  IN P_ONR_LTP_ID INT UNSIGNED,
  IN P_ONR_ID INT UNSIGNED,
  IN P_CHD_LTP_ID INT UNSIGNED,
  IN P_FLT_ID INT UNSIGNED,
  IN P_FLM_ID INT UNSIGNED,
  IN P_MIN_FLL_ID INT UNSIGNED,
  IN P_COUNT INT UNSIGNED,
  OUT P_RETURN_CODE INT,
  OUT P_RETURN_MSG VARCHAR(200))
BEGIN
  /*XCal - Supported return codes
  -1 = Requirement not added/modified, supporting type not present (ONR/CHD_LTP,FLT,FLM,FLL)
  0 = Requirement checked, no changes to update
  1 = Requirement added
  2 = Requirement updated
  */
  DECLARE vONRLTP INT UNSIGNED;
  DECLARE vONR INT UNSIGNED;
  DECLARE vCHDLTP INT UNSIGNED;
  DECLARE vFLT INT UNSIGNED;
  DECLARE vFLM INT UNSIGNED;
  DECLARE vFLL INT UNSIGNED;
  DECLARE vCount INT UNSIGNED;

  IF EXISTS(SELECT LTP_ID FROM lnk_link_types WHERE LTP_ID = P_ONR_LTP_ID)
    AND EXISTS(SELECT LTP_ID FROM lnk_link_types WHERE LTP_ID = P_CHD_LTP_ID)
  THEN
    IF ((P_FLT_ID IS NULL) OR (P_FLT_ID = 0) OR EXISTS(SELECT FLT_ID FROM rqf_fulfillment_types WHERE FLT_ID = P_FLT_ID))
    THEN
      IF ((P_FLM_ID IS NULL) OR (P_FLM_ID = 0) OR EXISTS(SELECT FLM_ID FROM rqf_fulfillments WHERE FLM_ID = P_FLM_ID))
        AND ((P_MIN_FLL_ID IS NULL) OR (P_MIN_FLL_ID = 0) OR EXISTS(SELECT FLL_ID FROM rqf_fulfillment_levels WHERE FLL_ID = P_MIN_FLL_ID))
      THEN
        IF (P_REQ_ID IS NULL) OR (P_REQ_ID = 0) THEN
          INSERT INTO rqf_requirements (
            REQ_ONR_LTP_ID,
            REQ_ONR_ID,
            REQ_CHD_LTP_ID,
            REQ_FLT_ID,
            REQ_FLM_ID,
            REQ_MIN_FLL_ID,
            REQ_COUNT,
            REQ_UPDATED)
	      VALUES (
            P_ONR_LTP_ID,
            P_ONR_ID,
            P_CHD_LTP_ID,
            P_FLT_ID,
            P_FLM_ID,
            P_MIN_FLL_ID,
            P_COUNT,
            current_timestamp());

          SET P_REQ_ID = LAST_INSERT_ID(),
            P_RETURN_CODE = 1,
            P_RETURN_MSG = 'Requirement added';
        ELSE
          SELECT
            REQ_ONR_LTP_ID,
            REQ_ONR_ID,
            REQ_CHD_LTP_ID,
            REQ_FLT_ID,
            REQ_FLM_ID,
            REQ_MIN_FLL_ID,
            REQ_COUNT
	      FROM
            rqf_requirements
          WHERE
            REQ_ID = P_REQ_ID
	      INTO
            vONRLTP,vONR,vCHDLTP,vFLT,vFLM,vFLL,vCount;

          IF (vONRLTP <> P_ONR_LTP_ID) OR (vONR <> P_ONR_ID) OR (vCHDLTP <> P_CHD_LTP_ID)
            OR ((vFLT <> P_FLT_ID) OR (vFLT IS NULL AND P_FLT_ID IS NOT NULL) OR (vFLT IS NOT NULL AND P_FLT_ID IS NULL))
            OR ((vFLM <> P_FLM_ID) OR (vFLM IS NULL AND P_FLM_ID IS NOT NULL) OR (vFLM IS NOT NULL AND P_FLM_ID IS NULL))
            OR ((vFLL <> P_MIN_FLL_ID) OR (vFLL IS NULL AND P_MIN_FLL_ID IS NOT NULL) OR (vFLL IS NOT NULL AND P_MIN_FLL_ID IS NULL))
            OR (vCount <> P_COUNT)
	      THEN
            UPDATE rqf_requirements SET
              REQ_ONR_LTP_ID = P_ONR_LTP_ID,
              REQ_ONR_ID = P_ONR_ID,
              REQ_CHD_LTP_ID = P_CHD_LTP_ID,
              REQ_FLT_ID = P_FLT_ID,
              REQ_FLM_ID = P_FLM_ID,
              REQ_MIN_FLL_ID = P_MIN_FLL_ID,
              REQ_COUNT = P_COUNT,
              REQ_UPDATED = current_timestamp()
            WHERE
              REQ_ID = P_REQ_ID;

	        SET P_RETURN_CODE = 2,
              P_RETURN_MSG = 'Requirement record updated';
          ELSE
            SET P_RETURN_CODE = 0,
              P_RETURN_MSG = 'No changes detected to update';
          END IF;
        END IF;
      ELSE
        SET P_RETURN_CODE = -1,
          P_RETURN_MSG = 'No action taken, a fulfillment or level does not exist';
      END IF;
	ELSE
        SET P_RETURN_CODE = -1,
          P_RETURN_MSG = 'No action taken, the requested fulfillment type does not exist';
    END IF;
  ELSE
    SET P_RETURN_CODE = -1,
      P_RETURN_MSG = 'No action taken, a requested link type does not exist';
  END IF;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_rqf_rem_fulfillment` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE PROCEDURE `sp_rqf_rem_fulfillment`(
  IN P_FLM_ID INT UNSIGNED,
  OUT P_RETURN_CODE INT,
  OUT P_RETURN_MSG VARCHAR(100))
BEGIN
  /*XCal - Supported return values
    -1 = Fulfillment ID not present to remove
    1 = Fulfillment deleted
    2 = Fulfillment in use, suppressed
  */
  DECLARE vUnlinkCode INT;
  DECLARE vUnlinkMsg VARCHAR(100);

  IF EXISTS(
    SELECT
      FLM_ID
	FROM
      rqf_fulfillments
	WHERE
      FLM_ID = P_FLM_ID)
  THEN
    /*XCal - If we add requirements to the linking system we'll need to add ANOTHER exists*/
    IF NOT (
	  EXISTS(
        SELECT
          FLL_FLM_ID
	    FROM
          rqf_fulfillment_levels
	    WHERE
          FLL_FLM_ID = P_FLM_ID) OR
	  EXISTS(
        SELECT
          FLP_FLM_ID
		FROM
          rqf_fulfillment_providers
		WHERE
          FLP_FLM_ID = P_FLM_ID) OR
      EXISTS(
        SELECT
          REQ_FLM_ID
		FROM
          rqf_requirements
		WHERE
          REQ_FLM_ID = P_FLM_ID))
	THEN
      DELETE FROM rqf_fulfillments
      WHERE FLM_ID = P_FLM_ID;

      /* XCal - If we introduce link requirements we'll need to call the below with the right LTP_ID
      CALL sp_lnk_rem_onr_links(4,P_FLM_ID,vUnlinkCode,vUnlinkMsg);*/

      SET P_RETURN_CODE = 1;
      SET P_RETURN_MSG = CONCAT('Fulfillment deleted.',' ',vUnlinkMsg);
    ELSE
      UPDATE rqf_fulfillments SET
        FLM_SR = 1,
        FLM_UPDATED = current_timestamp()
	  WHERE FLM_ID = P_FLM_ID;

	  SET P_RETURN_CODE = 2;
      SET P_RETURN_MSG = 'Fulfillment in use, record suppressed from searches.';
    END IF;
  ELSE
    SET P_RETURN_CODE = -1;
    SET P_RETURN_MSG = 'No record present to remove.';
  END IF;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_rqf_rem_fulfill_level` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE PROCEDURE `sp_rqf_rem_fulfill_level`(
  P_FLL_ID INT UNSIGNED,
  P_RETURN_CODE INT,
  P_RETURN_MSG VARCHAR(200))
BEGIN
  /*XCal - Supported return codes
  -1 = Fulfillment level not present
  1 = Fulfillment level removed
  2 = Fulfillment level in use, suppressed
  */
  IF EXISTS(
    SELECT
      FLL_ID
	FROM
      rqf_fulfillment_levels
	WHERE
      FLL_ID = P_FLL_ID)
  THEN
    IF EXISTS(
      SELECT
        FLP_FLL_ID
	  FROM
        rqf_fulfillment_providers
	  WHERE
        FLP_FLL_ID = P_FLL_ID)
	THEN
      UPDATE rqf_fulfillment_levels SET
        FLL_SR = 1,
        FLL_UPDATED = current_timestamp()
	  WHERE
        FLL_ID = P_FLL_ID;

      SET P_RETURN_CODE = 2,
        P_RETURN_MSG = 'Fulfillment level in use, suppressed';
    ELSE
      DELETE FROM rqf_fulfillment_levels
      WHERE FLL_ID = P_FLL_ID;

      SET P_RETURN_CODE = 1,
        P_RETURN_MSG = 'Fulfillment level removed';
    END IF;
  ELSE
    SET P_RETURN_CODE = -1,
      P_RETURN_MSG = 'No record present to remove';
  END IF;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_rqf_rem_fulfill_type` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE PROCEDURE `sp_rqf_rem_fulfill_type`(
  IN P_FLT_ID INT UNSIGNED,
  OUT P_RETURN_CODE INT,
  OUT P_RETURN_MSG VARCHAR(100))
BEGIN
  /*XCal - Supported return values
    -1 = Fulfillment Type ID not present to remove
    1 = Fulfillment Type deleted
    2 = Fulfillment Type in use, suppressed
  */
  IF EXISTS(
    SELECT
      FLT_ID
	FROM
      rqf_fulfillment_types
	WHERE
      FLT_ID = P_FLT_ID)
  THEN
    IF NOT EXISTS(
      SELECT
        FLM_ID
	  FROM
        rqf_fulfillments
	  WHERE
        FLM_FLT_ID = P_FLT_ID)
	THEN
      DELETE FROM rqf_fulfillment_types
      WHERE FLT_ID = P_FLT_ID;

      SET P_RETURN_CODE = 1;
      SET P_RETURN_MSG = 'Fulfillment type deleted.';
    ELSE
      UPDATE rqf_fulfillment_types SET
        FLT_SR = 1,
        FLT_UPDATED = current_timestamp()
	  WHERE FLT_ID = P_FLT_ID;

	  SET P_RETURN_CODE = 2;
      SET P_RETURN_MSG = 'Fulfillment type in use, record suppressed from searches.';
    END IF;
  ELSE
    SET P_RETURN_CODE = -1;
    SET P_RETURN_MSG = 'No record present to remove.';
  END IF;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_sch_addedit_link_sched` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE PROCEDURE `sp_sch_addedit_link_sched`(
  IN P_LINK_TYPE INT UNSIGNED,
  IN P_LINK_ID INT UNSIGNED,
  INOUT P_SCI_ID INT UNSIGNED,
  IN P_SCT_ID INT UNSIGNED,
  IN P_START DATETIME,
  IN P_END DATETIME,
  IN P_BREAKS DATETIME,
  OUT P_RETURN_CODE INT,
  OUT P_RETURN_MSG VARCHAR(1000))
BEGIN
  /*XCal - Supported Return Codes
    -1 = Link Type Not Supported
    1 = Added Schedule Item and Link
    2 = Existing record(s) updated
    3 = Schedule item updated/ignored. Link Added
  */
  DECLARE vSCT_ID INT UNSIGNED;
  DECLARE vStart DATETIME;
  DECLARE vEnd DATETIME;
  DECLARE vBreaks DATETIME;

  /*XCal - First let's validate that the link type is allowed*/
  IF EXISTS(
    SELECT LTA_SR
    FROM lnk_link_type_avail
    WHERE LTA_ONR_LTP_ID = P_LINK_TYPE
    AND LTA_CHD_LTP_ID = 8)
  THEN
    /*XCal - If the schedule is being created and there are details add and link it*/
    IF (P_SCI_ID = NULL) OR (P_SCI_ID = 0) THEN
      IF (P_START <> 0) AND (P_END <> 0) THEN
        INSERT INTO sch_schedule_items (
          SCI_SCT_ID,SCI_START,SCI_END,SCI_BREAKS,SCI_UPDATED)
        VALUES (
          P_SCT_ID,P_START,P_END,P_BREAKS,current_timestamp());

	    SET P_SCI_ID = LAST_INSERT_ID();
        INSERT INTO lnk_links (
          LNK_ONR_LTP_ID,LNK_ONR_ID,LNK_CHD_LTP_ID,LNK_CHD_ID,LNK_UPDATED)
	    VALUES (
          P_LINK_TYPE,P_LINK_ID,8,P_SCI_ID,current_timestamp());

        SET P_RETURN_CODE = 1;
        SET P_RETURN_MSG = 'Schedule and link added.';
	  END IF;
    ELSE /*XCal - If we're linking or updating an existing schedule */
      SELECT
        SCI_SCT_ID,SCI_START,SCI_END,SCI_BREAKS
	  FROM
        sch_schedule_items
  	  WHERE
        SCI_ID = P_SCI_ID
	  INTO vSCT_ID,vStart,vEnd,vBreaks;

      /*XCal - Only update the group if it's been changed */
      IF (vSCT_ID <> P_SCT_ID) OR
	    (vStart <> P_START) OR (vEnd <> P_END) OR (vBreaks <> P_BREAKS)
	  THEN
        UPDATE sch_schedule_items SET
          SCI_SCT_ID = P_SCT_ID,
          SCI_START = P_START,
          SCI_END = P_END,
          SCI_BREAKS = P_BREAKS,
          SCI_UPDATED = current_timestamp()
        WHERE
          SCI_ID = P_SCI_ID;

		SET P_RETURN_CODE = 2;
		SET P_RETURN_MSG = 'Existing schedule updated.';
	  ELSE
        SET P_RETURN_MSG = 'Schedule not updated, no changes detected.';
      END IF;

      /*XCal - Update the link record if it exists to show when it was last confirmed,
      add the link record if it doesn't exist */
      IF EXISTS(
        SELECT LNK_UPDATED
        FROM lnk_links
        WHERE LNK_ONR_LTP_ID = P_LINK_TYPE
        AND LNK_ONR_ID = P_LINK_ID
        AND LNK_CHD_LTP_ID = 8
        AND LNK_CHD_ID = P_SCI_ID)
	  THEN
        UPDATE lnk_links SET
          LNK_UPDATED = current_timestamp()
        WHERE LNK_ONR_LTP_ID = P_LINK_TYPE
          AND LNK_ONR_ID = P_LINK_ID
          AND LNK_CHD_LTP_ID = 8
          AND LNK_CHD_ID = P_SCI_ID;

		SET P_RETURN_CODE = 2;
        IF LENGTH(P_RETURN_MSG) > 0 THEN
          SET P_RETURN_MSG = CONCAT(P_RETURN_MSG,' ');
		ELSE
          SET P_RETURN_MSG = '';
		END IF;
		SET P_RETURN_MSG = CONCAT(P_RETURN_MSG,'Existing Link Record Updated.');
	  ELSE
        INSERT INTO lnk_links (
          LNK_ONR_LTP_ID,
          LNK_ONR_ID,
          LNK_CHD_LTP_ID,
          LNK_CHD_ID,
          LNK_UPDATED)
	    VALUES (
          P_LINK_TYPE,
          P_LINK_ID,
          8,
          P_SCI_ID,
          current_timestamp());

		SET P_RETURN_CODE = 3;
        IF LENGTH(P_RETURN_MSG) > 0 THEN
          SET P_RETURN_MSG = CONCAT(P_RETURN_MSG,' ');
		ELSE
          SET P_RETURN_MSG = '';
		END IF;
		SET P_RETURN_MSG = CONCAT(P_RETURN_MSG,' Link record added.');
      END IF;
    END IF;
  ELSE
    SET P_RETURN_CODE = -1;
    SET P_RETURN_MSG = 'The requested link type is not allowed.';
  END IF;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_sch_rem_schedule_type` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE PROCEDURE `sp_sch_rem_schedule_type`(
  IN P_SCT_ID INT UNSIGNED,
  OUT P_RETURN_CODE INT,
  OUT P_RETURN_MSG VARCHAR(100))
BEGIN
  /*XCal - Supported return values
    -1 = Schedule Type ID not present to remove
    1 = Schedule Type deleted
    2 = Schedule Type in use, suppressed
  */
  IF EXISTS(
    SELECT
      SCT_ID
	FROM
      sch_schedule_types
	WHERE
      SCT_ID = P_SCT_ID)
  THEN
    IF NOT EXISTS(
      SELECT
        SCI_ID
	  FROM
        sch_schedule_items
	  WHERE
        SCI_SCT_ID = P_SCT_ID)
	THEN
      DELETE FROM sch_schedule_types
      WHERE SCT_ID = P_SCT_ID;

      SET P_RETURN_CODE = 1;
      SET P_RETURN_MSG = 'Schedule type deleted.';
    ELSE
      UPDATE sch_schedule_types SET
        SCT_SR = 1,
        SCT_UPDATED = current_timestamp()
	  WHERE SCT_ID = P_SCT_ID;

	  SET P_RETURN_CODE = 2;
      SET P_RETURN_MSG = 'Schedule type in use, record suppressed from searches.';
    END IF;
  ELSE
    SET P_RETURN_CODE = -1;
    SET P_RETURN_MSG = 'No record present to remove.';
  END IF;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_usr_rem_role` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE PROCEDURE `sp_usr_rem_role`(
  IN P_ROL_ID INT UNSIGNED,
  OUT P_RETURN_CODE INT,
  OUT P_RETURN_MSG VARCHAR(100))
BEGIN
  /*XCal - Supported return values
    -1 = Role ID not present to remove
    1 = Role deleted
    2 = Role in use, suppressed
  */
  IF EXISTS(
    SELECT
      ROL_ID
	FROM
      usr_roles
	WHERE
      ROL_ID = P_ROL_ID)
  THEN
    IF NOT EXISTS(
      SELECT
        URL_ROL_ID
	  FROM
        usr_user_roles
	  WHERE
        URL_ROL_ID = P_ROL_ID)
	THEN
      DELETE FROM usr_roles
      WHERE ROL_ID = P_ROL_ID;

      SET P_RETURN_CODE = 1;
      SET P_RETURN_MSG = 'Role deleted.';
    ELSE
      UPDATE usr_roles SET
        ROL_SR = 1,
        ROL_UPDATED = current_timestamp()
	  WHERE ROL_ID = P_ROL_ID;

	  SET P_RETURN_CODE = 2;
      SET P_RETURN_MSG = 'Role in use, record suppressed from searches.';
    END IF;
  ELSE
    SET P_RETURN_CODE = -1;
    SET P_RETURN_MSG = 'No record present to remove.';
  END IF;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_usr_rem_user` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE PROCEDURE `sp_usr_rem_user`(
  IN P_USR_ID INT UNSIGNED,
  OUT P_RETURN_CODE INT,
  OUT P_RETURN_MSG VARCHAR(100))
BEGIN
  /*XCal - Supported return values
    -1 = User ID not present to remove
    1 = User deleted
    2 = User in use, suppressed
  */
  IF EXISTS(
    SELECT
      USR_ID
	FROM
      usr_users
	WHERE
      USR_ID = P_USR_ID)
  THEN
    IF NOT EXISTS(
      SELECT
        WAC_USR_ID
	  FROM
        web_accounts
	  WHERE
        WAC_USR_ID = P_USR_ID)
	THEN
      DELETE FROM usr_user_roles
      WHERE URL_USR_ID = P_USR_ID;

      DELETE FROM usr_users
      WHERE USR_ID = P_USR_ID;

      SET P_RETURN_CODE = 1;
      SET P_RETURN_MSG = 'User and assigned roles deleted.';
    ELSE
      UPDATE usr_users SET
        USR_SR = 1,
        USR_UPDATED = current_timestamp()
	  WHERE USR_ID = P_USR_ID;

	  SET P_RETURN_CODE = 2;
      SET P_RETURN_MSG = 'User in use, record suppressed from searches.';
    END IF;
  ELSE
    SET P_RETURN_CODE = -1;
    SET P_RETURN_MSG = 'No record present to remove.';
  END IF;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_web_login_email` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE PROCEDURE `sp_web_login_email`(
  IN P_EMAIL VARCHAR(150),
  IN P_HASHPASS VARCHAR(100),
  OUT P_RETURN_CODE INT,
  OUT P_RETURN_MESSAGE VARCHAR(100),
  OUT P_TOKEN VARCHAR(36))
BEGIN
  DECLARE vID INT UNSIGNED;
  DECLARE vToken VARCHAR(36);
  DECLARE vExpiry DATETIME;

  SELECT
    WAC_ID
  FROM
    web_accounts
  WHERE
    WAC_EMAIL = P_EMAIL
    AND WAC_PASSHASH = P_HASHPASS
  INTO vID;

  IF vID IS NULL THEN
    SET P_RETURN_CODE = -1;
    SET P_RETURN_MESSAGE = 'No account found with that email address and password';
  ELSE
    SELECT
      WEL_TOKEN,
      WEL_EXPIRES
	FROM
      web_logins
	WHERE
      WEL_WAC_ID = vID
	INTO vToken, vExpiry;

    SET P_TOKEN = UUID();

    WHILE EXISTS(SELECT WEL_TOKEN FROM web_logins WHERE WEL_TOKEN = P_TOKEN) DO
      SET P_TOKEN = UUID();
	END WHILE;

    IF (vToken IS NULL) THEN
      INSERT INTO web_logins (
        WEL_TOKEN,
        WEL_WAC_ID,
        WEL_CREATED,
        WEL_EXPIRES)
	  VALUES (
        P_TOKEN,
        vID,
        current_timestamp(),
        current_timestamp() + interval 30 minute);

      SET P_RETURN_CODE = 1;
      SET P_RETURN_MESSAGE = 'Logged in.';
    ELSE
      UPDATE web_logins SET
        WEL_TOKEN = P_TOKEN,
        WEL_CREATED = current_timestamp(),
        WEL_EXPIRES = current_timestamp() + interval 30 minute
	  WHERE
        WEL_TOKEN = vToken
        AND WEL_WAC_ID = vID;

      IF vExpiry < current_timestamp() THEN
        SET P_RETURN_CODE = 2;
        SET P_RETURN_MESSAGE = 'Logged in, expired token replaced.';
	  ELSE
        SET P_RETURN_CODE = 3;
        SET P_RETURN_MESSAGE = 'Logged in, valid token replaced.';
	  END IF;
    END IF;
  END IF;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_web_login_username` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE PROCEDURE `sp_web_login_username`(
  IN P_USERNAME VARCHAR(50),
  IN P_HASHPASS VARCHAR(100),
  OUT P_RETURN_CODE INT,
  OUT P_RETURN_MESSAGE VARCHAR(100),
  OUT P_TOKEN VARCHAR(36))
BEGIN
  DECLARE vID INT UNSIGNED;
  DECLARE vToken VARCHAR(36);
  DECLARE vExpiry DATETIME;

  SELECT
    WAC_ID
  FROM
    web_accounts
  WHERE
    WAC_USERNAME = P_USERNAME
    AND WAC_PASSHASH = P_HASHPASS
  INTO vID;

  IF vID IS NULL THEN
    SET P_RETURN_CODE = -1;
    SET P_RETURN_MESSAGE = 'No account found with that user name and password';
  ELSE
    SELECT
      WEL_TOKEN,
      WEL_EXPIRES
	FROM
      web_logins
	WHERE
      WEL_WAC_ID = vID
	INTO vToken, vExpiry;

    SET P_TOKEN = UUID();

    WHILE EXISTS(SELECT WEL_TOKEN FROM web_logins WHERE WEL_TOKEN = P_TOKEN) DO
      SET P_TOKEN = UUID();
	END WHILE;

    IF (vToken IS NULL) THEN
      INSERT INTO web_logins (
        WEL_TOKEN,
        WEL_WAC_ID,
        WEL_CREATED,
        WEL_EXPIRES)
	  VALUES (
        P_TOKEN,
        vID,
        current_timestamp(),
        current_timestamp() + interval 30 minute);

      SET P_RETURN_CODE = 1;
      SET P_RETURN_MESSAGE = 'Logged in.';
    ELSE
      UPDATE web_logins SET
        WEL_TOKEN = P_TOKEN,
        WEL_CREATED = current_timestamp(),
        WEL_EXPIRES = current_timestamp() + interval 30 minute
	  WHERE
        WEL_TOKEN = vToken
        AND WEL_WAC_ID = vID;

      IF vExpiry < current_timestamp() THEN
        SET P_RETURN_CODE = 2;
        SET P_RETURN_MESSAGE = 'Logged in, expired token replaced.';
	  ELSE
        SET P_RETURN_CODE = 3;
        SET P_RETURN_MESSAGE = 'Logged in, valid token replaced.';
	  END IF;
    END IF;
  END IF;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_web_logout_token` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE PROCEDURE `sp_web_logout_token`(
  IN P_TOKEN VARCHAR(36),
  OUT P_RETURN_CODE INT,
  OUT P_RETURN_MESSAGE VARCHAR(100))
BEGIN
  IF EXISTS(SELECT WEL_WAC_ID FROM web_logins WHERE WEL_TOKEN = P_TOKEN) THEN
    DELETE FROM
      web_logins
	WHERE
      WEL_TOKEN = P_TOKEN;

    SET P_RETURN_CODE = 1;
    SET P_RETURN_MESSAGE = 'Logged out successfully';
  ELSE
    SET P_RETURN_CODE = -1;
    SET P_RETURN_MESSAGE = 'Unexpected error, login token not found';
  END IF;

  DELETE FROM
    web_logins
  WHERE
    WEL_EXPIRES < current_timestamp();
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_web_register_email` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE PROCEDURE `sp_web_register_email`(
  IN P_EMAIL VARCHAR(150),
  IN P_HASHPASS VARCHAR(100),
  OUT P_RETURN_CODE INT,
  OUT P_RETURN_MESSAGE VARCHAR(100),
  OUT P_TOKEN VARCHAR(100))
BEGIN
  DECLARE vID INT UNSIGNED;

  IF EXISTS(
    SELECT
      WAC_ID
    FROM
      web_accounts
    WHERE
      UPPER(WAC_EMAIL) = UPPER(P_EMAIL)
  ) THEN
    SET P_RETURN_CODE = -1;
    SET P_RETURN_MESSAGE = 'There is already an account for that email address.';
  ELSE
    INSERT INTO web_accounts (
      WAC_EMAIL,
      WAC_PASSHASH,
      WAC_ACTIVE,
      WAC_UPDATED)
    VALUES (
      P_EMAIL,
      P_HASHPASS,
      0,
      current_timestamp());
    SET vID = LAST_INSERT_ID();
    SET P_TOKEN = UUID();
    SET P_TOKEN = sha256(P_TOKEN);

    INSERT INTO web_account_activations (
      WAA_WAC_ID,
      WAA_TOKEN)
	VALUES (
      vID,
      P_TOKEN);

	SET P_RETURN_CODE = 1;
    SET P_RETURN_MESSAGE = 'The account has been registered and awaits activation.';
  END IF;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_web_register_username` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE PROCEDURE `sp_web_register_username`(
  IN P_USERNAME VARCHAR(50),
  IN P_HASHPASS VARCHAR(100),
  OUT P_RETURN_CODE INT,
  OUT P_RETURN_MESSAGE VARCHAR(100),
  OUT P_TOKEN VARCHAR(36))
BEGIN
  DECLARE vID INT UNSIGNED;

  IF EXISTS(
    SELECT
      WAC_ID
    FROM
      web_accounts
    WHERE
      UPPER(WAC_USERNAME) = UPPER(P_USERNAME)
  ) THEN
    SET P_RETURN_CODE = -1;
    SET P_RETURN_MESSAGE = 'There is already an account for that username.';
  ELSE
    INSERT INTO web_accounts (
      WAC_USERNAME,
      WAC_PASSHASH,
      WAC_ACTIVE,
      WAC_UPDATED)
    VALUES (
      P_USERNAME,
      P_HASHPASS,
      1,
      current_timestamp());

    SET vID = LAST_INSERT_ID();
    SET P_TOKEN = UUID();

    WHILE EXISTS(SELECT WEL_TOKEN FROM web_logins WHERE WEL_TOKEN = P_TOKEN) DO
      SET P_TOKEN = UUID();
	END WHILE;

    INSERT INTO web_logins (
      WEL_TOKEN,
      WEL_WAC_ID,
      WEL_CREATED,
      WEL_EXPIRES)
    VALUES (
      P_TOKEN,
      vID,
      current_timestamp(),
      current_timestamp() + interval 30 minute);

	SET P_RETURN_CODE = 1;
    SET P_RETURN_MESSAGE = 'The account has been registered and you are now logged in.';
  END IF;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_web_validate_token` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE PROCEDURE `sp_web_validate_token`(
  IN P_TOKEN VARCHAR(36),
  OUT P_WAC_ID INT UNSIGNED)
BEGIN
  /*XCal - Return NULL if invalid for any reason or the logged in WAC_ID if valid*/
  DECLARE vWAC INT UNSIGNED;

  SET P_WAC_ID = NULL;

  SELECT
    WEL_WAC_ID
  FROM
    web_logins
  WHERE
    WEL_TOKEN = P_TOKEN
    AND WEL_EXPIRES >= current_timestamp()
  INTO vWAC;

  IF vWAC IS NOT NULL THEN

    SET P_WAC_ID = vWAC;

    UPDATE
      web_logins
	SET
      WEL_EXPIRES = current_timestamp() + interval 30 minute
    WHERE
      WEL_TOKEN = P_TOKEN;
  END IF;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2017-01-13 11:15:11
