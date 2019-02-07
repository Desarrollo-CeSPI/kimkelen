<?php use_helper('Date') ?>
<?php echo json_encode( 
        array(
                "apellido" => $person->getLastname(),
                "nombres" => $person->getFirstname(),
                "genero"=> ($person->getSex() == 1)? "Masculino"  :"Femenino",
                "fecha_nacimiento" => format_date($person->getBirthdate(),'dd/MM/yyyy'),            
                "nacionalidad" => $person->getFullNationality(),
                "legajo" => $person->getStudent()->getGlobalFileNumber(),
                "pais_origen" => $person->getBirthCountry(),
                "codigo_pais_procedencia" => $person->getBirthCountry(),
                "nombre_pais_procedencia" => $person->getBirthCountryRepresentation(),
                "codigo_provincia_procedencia" => $person->getBirthState(),
                "nombre_provincia_procedencia"=> $person->getBirthStaterepresentation(),
                "codigo_localidad_procedencia" => $person->getBirthCity(),
                "nombre_localidad_procedencia" => $person->getBirthCityRepresentation(),
                "direccion_procedencia" => $person->getAddress()->getFullAddress()

          ))?>