$(function() {
  countGames();
  
  $("#group_amount").change(function() {
    countGames();
  });
  
});

var countGames = function() {
  var iloscGrup = $("#group_amount").val();
  var result = 0;
  var iloscZawodnikowWGrupie = [];
  var tmp = iloscZawodnikow;
  
  for(i = 0; i < iloscGrup; i++) {
    iloscZawodnikowWGrupie[i] = Math.ceil(tmp / (iloscGrup - i));
    
    tmp = tmp - Math.ceil(tmp / (iloscGrup - i));
  }
  
  for(i = 0; i < iloscGrup; i++) {
    result = result + ( iloscZawodnikowWGrupie[i] * (iloscZawodnikowWGrupie[i]-1) / 2 ); 
    if(( iloscZawodnikowWGrupie[i] * (iloscZawodnikowWGrupie[i]-1) / 2 ) == 0) {
      result = '-';
      $("#group_amount_games").text(result);
      
      return;
    }
  }
  
  $("#group_amount_games").text(result);
}