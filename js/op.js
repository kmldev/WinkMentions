(function() {

  "use strict";

  $("form.login").on("submit",function(e){

    e.preventDefault();

    var me = $(this);
    me.waitMe();

    $.post("dologin.php",$(this).serialize() + '&login=1',function(data){

      if (data) {
        var js = false;
        try {
          js = $.parseJSON(data);
        } catch (e) {          
        }
        if (js) {
          if (js.logged) {
            me[0].submit();
          }
          else if (js.error) return swal( "" ,  js.error ,  "error" );
        }
        else swal( "" ,  "Problema di comunicazione, riprovare." ,  "error" );
      }
      else swal( "" ,  "Problema di connessione, riprovare." ,  "error" );

    })
    .always(function(){
      $(me).waitMe("hide");
    });

  });

  $(".btn-submit").on("click",function(e) {
    e.preventDefault();
    $(this).parent("form").trigger("submit");    
  });

})();