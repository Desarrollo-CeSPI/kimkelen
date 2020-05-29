CREATE TABLE `observation_mark` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `letter` VARCHAR(2) NOT NULL,
  `description` VARCHAR(200) NOT NULL,
  PRIMARY KEY (`id`));

ALTER TABLE `course_subject_student_mark` 
ADD COLUMN `observation_mark_id` INT NULL;

ALTER TABLE `course_subject_student_mark` 
ADD INDEX `index4` (`observation_mark_id` ASC);

ALTER TABLE `course_subject_student_mark` 
ADD CONSTRAINT `fk_course_subject_student_mark_4`
  FOREIGN KEY (`observation_mark_id`)
  REFERENCES `observation_mark` (`id`)
  ON DELETE NO ACTION
  ON UPDATE NO ACTION;

INSERT INTO observation_mark (letter,description) 
VALUES ('A','Estableció un vínculo continuo con la asignatura. Participó de la totalidad de las actividades propuestas. Profundizó en la construcción del conocimiento.'),
('E','Estableció un vínculo continuo con la asignatura. Participó parcialmente de las actividades propuestas. Abordó la construcción del conocimiento.'),
('I','Estableció un vínculo discontinuo con la asignatura. Participó parcialmente de las actividades propuestas. Se dificultó la construcción del conocimiento.'),
('O','Estableció un mínimo vínculo con la asignatura. No participó de las actividades propuestas. No se pudo concretar la construcción del conocimiento.'),
('U','No estableció vínculo con la asignatura. No se ha podido iniciar el trayecto académico correspondiente.');
