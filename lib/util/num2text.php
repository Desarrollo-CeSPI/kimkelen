<?php

class num2text {

  public function Centenas($VCentena) {
    $Numeros[0] = "cero";
    $Numeros[1] = "uno";
    $Numeros[2] = "dos";
    $Numeros[3] = "tres";
    $Numeros[4] = "cuatro";
    $Numeros[5] = "cinco";
    $Numeros[6] = "seis";
    $Numeros[7] = "siete";
    $Numeros[8] = "ocho";
    $Numeros[9] = "nueve";
    $Numeros[10] = "diez";
    $Numeros[11] = "once";
    $Numeros[12] = "doce";
    $Numeros[13] = "trece";
    $Numeros[14] = "catorce";
    $Numeros[15] = "quince";
    $Numeros[20] = "veinte";
    $Numeros[30] = "treinta";
    $Numeros[40] = "cuarenta";
    $Numeros[50] = "cincuenta";
    $Numeros[60] = "sesenta";
    $Numeros[70] = "setenta";
    $Numeros[80] = "ochenta";
    $Numeros[90] = "noventa";
    $Numeros[100] = "ciento";
    $Numeros[101] = "quinientos";
    $Numeros[102] = "setecientos";
    $Numeros[103] = "novecientos";
    If ($VCentena == 1) {
      return $Numeros[100];
    }
    Else If ($VCentena == 5) {
      return $Numeros[101];
    }
    Else If ($VCentena == 7) {
      return ( $Numeros[102]);
    }
    Else If ($VCentena == 9) {
      return ($Numeros[103]);
    }
    Else {
      return $Numeros[$VCentena];
    }
  }

  public function Unidades($VUnidad) {
    $Numeros[0] = "cero";
    $Numeros[1] = "un";
    $Numeros[2] = "dos";
    $Numeros[3] = "tres";
    $Numeros[4] = "cuatro";
    $Numeros[5] = "cinco";
    $Numeros[6] = "seis";
    $Numeros[7] = "siete";
    $Numeros[8] = "ocho";
    $Numeros[9] = "nueve";
    $Numeros[10] = "diez";
    $Numeros[11] = "once";
    $Numeros[12] = "doce";
    $Numeros[13] = "trece";
    $Numeros[14] = "catorce";
    $Numeros[15] = "quince";
    $Numeros[20] = "veinte";
    $Numeros[30] = "treinta";
    $Numeros[40] = "cuarenta";
    $Numeros[50] = "cincuenta";
    $Numeros[60] = "sesenta";
    $Numeros[70] = "setenta";
    $Numeros[80] = "ochenta";
    $Numeros[90] = "noventa";
    $Numeros[100] = "ciento";
    $Numeros[101] = "quinientos";
    $Numeros[102] = "setecientos";
    $Numeros[103] = "novecientos";

    $tempo = $Numeros[$VUnidad];
    return $tempo;
  }

  public function Decenas($VDecena) {
    $Numeros[0] = "cero";
    $Numeros[1] = "uno";
    $Numeros[2] = "dos";
    $Numeros[3] = "tres";
    $Numeros[4] = "cuatro";
    $Numeros[5] = "cinco";
    $Numeros[6] = "seis";
    $Numeros[7] = "siete";
    $Numeros[8] = "ocho";
    $Numeros[9] = "nueve";
    $Numeros[10] = "diez";
    $Numeros[11] = "once";
    $Numeros[12] = "doce";
    $Numeros[13] = "trece";
    $Numeros[14] = "catorce";
    $Numeros[15] = "quince";
    $Numeros[20] = "veinte";
    $Numeros[30] = "treinta";
    $Numeros[40] = "cuarenta";
    $Numeros[50] = "cincuenta";
    $Numeros[60] = "sesenta";
    $Numeros[70] = "setenta";
    $Numeros[80] = "ochenta";
    $Numeros[90] = "noventa";
    $Numeros[100] = "ciento";
    $Numeros[101] = "quinientos";
    $Numeros[102] = "setecientos";
    $Numeros[103] = "novecientos";
    $tempo = ($Numeros[$VDecena]);
    return $tempo;
  }

  public function NumerosALetras($Numero) {

    $Decimales = 0;
    //$Numero = intval($Numero);
    $letras = "";

    while ($Numero != 0) {

      // '*---> Validación si se pasa de 100 millones
      If ($Numero >= 1000000000) {
        $letras = "Error en Conversión a Letras";
        $Numero = 0;
        $Decimales = 0;
      }

      // '*---> Centenas de Millón
      If (($Numero < 1000000000) And ($Numero >= 100000000)) {
        If ((Intval($Numero / 100000000) == 1) And (($Numero - (Intval($Numero / 100000000) * 100000000)) < 1000000)) {
          $letras .= (string) "cien millones ";
        }
        Else {
          $letras = $letras & $this->Centenas(Intval($Numero / 100000000));
          If ((Intval($Numero / 100000000) <> 1) And (Intval($Numero / 100000000) <> 5) And (Intval($Numero / 100000000) <> 7) And (Intval($Numero / 100000000) <> 9)) {
            $letras .= (string) "cientos ";
          }
          Else {
            $letras .= (string) " ";
          }
        }
        $Numero = $Numero - (Intval($Numero / 100000000) * 100000000);
      }

      // '*---> Decenas de Millón
      If (($Numero < 100000000) And ($Numero >= 10000000)) {
        If (Intval($Numero / 1000000) < 16) {
          $tempo = $this->Decenas(Intval($Numero / 1000000));
          $letras .= (string) $tempo;
          $letras .= (string) " millones ";
          $Numero = $Numero - (Intval($Numero / 1000000) * 1000000);
        }
        Else {
          $letras = $letras & $this->Decenas(Intval($Numero / 10000000) * 10);
          $Numero = $Numero - (Intval($Numero / 10000000) * 10000000);
          If ($Numero > 1000000) {
            $letras .= $letras & " y ";
          }
        }
      }
      // '*---> Unidades de Millón
      If (($Numero < 10000000) And ($Numero >= 1000000)) {
        $tempo = (Intval($Numero / 1000000));
        If ($tempo == 1) {
          $letras .= (string) " un millón ";
        }
        Else {
          $tempo = $this->Unidades(Intval($Numero / 1000000));
          $letras .= (string) $tempo;
          $letras .= (string) " millones ";
        }
        $Numero = $Numero - (Intval($Numero / 1000000) * 1000000);
      }

      // '*---> Centenas de Millar
      If (($Numero < 1000000) And ($Numero >= 100000)) {
        $tempo = (Intval($Numero / 100000));
        $tempo2 = ($Numero - ($tempo * 100000));
        If (($tempo == 1) And ($tempo2 < 1000)) {
          $letras .= (string) "cien mil ";
        }
        Else {
          $tempo = $this->Centenas(Intval($Numero / 100000));
          $letras .= (string) $tempo;
          $tempo = (Intval($Numero / 100000));
          If (($tempo <> 1) And ($tempo <> 5) And ($tempo <> 7) And ($tempo <> 9)) {
            $letras .= (string) "cientos ";
          }
          Else {
            $letras .= (string) " ";
          }
        }
        $Numero = $Numero - (Intval($Numero / 100000) * 100000);
      }

      // '*---> Decenas de Millar
      If (($Numero < 100000) And ($Numero >= 10000)) {
        $tempo = (Intval($Numero / 1000));
        If ($tempo < 16) {
          $tempo = $this->Decenas(Intval($Numero / 1000));
          $letras .= (string) $tempo;
          $letras .= (string) " mil ";
          $Numero = $Numero - (Intval($Numero / 1000) * 1000);
        }
        Else {
          $tempo = $this->Decenas(Intval($Numero / 10000) * 10);
          $letras .= (string) $tempo;
          $Numero = $Numero - (Intval(($Numero / 10000)) * 10000);
          If ($Numero > 1000) {
            $letras .= (string) " y ";
          }
          Else {
            $letras .= (string) " mil ";
          }
        }
      }

      // '*---> Unidades de Millar
      if (($Numero < 10000) And ($Numero >= 1000)) {
        $tempo = (Intval($Numero / 1000));
        If ($tempo == 1) {
          $letras .= (string) "un";
        }
        Else {
          $tempo = $this->Unidades(Intval($Numero / 1000));
          $letras .= (string) $tempo;
        }
        $letras .= (string) " mil ";
        $Numero = $Numero - (Intval($Numero / 1000) * 1000);
      }
      // '*---> Centenas
      If (($Numero < 1000) And ($Numero > 99)) {
        If ((Intval($Numero / 100) == 1) And (($Numero - (Intval($Numero / 100) * 100)) < 1)) {
          $letras = $letras & "cien ";
        }
        Else {
          $temp = (Intval($Numero / 100));
          $l2 = $this->Centenas($temp);
          $letras .= (string) $l2;
          If ((Intval($Numero / 100) <> 1) And (Intval($Numero / 100) <> 5) And (Intval($Numero / 100) <> 7) And (Intval($Numero / 100) <> 9)) {
            $letras .= "cientos ";
          }
          Else {
            $letras .= (string) " ";
          }
        }
        $Numero = $Numero - (Intval($Numero / 100) * 100);
      }
      // '*---> Decenas
      If (($Numero < 100) And ($Numero > 9)) {
        If ($Numero < 16) {
          $tempo = $this->Decenas(Intval($Numero));
          $letras .= $tempo;
          $Numero = $Numero - Intval($Numero);
        }
        Else {
          $tempo = $this->Decenas(Intval(($Numero / 10)) * 10);
          $letras .= (string) $tempo;
          $Numero = $Numero - (Intval(($Numero / 10)) * 10);
          If ($Numero > 0.99) {
            $letras .=(string) " y ";
          }
        }
      }
      // '*---> Unidades
      If (($Numero < 10) And ($Numero > 0.99)) {
        $tempo = $this->Unidades(Intval($Numero));
        $letras .= (string) $tempo;
        $Numero = $Numero - Intval($Numero);
      }


      // '*---> Decimales
      If ($Decimales > 0) {
        // $letras .=(string) " con ";
        // $Decimales= $Decimales*100;
        // echo ("*");
        // $Decimales = number_format($Decimales, 2);
        // echo ($Decimales);
        // $tempo = $this->Decenas(Intval($Decimales));
        // $letras .= (string) $tempo;
        // $letras .= (string) "centavos";
      }
      Else {
        If (($letras <> "Error en Conversión a Letras") And (strlen(Trim($letras)) > 0)) {
          $letras .= (string) " ";
        }
      }
      return $letras;
    }//while
  }

//funcion
//favor de teclear a mano la cantidad numerica a convertir y asignarla a $tt

  public function num2str($tt) {
    $tt = $tt + 0.009;
    $Numero = intval($tt);
    $Decimales = $tt - Intval($tt);
    $Decimales = $Decimales * 100;
    $Decimales = Intval($Decimales);
    $x = $this->NumerosALetras($Numero);

    If ($Decimales > 0) {
      $x .= " con " . $this->NumerosALetras($Decimales);
    }
    return strtoupper($x);
  }

}

