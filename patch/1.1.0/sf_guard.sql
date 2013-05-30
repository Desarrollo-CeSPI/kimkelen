-- MySQL dump 10.13  Distrib 5.1.41, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: conservatorio
-- ------------------------------------------------------
-- Server version	5.1.41-3ubuntu12.1

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
-- Table structure for table `sf_guard_permission`
--

DROP TABLE IF EXISTS `sf_guard_permission`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sf_guard_permission` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sf_guard_permission_U_1` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=57 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sf_guard_permission`
--

LOCK TABLES `sf_guard_permission` WRITE;
/*!40000 ALTER TABLE `sf_guard_permission` DISABLE KEYS */;
INSERT INTO `sf_guard_permission` VALUES (1,'show_course','Listar y ver detalle de cursos y divisiones'),(2,'edit_course','Crear, editar y eliminar cursos y divisiones'),(3,'show_course_day','Listar y ver detalle de días de cursada'),(4,'edit_course_day','Crear, editar y eliminar días de cursada'),(5,'edit_absence_per_day','Crear, editar y eliminar inasistencias por día'),(6,'edit_absense_per_subject','Crear, editar y eliminar inasistencias por materia'),(7,'show_course_student_mark','Listar y ver calificaciones de los alumnos'),(8,'edit_course_student_mark','Crear, editar y eliminar calificaciones de los alumnos'),(9,'edit_correlative','Crear, editar y eliminar correlativas'),(10,'show_correlative','Listar y ver detalle de correlativas'),(11,'show_subject','Listar y ver detalle de materias'),(12,'edit_subject','Crear, editar y eliminar materias'),(13,'show_examination_subject_day','Listar y ver detalle de los días de examen de las materias'),(14,'edit_examination_subject_day','Crear, editar y eliminar días de examen de las materias'),(15,'show_personal_type','Listar y ver detalle de roles'),(16,'edit_personal_type','Crear, editar y eliminar roles'),(17,'show_examination_subject','Listar y ver detalle de las materias de una mesa de examen'),(18,'edit_examination_subject','Crear, editar y eliminar materias de una mesa de examen'),(19,'show_career_subject','Listar y ver materias de una carrera'),(20,'edit_career_subject','Crear, editar y eliminar materias de una carrera'),(21,'show_career','Listar y ver detalle de una carrera'),(22,'edit_career','Crear, editar y eliminar carreras'),(23,'show_career_student','Listar y ver detalle de inscripciones de alumnos'),(24,'edit_career_student','Inscribir alumnos en carreras'),(25,'show_group','Listar y ver detalle de grupos de usuarios (Recomendado para Administradores)'),(26,'edit_group','Crear, editar y eliminar grupos de usuarios (Recomendado para Administradores)'),(27,'show_student','Listar y ver detalle de alumnos'),(28,'edit_student','Crear, editar y eliminar alumnos'),(29,'edit_disciplinary_saction','Crear, editar y eliminar sanciones disciplinarias'),(30,'edit_module','Activar y desactivar agregados'),(31,'show_examination','Listar y ver detalle de mesas de examen'),(32,'edit_examination','Crear, editar y eliminar mesas de examen'),(33,'show_user','Listar y ver detalle de usuarios'),(34,'edit_user','Crear, editar y eliminar usuarios'),(35,'show_tutor','Listar y ver detalle de tutores'),(36,'edit_tutor','Crear, editar y eliminar tutores'),(37,'show_school_year','Listar y ver detalle de años lectivos'),(38,'edit_school_year','Crear, editar y eliminar años lectivos'),(39,'show_teacher','Listar y ver detalle de docentes'),(40,'edit_teacher','Crear, editar y eliminar docentes'),(41,'show_absence_reason','Listar y ver detalle de motivos de inasistencia'),(42,'edit_absence_reason','Crear, editar y eliminar motivos de inasistencia'),(43,'show_justification_type','Listar y ver detalle de tipos de justificación'),(44,'edit_justification_type','Crear, editar y eliminar tipos de justificación'),(45,'backup','Respaldo de datos (backup) (Recomendado para Administradores)'),(46,'show_absence_per_day','Listar y ver detalle de inasistencias por día'),(47,'show_absence_per_subject','Listar y ver detalle de inasistencias por materia'),(48,'show_disciplinary_sanction','Listar y ver detalle de sanciones disciplinarias'),(49,'edit_disciplinary_sanction','Crear, editar y eliminar sanciones disciplinarias'),(50,'show_deserter_students','Listar y ver detalle de alumnos desertores'),(51,'show_dissaproved_students','Listar y ver detalle de alumnos desaprobados'),(52,'show_repeater_students','Listar y ver detalle de alumnos repitentes'),(53,'private_access','Acceso a la parte privada de la aplicación (Recomendado para Administradores, Preceptores y Profesores)'),(54,'public_access','Acceso a la parte pública de la aplicación (Recomendado para Alumnos)'),(55,'show_occupation','Listar y ver detalle de ocupaciones'),(56,'edit_occupation','Crear, editar y eliminar ocupaciones');
/*!40000 ALTER TABLE `sf_guard_permission` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sf_guard_group_permission`
--

DROP TABLE IF EXISTS `sf_guard_group_permission`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sf_guard_group_permission` (
  `group_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL,
  PRIMARY KEY (`group_id`,`permission_id`),
  KEY `sf_guard_group_permission_FI_2` (`permission_id`),
  CONSTRAINT `sf_guard_group_permission_FK_1` FOREIGN KEY (`group_id`) REFERENCES `sf_guard_group` (`id`) ON DELETE CASCADE,
  CONSTRAINT `sf_guard_group_permission_FK_2` FOREIGN KEY (`permission_id`) REFERENCES `sf_guard_permission` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sf_guard_group_permission`
--

LOCK TABLES `sf_guard_group_permission` WRITE;
/*!40000 ALTER TABLE `sf_guard_group_permission` DISABLE KEYS */;
INSERT INTO `sf_guard_group_permission` VALUES (1,1),(2,1),(3,1),(1,2),(2,2),(1,3),(2,3),(1,4),(2,4),(1,5),(2,5),(1,6),(2,6),(1,7),(2,7),(3,7),(1,8),(2,8),(3,8),(1,9),(1,10),(1,11),(1,12),(1,13),(2,13),(3,13),(1,14),(2,14),(1,15),(2,15),(1,16),(1,17),(2,17),(3,17),(1,18),(2,18),(1,19),(1,20),(1,21),(1,22),(1,23),(2,23),(1,24),(2,24),(1,25),(1,26),(1,27),(2,27),(3,27),(1,28),(2,28),(1,29),(2,29),(1,30),(1,31),(2,31),(3,31),(1,32),(2,32),(1,33),(1,34),(2,34),(1,35),(2,35),(3,35),(1,36),(2,36),(1,37),(1,38),(1,39),(2,39),(1,40),(1,41),(2,41),(1,42),(2,42),(1,43),(2,43),(1,44),(2,44),(1,45),(1,46),(2,46),(1,47),(2,47),(1,48),(2,48),(1,49),(2,49),(1,50),(2,50),(1,51),(2,51),(1,52),(2,52),(1,53),(2,53),(3,53),(4,54),(1,55),(2,55),(1,56),(2,56);
/*!40000 ALTER TABLE `sf_guard_group_permission` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sf_guard_user_permission`
--

DROP TABLE IF EXISTS `sf_guard_user_permission`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sf_guard_user_permission` (
  `user_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL,
  PRIMARY KEY (`user_id`,`permission_id`),
  KEY `sf_guard_user_permission_FI_2` (`permission_id`),
  CONSTRAINT `sf_guard_user_permission_FK_1` FOREIGN KEY (`user_id`) REFERENCES `sf_guard_user` (`id`) ON DELETE CASCADE,
  CONSTRAINT `sf_guard_user_permission_FK_2` FOREIGN KEY (`permission_id`) REFERENCES `sf_guard_permission` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sf_guard_user_permission`
--

LOCK TABLES `sf_guard_user_permission` WRITE;
/*!40000 ALTER TABLE `sf_guard_user_permission` DISABLE KEYS */;
/*!40000 ALTER TABLE `sf_guard_user_permission` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2010-05-28 10:03:50
