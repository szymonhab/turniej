$(document).ready(

  function() {
    $("#menu li a").click(
      function() {
        $(this).siblings(".podmenu").toggle("normal");
        $(this).addClass("selected");
        $("#menu li a").not(this).siblings(".podmenu").hide("normal");
        $("#menu li a").not(this).removeClass("selected");
      }       
    );
    
    $("#max").click(
      function() {
        $("#content").css("width", "1200");
        $("#max").hide();
        $("#min").show();
        $("#menu").hide();
      }
    );
    
    $("#min").click(
      function() {
        $("#content").css("width", "980");
        $("#min").hide();
        $("#max").show();
        $("#menu").show();
      }
    );
    
    $(".datepicker").datepicker({ 
    	dateFormat: "yy-mm-dd" 
    });
    
    $(".prompt").attr("onclick", "return confirm('Czy napewno chcesz kontynuować? Ta akcja będzie nieodwracalna!');")
    $('form:not(.js-allow-double-submission)').preventDoubleSubmission();
  }
);

//jQuery plugin blokada podwójnego zatwierdzania formularzy
jQuery.fn.preventDoubleSubmission = function() {
  $(this).on('submit',function(e){
    var $form = $(this);

    if ($form.data('submitted') === true) {
      e.preventDefault();
    } else {
      $form.data('submitted', true);
    }
  });

  // Keep chainability
  return this;
};