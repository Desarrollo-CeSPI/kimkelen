jQuery(document).ready(function() {

    if(jQuery("#student_person-nationality_id").val() == '4'){

                        // es nacionalidad otra. muestro el input
                        jQuery(".sf_admin_form_field_person-nationality_other_id").show();
                }else{

                        jQuery(".sf_admin_form_field_person-nationality_other_id").hide();


                }



    jQuery("#student_person-nationality_id").change(function(){
                    if(jQuery("#student_person-nationality_id").val() == '4'){

                        // es nacionalidad otra. muestro el input
                        jQuery(".sf_admin_form_field_person-nationality_other_id").show();
                }else{


                        jQuery("#student_person-nationality_other_id option").removeAttr('selected');
                        jQuery("#student_person-nationality_other_id").val('');
                        jQuery(".sf_admin_form_field_person-nationality_other_id").hide();
                        $('#student_person-nationality_other_id option[value=]').attr('selected','selected');


                }
        });
  });

