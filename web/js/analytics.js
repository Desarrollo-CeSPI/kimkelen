window.addEventListener('load', function() {
  document.getElementById("analytic_certificate_number" ).addEventListener('change', function() {
      certificate = document.getElementById("analytic_certificate_number" ).value;
      url = document.getElementById('link_print').href;
      if(certificate.trim() != ''){
           document.getElementById('link_print').href= url + '&certificate=' + certificate;

      }      
  });
})
