var MIN_SIZE = 12;
var MAX_SIZE = 26;


function setFontSize(){
    var size = parseInt(jQuery.cookie("conservatorio"));
    if(isNaN(size)){
        jQuery.cookie("conservatorio", MIN_SIZE);
    }else{
        setSize(size);
    }
}

function zoomIn()
{
    var size = parseInt(jQuery.cookie("conservatorio")) + 1;
    if(size<=MAX_SIZE){
        setSize(size);
    }
}

function zoomOut()
{
    var size = parseInt(jQuery.cookie("conservatorio")) - 1;
    if(size>=MIN_SIZE){
        setSize(size);
    }
}

function setSize(size){
    jQuery.cookie("conservatorio", size);
    $('content').setStyle({
        fontSize: size+'px'
    });
}

function blockScreen(){
    jQuery(document.getElementById('content')).html('<div class="block_screen"><h1>Esta operacion va a llevar varios minutos, tenga paciencia</h1></div><div id= "block_screen"> <div>');
    var opts = {
        lines: 12, // The number of lines to draw
        length: 7, // The length of each line
        width: 4, // The line thickness
        radius: 10, // The radius of the inner circle
        color: '#000', // #rgb or #rrggbb
        speed: 1, // Rounds per second
        trail: 60, // Afterglow percentage
        shadow: true // Whether to render a shadow
    };
    var target = document.getElementById('block_screen');
    var spinner = new Spinner(opts).spin(target);
    sleep(10);
}

function selectReportTab(tab_id)
{
  jQuery("#report_tabs #tabs ul li").removeClass("selected");
  jQuery("#report_tabs #tabs ul li"+tab_id+'_tab').addClass("selected");

  jQuery(".tab_content").hide();
  jQuery(tab_id).show();
}

function blockScreenCareerSchoolYear(){
    var opts = {
        lines: 10, // The number of lines to draw
        length: 6, // The length of each line
        width: 4, // The line thickness
        radius: 6, // The radius of the inner circle
        color: '#000', // #rgb or #rrggbb
        speed: 1, // Rounds per second
        trail: 60, // Afterglow percentage
        shadow: true // Whether to render a shadow
    };
    var target = document.getElementById('block_screen');
    var spinner = new Spinner(opts).spin(target);
    sleep(10);
}