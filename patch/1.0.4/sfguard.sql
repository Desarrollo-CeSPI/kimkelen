-- MySQL dump 10.13  Distrib 5.1.41, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: alumnos
-- ------------------------------------------------------
-- Server version	5.1.41-3ubuntu12

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
-- Table structure for table `sf_guard_user`
--

DROP TABLE IF EXISTS `sf_guard_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sf_guard_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(128) NOT NULL,
  `algorithm` varchar(128) NOT NULL DEFAULT 'sha1',
  `salt` varchar(128) NOT NULL,
  `password` varchar(128) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `is_active` tinyint(4) NOT NULL DEFAULT '1',
  `is_super_admin` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `sf_guard_user_U_1` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sf_guard_user`
--

LOCK TABLES `sf_guard_user` WRITE;
/*!40000 ALTER TABLE `sf_guard_user` DISABLE KEYS */;
INSERT INTO `sf_guard_user` VALUES (1,'admin','sha1','be4106d85d048199ded83538799d662c','28c3a796e02d84bcfbd986eb84340bf500e146eb','2010-05-13 11:31:32',NULL,1,1),(2,'preceptor','sha1','4000ce54f3a5a69c91f5a93968140b83','9bcd68bbad25281bde2b15c3ea82a43af52cdbbd','2010-05-13 11:31:32',NULL,1,0),(3,'profesor','sha1','79d86d1bddc80f1dbf6b3c53f6be745c','ccbb5ea04201db9b3f2de2f13b058976f6d938ad','2010-05-13 11:31:32',NULL,1,0);
/*!40000 ALTER TABLE `sf_guard_user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sf_guard_group`
--

DROP TABLE IF EXISTS `sf_guard_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sf_guard_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sf_guard_group_U_1` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sf_guard_group`
--

LOCK TABLES `sf_guard_group` WRITE;
/*!40000 ALTER TABLE `sf_guard_group` DISABLE KEYS */;
INSERT INTO `sf_guard_group` VALUES (1,'Administrador',NULL),(2,'Preceptor',NULL),(3,'Profesor',NULL),(4,'Alumno',NULL);
/*!40000 ALTER TABLE `sf_guard_group` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sf_guard_user_profile`
--

DROP TABLE IF EXISTS `sf_guard_user_profile`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sf_guard_user_profile` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `first_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `identification_type` int(11) NOT NULL,
  `identification_number` varchar(20) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `password_last_change` date DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `observations` text,
  `address_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sf_guard_user_profile_FI_1` (`user_id`),
  KEY `sf_guard_user_profile_FI_2` (`address_id`),
  CONSTRAINT `sf_guard_user_profile_FK_1` FOREIGN KEY (`user_id`) REFERENCES `sf_guard_user` (`id`),
  CONSTRAINT `sf_guard_user_profile_FK_2` FOREIGN KEY (`address_id`) REFERENCES `address` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Representa el perfil de un usuario';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sf_guard_user_profile`
--

LOCK TABLES `sf_guard_user_profile` WRITE;
/*!40000 ALTER TABLE `sf_guard_user_profile` DISABLE KEYS */;
/*!40000 ALTER TABLE `sf_guard_user_profile` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `teacher_guard_user`
--

DROP TABLE IF EXISTS `teacher_guard_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `teacher_guard_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sf_guard_user_id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  PRIMARY KEY (`id`,`sf_guard_user_id`,`teacher_id`),
  UNIQUE KEY `teacher_guard_user` (`sf_guard_user_id`),
  KEY `teacher_guard_user_FI_2` (`teacher_id`),
  CONSTRAINT `teacher_guard_user_FK_1` FOREIGN KEY (`sf_guard_user_id`) REFERENCES `sf_guard_user` (`id`) ON DELETE CASCADE,
  CONSTRAINT `teacher_guard_user_FK_2` FOREIGN KEY (`teacher_id`) REFERENCES `teacher` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Representa la relaciÃ³n entre un profesor y su usuario en el';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `teacher_guard_user`
--

LOCK TABLES `teacher_guard_user` WRITE;
/*!40000 ALTER TABLE `teacher_guard_user` DISABLE KEYS */;
/*!40000 ALTER TABLE `teacher_guard_user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `student_guard_user`
--

DROP TABLE IF EXISTS `student_guard_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `student_guard_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sf_guard_user_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  PRIMARY KEY (`id`,`sf_guard_user_id`,`student_id`),
  UNIQUE KEY `teacher_guard_user` (`sf_guard_user_id`),
  KEY `student_guard_user_FI_2` (`student_id`),
  CONSTRAINT `student_guard_user_FK_1` FOREIGN KEY (`sf_guard_user_id`) REFERENCES `sf_guard_user` (`id`) ON DELETE CASCADE,
  CONSTRAINT `student_guard_user_FK_2` FOREIGN KEY (`student_id`) REFERENCES `student` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Representa la relaciÃ³n entre un alumno y su usuario en el s';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `student_guard_user`
--

LOCK TABLES `student_guard_user` WRITE;
/*!40000 ALTER TABLE `student_guard_user` DISABLE KEYS */;
/*!40000 ALTER TABLE `student_guard_user` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=InnoDB AUTO_INCREMENT=150 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sf_guard_permission`
--

LOCK TABLES `sf_guard_permission` WRITE;
/*!40000 ALTER TABLE `sf_guard_permission` DISABLE KEYS */;
INSERT INTO `sf_guard_permission` VALUES (1,'indexTutor','Listar tutores'),(2,'newTutor','Crear tutores'),(3,'editTutor','Editar tutores'),(4,'showTutor','Ver detalle de tutores'),(5,'deleteTutor','Borrar tutores'),(6,'indexTeacher','Listar docentes'),(7,'newTeacher','Crear docentes'),(8,'editTeacher','Editar docentes'),(9,'showTeacher','Ver detalle de docentes'),(10,'revertStateTeacher','Cambiar docentes a activos'),(11,'changeStateTeacher','Cambiar docentes a no activos'),(12,'indexSubject','Listar materias'),(13,'newSubject','Crear materias'),(14,'editSubject','Editar materias'),(15,'showSubject','Ver detalle de materias'),(16,'deleteSubject','Borrar materias'),(17,'showStudentsSubject','Ver alumnos de una materia'),(18,'showSchoolYearSubject','Ver años lectivos de una materia'),(19,'configurationSubject','Editar configuración de una materia'),(20,'getCareers','Ver carreras para una materia'),(21,'coursesForSchoolYearCareer','Ver cursos para el año lectivo'),(22,'indexStudent','Listar alumnos'),(23,'newStudent','Crear alumnos'),(24,'editStudent','Editar alumnos'),(25,'showStudent','Ver detalle de alumnos'),(26,'deleteStudent','Borrar alumnos'),(27,'equivalencesStudent','Editar equivalencias de alumnos'),(28,'deleteEquivalenceStudent','Borrar una equivalencia de un alumno'),(29,'registerCourseStudent','Enrolar alumnos en cursos'),(30,'registerStudentCareerStudent','Registrar alumnos a carreras'),(31,'printCareerCertificateStudent','Imprimir comprobante de inscripción de alumnos en carreras'),(32,'registerSchoolYearStudent','Enrolar alumnos en años lectivos'),(33,'disciplinarySanctionsStudent','Ver sanciones disciplinarias de alumnos'),(34,'generateUser','Generar usuarios para alumnos'),(35,'absencesPerDay','Ausencias por dia'),(36,'absencesPerSubject','Ausencias por materias'),(37,'exportToExcel','Generar archivos excel con datos de los alumnos'),(38,'indexCourse','Listar cursos'),(39,'newCourse','Crear cursos'),(40,'editCourse','Editar cursos'),(41,'showCourse','Ver detalle de cursos'),(42,'deleteCourse','Borrar cursos'),(43,'showInscriptedCourse','Ver alumnos inscriptos en los cursos'),(44,'scheduleCourse','Editar horarios de cursada'),(45,'configurationCourse','Editar la configuración de los cursos'),(46,'absencePerDayCourse','Cargar asistencias a los alumnos de un curso por dia'),(47,'absencePerSubjectCourse','Cargar asistencias a los alumnos de un curso por materia'),(48,'cloneCourse','Clonar un curso'),(49,'indexCareer','Listar carreras'),(50,'newCareer','Crear carreras'),(51,'editCareer','Editar carreras'),(52,'showCareer','Ver detalle de carrera'),(53,'deleteCareer','Borrar carreras'),(54,'cloneCareer','Duplicar una carrera'),(55,'configurationCareer','Configuración de una carrera'),(56,'showInscriptedCareer','Ver alumnos inscriptos a una carrera'),(57,'subjectsCareer','Ver materias de una carrera'),(58,'careerViewCareer','Ver plan de estudios de una carrera'),(59,'printCareerViewCareer','Imprimir plan de estudios de una carrera'),(60,'changeStatusCareer','Cambiar estado de una carrera'),(61,'indexPersonal','Listado de personal'),(62,'newPersonal','Crear nuevo personal'),(63,'editPersonal','Editar datos de personal'),(64,'showPersonal','Ver datos del personal'),(65,'deletePersonal','Borrar personal'),(66,'changeStatePersonal','Cambiar estado del personal'),(67,'indexPersonalType','Listado de tipo de personal'),(68,'newPersonalType','Crear nuevo tipo de personal'),(69,'editPersonalType','Editar datos de tipo de personal'),(70,'showPersonalType','Ver datos de tipo de personal'),(71,'deletePersonalType','Borrar tipo de personal'),(72,'indexSchoolYear','Listado de años escolares'),(73,'newSchoolYear','Crear nuevo año escolar'),(74,'showSchoolYear','Ver detalles de año escolar'),(75,'deleteSchoolYear','Borrar año escolar'),(76,'changeStateSchoolYear','Cambiar estado del año lectivo'),(77,'indexExamination','Listado de mesa de examenes'),(78,'newExamination','Crear nueva mesa de examenes'),(79,'editExamination','Editar datos de mesas de examenes'),(80,'showExamination','Ver datos de mesas de examenes'),(81,'deleteExamination','Borrar mesa de examen'),(82,'changeStatusExamination','Cambiar estado de una mesa de examen'),(83,'changeExaminationStatus','Cmabiar estado de una mesa de examen'),(84,'indexCareerStudent','Listado de alumnos inscriptos a carreras'),(85,'newCareerStudent','Inscribir alumno a Carrera'),(86,'editCareerStudent','Editar inscripción de alumnos a carreras'),(87,'showCareerStudent','Ver inscripción a carreras de alumnos'),(88,'deleteCareerStudent','Desinscribir alumno de carrera'),(89,'indexCareerSubject','Listar materias de una carrera'),(90,'newCareerSubject','Cargar materias en un plan de estudio'),(91,'editCareerSubject','Editar materias de un plan de estudio'),(92,'showCareerSubject','Ver detalle de una materia de un plan de estudio'),(93,'deleteCareerSubject','Borrar materias del plan de estudio de una carrera'),(94,'configurationCareerSubject','Configuración de una materia que pertenece a una carrera'),(95,'indexCorrelative','Listado correlativas de una materia en una carrera'),(96,'newCorrelative','Crear correlativas de una materia en una carrera'),(97,'editCorrelative','Editar correlativas de una materia en una carrera'),(98,'showCorrelative','Ver correlativas de una materia en una carrera'),(99,'deleteCorrelative','Borrar correlativas de una materia en una carrera'),(100,'indexCourseDay','Listado horarios de cursada'),(101,'newCourseDay','Crear horarios de cursada'),(102,'showCourseDay','Ver horarios de cursada'),(103,'deleteCourseDay','Borrar horarios de cursada'),(104,'newCourseStudentMark','Cargar calificaciones a los alumnos de un curso'),(105,'editCourseStudentMark','Editar calificaciones a los alumnos de un curso'),(106,'deleteCourseStudentMark','Borrar calificaciones a los alumnos de un curso'),(107,'indexExaminationSubject','Listado de materias de una mesa de examen'),(108,'newExaminationSubject','Cargar materias en una mesa de examen'),(109,'editExaminationSubject','Editar materias de una mesa de examen'),(110,'showExaminationSubject','Ver detalle de materias de una mesa de examen'),(111,'deleteExaminationSubject','Borrar materias de una mesa de examen'),(112,'marksExaminationSubject','Cargar calificaciones para una materia en una mesa de examen'),(113,'indexExaminationSubjectDay','Listado de horarios de materias de una mesa de examen'),(114,'newExaminationSubjectDay','Cargar horarios de materias en una mesa de examen'),(115,'editExaminationSubjectDay','Editar horarios de materias de una mesa de examen'),(116,'showExaminationSubjectDay','Ver detalle de horarios de materias de una mesa de examen'),(117,'deleteExaminationSubjectDay','Borrar horarios de materias de una mesa de examen'),(118,'editModules','Editar modulos del sistema (Recomendado solo para administradores)'),(119,'indexAbsence','Listado de asistencias'),(120,'newAbsence','Crear asistencias'),(121,'editAbsence','Editar asistencias'),(122,'showAbsence','Ver detalle de asistencias'),(123,'deleteAbsence','Borrar asistencias'),(124,'indexAbsenceReason','Listado de motivos de inasistencias'),(125,'newAbsenceReason','Crear motivo de inasistencias'),(126,'editAbsenceReason','Editar motivos de inasistencias'),(127,'showAbsenceReason','Ver detalle de motivos de inasistencias'),(128,'deleteAbsenceReason','Borrar motivos de inasistencias'),(129,'indexDisciplinarySanction','Listado de sanciones disciplinarias'),(130,'newDisciplinarySanction','Crear sansion disciplinaria'),(131,'editDisciplinarySanction','Editar sansion disciplinaria'),(132,'showDisciplinarySanction','Ver detalle de sansion disciplinaria'),(133,'deleteDisciplinarySanction','Borrar sansion disciplinaria'),(134,'indexJustificationType','Listado de justificaciones de inasistencias'),(135,'newJustificationType','Crear justificaciones de inasistencias'),(136,'editJustificationType','Editar justificaciones de inasistencias'),(137,'showJustificationType','Ver detalle de justificaciones de inasistencias'),(138,'deleteJustificationType','Borrar justificaciones de inasistencias'),(139,'indexUser','Listado de usuarios (Recomendado solo para administradores)'),(140,'newUser','Crear usuarios (Recomendado solo para administradores)'),(141,'editUser','Editar usuarios (Recomendado solo para administradores)'),(142,'showUser','Ver detalle de usuarios (Recomendado solo para administradores)'),(143,'deleteUser','Borrar usuarios (Recomendado solo para administradores)'),(144,'privateAccess','Acceso privado (Docentes, Administradores, Preceptores, etc)'),(145,'publicAccess','Acceso publico (Alumnos)'),(146,'indexDisapprovedStudent','Listado de alumnos desaprobados'),(147,'showDisapprovedStudent','Ver detalle de un alumnos desaprobado'),(148,'canDoBackup','Generar backups(respaldos) del sistema'),(149,'administration_menu','Ver el menu de administrador');
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
INSERT INTO `sf_guard_group_permission` VALUES (2,1),(2,2),(2,3),(2,4),(2,5),(2,22),(3,22),(2,23),(2,24),(2,25),(3,25),(2,26),(2,27),(2,32),(2,33),(2,34),(3,36),(2,38),(3,38),(2,39),(2,40),(2,41),(3,41),(2,44),(2,46),(2,47),(3,47),(2,48),(2,49),(3,49),(3,52),(2,55),(2,56),(3,56),(2,58),(3,58),(2,59),(2,77),(3,77),(2,78),(2,79),(2,80),(2,85),(2,100),(2,101),(2,104),(3,104),(2,107),(3,107),(2,108),(2,109),(3,109),(2,110),(3,110),(2,111),(2,112),(3,112),(2,113),(3,113),(2,114),(3,114),(2,115),(3,115),(3,117),(3,119),(4,119),(3,120),(3,121),(3,122),(4,122),(3,123),(2,129),(3,129),(4,129),(3,132),(4,132),(2,144),(3,144),(4,145),(2,146),(3,146),(2,147),(3,147);
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

--
-- Table structure for table `sf_guard_user_group`
--

DROP TABLE IF EXISTS `sf_guard_user_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sf_guard_user_group` (
  `user_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  PRIMARY KEY (`user_id`,`group_id`),
  KEY `sf_guard_user_group_FI_2` (`group_id`),
  CONSTRAINT `sf_guard_user_group_FK_1` FOREIGN KEY (`user_id`) REFERENCES `sf_guard_user` (`id`) ON DELETE CASCADE,
  CONSTRAINT `sf_guard_user_group_FK_2` FOREIGN KEY (`group_id`) REFERENCES `sf_guard_group` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sf_guard_user_group`
--

LOCK TABLES `sf_guard_user_group` WRITE;
/*!40000 ALTER TABLE `sf_guard_user_group` DISABLE KEYS */;
INSERT INTO `sf_guard_user_group` VALUES (2,2),(3,3);
/*!40000 ALTER TABLE `sf_guard_user_group` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sf_guard_remember_key`
--

DROP TABLE IF EXISTS `sf_guard_remember_key`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sf_guard_remember_key` (
  `user_id` int(11) NOT NULL,
  `remember_key` varchar(32) DEFAULT NULL,
  `ip_address` varchar(50) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`user_id`,`ip_address`),
  CONSTRAINT `sf_guard_remember_key_FK_1` FOREIGN KEY (`user_id`) REFERENCES `sf_guard_user` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sf_guard_remember_key`
--

LOCK TABLES `sf_guard_remember_key` WRITE;
/*!40000 ALTER TABLE `sf_guard_remember_key` DISABLE KEYS */;
/*!40000 ALTER TABLE `sf_guard_remember_key` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2010-05-13 11:31:41
