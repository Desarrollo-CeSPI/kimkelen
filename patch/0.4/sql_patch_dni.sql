UPDATE student SET identification_number=REPLACE(identification_number, '.', '');
UPDATE teacher SET identification_number=REPLACE(identification_number, ',', '');
