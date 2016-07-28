ALTER TABLE origin_school ADD city_id INTEGER NOT NULL;
ALTER TABLE origin_school ADD INDEX ( city_id );
ALTER TABLE origin_school ADD FOREIGN KEY (city_id) REFERENCES city (id);
