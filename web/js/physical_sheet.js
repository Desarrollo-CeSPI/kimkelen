window.addEventListener('load', function() {
        document.getElementById("book_id" ).addEventListener('change', function() {
            books = document.getElementsByClassName('book_sheet');

            book_id = document.getElementById("book_id").value;
            for (i = 0; i < books.length; i++) 
            {
              //document.getElementsByClassName('book_sheet')[i].selectedIndex = book_id;
              document.getElementsByClassName('book_sheet')[i].value = book_id;
            } 
            
            elements = document.getElementsByClassName("check_sheet_book_desc" )
            for (i = 0; i < elements.length; i++) 
            {
                document.getElementsByClassName('check_sheet_book_desc')[i].hide();
                
                
                url = "/checkSheetBook?book_id="+ book_id +"&physical_sheet="+document.getElementsByClassName('physical_sheet')[i].value ;
                jQuery.ajax({
                url: url,
                success: function (data)
                {                      
                  var element = jQuery('#check_sheet_book_' + i);
                  element.html(data);
                  element.show();
                }
            });
                      
            }
        }); 
     
        /*
        elements = document.getElementsByClassName("physical_sheet" )
            for (i = 0; i < elements.length; i++) 
            {
                document.getElementsByClassName('physical_sheet')[i].addEventListener('input', function(e) {
                book = document.getElementById("book_id").value;
             
                if (book !== '' )
                {   url = "/checkSheetBook?book_id="+ book +"&physical_sheet="+this.value ;
                    jQuery.ajax({
                    url: url,
                    success: function (data)
                    {
                      var element = jQuery('#check_sheet_book');
                      element.html(data);
                      element.show();
                    }
                  });
                }
                    

              });
              
            } */
    });
    
    function checkSheetBook(e,num)
    {
        book = document.getElementById("book_id").value;
        if (book !== '' )
        {   url = "/checkSheetBook?book_id="+ book +"&physical_sheet="+e.value ;
            jQuery.ajax({
            url: url,
            success: function (data)
            {                      
              var element = jQuery('#check_sheet_book_' + num);
              element.html(data);
              element.show();
            }
          });
        }
    }