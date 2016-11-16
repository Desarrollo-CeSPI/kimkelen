alter table school_year_student modify health_info VARCHAR(50) NOT NULL DEFAULT 'No entregado';
alter table school_year_student add date_health_info DATE default NULL;
