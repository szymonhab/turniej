$(function() { 
  $(".dodaj_rozgrywke").click(function() {
    $("#id_wybranej_szachownicy").val($(this).parent().parent().parent().find(".nr_szachownicy").text());
  })
  
  $(".wynik_rozgrywki").click(function() {
    $(this).parent().find(".wynik_rozgrywki").each(function(){
      $(this).removeClass('choosen').find(".wybrany span").removeClass('fa fa-check');
    })
    $(this).addClass('choosen').find(".wybrany span").addClass('fa fa-check');
    $('#id_wybranej_rozgrywki').val($(this).children(".wynik_rozgrywki_id").text());
  });
  
  spinner = new Spinner().spin($('.spinner')[0]);
});

var podliczWyniki = function() {
  $($('.spinner')[0]).data('spinner', spinner);
  var bg;

  $.ajax({
    'url': podliczWynikiAddress,
    'dataType': 'json',
    'success': function( data ) {
      $('.prow').remove();
      for(i = 0; i < data.length; i++) {
        if(data[i][4] == 1) {
          bg = "redBg";
        } else {
          bg = "";
        }
        
        $('#podliczone_wyniki').append("              \
          <tr class=\"prow " + bg + "\">              \
            <td>" + data[i][0] + "</td>               \
            <td class=\"c\">" + data[i][1] + "</td>   \
            <td class=\"c\">" + data[i][2] + "</td>   \
            <td class=\"c\">" + data[i][3] + "</td>   \
          </tr>                                       \
        ");
      }
      
      $('.spinner').empty();
    }
  });
}