/**
 * CONSERVATORIO
 *
 * @version 0.3 
 **/

/* Create student_school_year */

CREATE TABLE `student_school_year` (
  `id` int(11) NOT NULL auto_increment,
  `student_id` int(11) NOT NULL,
  `school_year_id` int(11) NOT NULL,
  `created_at` datetime default NULL,
  PRIMARY KEY  (`id`,`student_id`,`school_year_id`),
  KEY `student_school_year_FI_1` (`student_id`),
  KEY `student_school_year_FI_2` (`school_year_id`),
  CONSTRAINT `student_school_year_FK_1` FOREIGN KEY (`student_id`) REFERENCES `student` (`id`) ON DELETE CASCADE,
  CONSTRAINT `student_school_year_FK_2` FOREIGN KEY (`school_year_id`) REFERENCES `school_year` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
