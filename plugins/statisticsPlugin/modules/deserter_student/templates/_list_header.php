<table>
  <tr>
    <th> <?php echo  __('Cantidad de alumnos desertores');?> </th>
  </tr>
  <tr>
    <td> <?php echo __('Se registraron: '.$sf_user->getAttribute('students_count').' alumnos');?> </td>
  </tr>
</table>
<table>
  <tr>
    <?php $total = count(StudentPeer::doSelect(new Criteria()));?>
    <th> <?php echo __('Porcentaje de alumnos desertores del total');?> </th>
  </tr>
  <tr>
    <td> <?php echo __(number_format($sf_user->getAttribute('students_percentaje'),2).' %');?> </td>
  </tr>
</table>




