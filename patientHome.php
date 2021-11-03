patient
for ($x = 0; $x < 0; $x++) {
            $medIDSUBMIT = $dataMedArrayP[$x][0];
            $drugPresSUBMIT = $dataMedArrayP[$x][1];
            $qtySUBMIT = $dataMedArrayP[$x][2];
            $doseSUBMIT = $dataMedArrayP[$x][3];
            $freqSUBMIT = $dataMedArrayP[$x][4];
            $UOMSUBMIT = $dataMedArrayP[$x][5];
            $routeSUBMIT = $dataMedArrayP[$x][6];
            $durationSUBMIT = $dataMedArrayP[$x][7];

            if($connection->query("INSERT INTO prescriptionhasorder(prescription_order_id, prescription_id, medicine_id, drug_prescription, quantity, dose, frequency, UOM, Route, stop_after) 
              VALUES (NULL,$presID,$medIDSUBMIT,'$drugPresSUBMIT',$qtySUBMIT,$doseSUBMIT,
              '$freqSUBMIT','$UOMSUBMIT','$routeSUBMIT','$durationSUBMIT')") === TRUE){

              }else{
                exit('no exist' . $dataMedArrayP);
              }
          }