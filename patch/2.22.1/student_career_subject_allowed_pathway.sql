CREATE TABLE `student_career_subject_allowed_pathway` (
  `id` int(11) NOT NULL,
  `career_subject_id` int(11) NOT NULL COMMENT 'Referencia a la materia de una carrera',
  `student_id` int(11) NOT NULL COMMENT 'Referencia al estudiante'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Representa que materias puede cursar un alumno';

ALTER TABLE `student_career_subject_allowed_pathway`
  ADD PRIMARY KEY (`id`,`career_subject_id`,`student_id`),
  ADD UNIQUE KEY `career_subject_student` (`career_subject_id`,`student_id`),
  ADD KEY `career_subject_student_index` (`student_id`,`career_subject_id`);

ALTER TABLE `student_career_subject_allowed_pathway`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=249377;

ALTER TABLE `student_career_subject_allowed_pathway`
  ADD CONSTRAINT `student_career_subject_allowed_pathway_FK_1` FOREIGN KEY (`career_subject_id`) REFERENCES `career_subject` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `student_career_subject_allowed_pathway_FK_2` FOREIGN KEY (`student_id`) REFERENCES `student` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

