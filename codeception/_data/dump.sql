-- MySQL dump 10.14  Distrib 5.5.37-MariaDB, for Linux (x86_64)
--
-- Host: localhost    Database: phalcon_incubator
-- ------------------------------------------------------
-- Server version	5.5.36-MariaDB-log

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
-- Table structure for table `access_list`
--

DROP TABLE IF EXISTS `access_list`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `access_list` (
  `roles_name` varchar(32) NOT NULL,
  `resources_name` varchar(32) NOT NULL,
  `access_name` varchar(32) NOT NULL,
  `allowed` int(3) NOT NULL,
  PRIMARY KEY (`roles_name`,`resources_name`,`access_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `access_list`
--

LOCK TABLES `access_list` WRITE;
/*!40000 ALTER TABLE `access_list` DISABLE KEYS */;
/*!40000 ALTER TABLE `access_list` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `acl_access`
--

DROP TABLE IF EXISTS `acl_access`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `acl_access` (
  `acs_role` varchar(255) NOT NULL,
  `acs_resource` varchar(255) NOT NULL,
  `acs_operation` varchar(255) NOT NULL,
  PRIMARY KEY (`acs_role`,`acs_resource`,`acs_operation`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `acl_access`
--

LOCK TABLES `acl_access` WRITE;
/*!40000 ALTER TABLE `acl_access` DISABLE KEYS */;
/*!40000 ALTER TABLE `acl_access` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `acl_resource`
--

DROP TABLE IF EXISTS `acl_resource`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `acl_resource` (
  `rsc_name` varchar(255) NOT NULL,
  `rsc_operation` varchar(255) NOT NULL,
  PRIMARY KEY (`rsc_name`,`rsc_operation`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `acl_resource`
--

LOCK TABLES `acl_resource` WRITE;
/*!40000 ALTER TABLE `acl_resource` DISABLE KEYS */;
/*!40000 ALTER TABLE `acl_resource` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `acl_role`
--

DROP TABLE IF EXISTS `acl_role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `acl_role` (
  `rl_name` varchar(255) NOT NULL,
  `rl_inherits` varchar(255) NOT NULL,
  PRIMARY KEY (`rl_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `acl_role`
--

LOCK TABLES `acl_role` WRITE;
/*!40000 ALTER TABLE `acl_role` DISABLE KEYS */;
/*!40000 ALTER TABLE `acl_role` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `resources`
--

DROP TABLE IF EXISTS `resources`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `resources` (
  `name` varchar(32) NOT NULL,
  `description` text,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `resources`
--

LOCK TABLES `resources` WRITE;
/*!40000 ALTER TABLE `resources` DISABLE KEYS */;
/*!40000 ALTER TABLE `resources` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `resources_accesses`
--

DROP TABLE IF EXISTS `resources_accesses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `resources_accesses` (
  `resources_name` varchar(32) NOT NULL,
  `access_name` varchar(32) NOT NULL,
  PRIMARY KEY (`resources_name`,`access_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `resources_accesses`
--

LOCK TABLES `resources_accesses` WRITE;
/*!40000 ALTER TABLE `resources_accesses` DISABLE KEYS */;
/*!40000 ALTER TABLE `resources_accesses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `roles` (
  `name` varchar(32) NOT NULL,
  `description` text,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles_inherits`
--

DROP TABLE IF EXISTS `roles_inherits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `roles_inherits` (
  `roles_name` varchar(32) NOT NULL,
  `roles_inherit` varchar(32) NOT NULL,
  PRIMARY KEY (`roles_name`,`roles_inherit`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles_inherits`
--

LOCK TABLES `roles_inherits` WRITE;
/*!40000 ALTER TABLE `roles_inherits` DISABLE KEYS */;
/*!40000 ALTER TABLE `roles_inherits` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2014-06-10  3:37:50
