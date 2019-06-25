<?php if(is_null($person)):

 echo json_encode( array(
                'error'  => 404,
                'mensaje' => "404 Not Found",
                'descripcion' => "La persona no existe"
          ));

  else:

echo json_encode( array(
                'email'  => $person->getEmail(),
                'telefono'  => $person->getPhone(),
                'persona'  => $person->getId(),
          ));

endif; 

?>
