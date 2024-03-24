(function() {

  "use strict";

  window.app = {};

  var pathname = window.location.pathname;
  var pagename = pathname.substring(pathname.lastIndexOf('/') + 1);

  pagename && $("a.nav-link[href='"+pagename+"']").parent().addClass("active");

  $(window).on("load",function(){

    var $me = $('#dataTable');

    var data = $me.data("filters");

    $me.DataTable({
      "processing": true,
      "serverSide": true,
      "ajax": {
        "url":"./getdataset.php",
        "type":"POST",
        "data":data
      },
      "language": {
        "search": "Cerca:",
        "lengthMenu": "Visualizza _MENU_ righe per pagina",
        "zeroRecords": "Nessun risultato",
        "info": "Pagina _PAGE_ di _PAGES_",
        "infoEmpty": "Nessun risultato disponibile",
        "infoFiltered": "(su _MAX_)",
        paginate: {
          first:      "Primo",
          previous:   "Precedente",
          next:       "Successivo",
          last:       "Ultimo"
        }
      }
    });

  });

  $("div[data-fileupload]").each(function(){
    var me = $(this);
    var upload = me.data("fileupload");
    var accept = me.data("accept");
    var drop = new Dropzone(this,{
      url: "./dofileupload.php", acceptedFiles: accept, params: {'mode':upload}
    });
    drop.on("sending",function(file){
      $("div.dropfiles").waitMe({"text":"upload del file in corso..."});
    });

    drop.on("error",function(file,reply){      
      $("div.dropfiles").waitMe("hide");
    });

    drop.on("success",function(file,reply){

        $("div.dropfiles").waitMe("hide");

        var js = $.parseJSON(reply);
        var fname = js.name;

        $("div.dropfiles").waitMe({"text":"elaborazione del file in corso..."});

        $.get("dofileimport.php",{"mode":upload,"file":fname},function(data){            

          $("div.dropfiles").waitMe("hide");

          var js = (data) ? $.parseJSON(data) : false;
          if (js && js.done) {
            me.parent().find(".import-done span").text(js.done);
            me.parent().find(".import-done").addClass("success");
          } else {
            //console.log('error',js);
            $("div.drop-zone div.dz-file-preview").remove();
            if (js.error) swal('Errore di import',js.error,'error');
          }
        });
    });    
  });

  $("body").on("click","a.docs-link-img",function(e){
    e.preventDefault();
    var me = $(this);
    var link = me.data("link");
    if (link) {
      //$.swipebox([{href:'getdoc.php?'+link}]);
      $.featherlight('getdoc.php?'+link, {type: 'image'});
    }
  });

  $("body").on("click","a.docs-link-pdf",function(e){
    e.preventDefault();
    var me = $(this);
    var link = me.data("link");
    if (link) {
      //window.open('getdoc.php?'+link);
      $.featherlight('getdoc.php?'+link, {type: 'iframe', iframeWidth: "800",
      iframeHeight: "600"});
    }
  });

  $("body").on("click","input.custom-switch-input.chk-accettazione",function(e){
    var me = $(this);
    var chk = me.is(":checked") ? '1' : '0';
    $.post("dovalidate.php",{k:me.data('key'),v:chk},function(data){
      if (data && (data=='1')) {
        //foo
      } else {
        swal("","Problema in update","error");
      }
    })
    .fail(function(){
      swal("","Problema in update, riaggiorna la pagina.","error");
    });
  });

  $("body").on("click","input.chk-validated",function(e){
    var me = $(this);
    var chk = me.is(":checked") ? '1' : '0';
    $.post("doprevalidated.php",{k:me.data('key'),v:chk},function(data){
      if (data && (data=='1')) {
        //foo
      } else {
        swal("","Problema in update","error");
      }
    })
    .fail(function(){
      swal("","Problema in update, riaggiorna la pagina.","error");
    });
  });

  $("body").on("click","a.btn-note",function(e){
    var me = $(this);
    var key = me.data('key');
    var note = me.data('note');

    swal({
      title: "Aggiungi una nota",
      content: {
        element: "textarea",
        attributes: {
          rows: 10,
          value: note
        },
      },
      buttons: {
        confirm: "Salva",
      }
    }).then( function(value) {
      if (value) {
        var valtxa = $("textarea.swal-content__textarea").val();
        $.post("doprevalidated.php",{k:key,v:9,n:valtxa},function(data){
          me.data('note',valtxa);
        });
      }
    });

  });

  var pdvinfo = $("#pdvinfo");

  $("#pdvlookup").autocomplete({
    bootstrapVersion:4,
    request: {url: "dopdvlookup.php"},
    input: function(input){
      pdvinfo[0].reset();
    },
    transfer: function(item) {
      var val = $(item).text();
      var info = val.split(' - ');
      pdvinfo[0].reset();
      $.get("./dopdvlookup.php",{q:info[0],d:1},function(data){
        if (data) {
          for (var a in data) {
            var vv = data[a];
            pdvinfo.find("[name='"+a+"']").val(vv);
          }
        }
      },"json");
      return info[1].split(' ')[0];
    }
  });

  $(".btn-add-busta").click(function(e){
    e.preventDefault();
    var cc = pdvinfo.find("[name='codcliente']").val();
    if (cc) {
      var campagna = pdvinfo.find("[name='campagna']").val();
      location.href = 'addbusta.php?w='+cc+'&c='+campagna;
    } else {
      doAlert("Devi cercare un punto vendita valido");
    }
  });

  $(".btn-info-pdv").click(function(e){
    e.preventDefault();
    var piva = pdvinfo.find("[name='piva']").val();
    if (piva) {
      var campagna = pdvinfo.find("[name='campagna']").val();
      location.href = 'infopdv.php?p='+piva+'&c='+campagna;
    } else {
      doAlert("Devi cercare un punto vendita valido");
    }
  });


  $("a.btn-submit").click(function(e){
    e.preventDefault();
    var me = $(this);
    var form = me.parents("form");
    $(form).submit();
  });

  $("input[name='numbuoni']").change(function(){
  
    var val = $(this).val();
    $("input[name='costo']").val(0);
    $.get('doaddbusta.php',{v:val},function(data){
      $("input[name='costo']").val(data.v);
    },"json");

  });

  $("a.btn-add-buono").click(function(e){
    e.preventDefault();
    var me = $(this);
    var valore = me.data('buono');
    var prod1 = $("#prod1");
    var prod2 = $("#prod2");    

    $("form[name='addbuono']")[0].reset();

    $("#prod1.d-none").removeClass("d-none").hide().slideDown();    
    prod1.find("[name='codean']").focus();
    prod1.find("[name='valore']").val(valore);
    if (valore == 12) {      
      $("#prod2.d-none").removeClass("d-none").prop("disabled",false).hide().slideDown();
    } else {
      prod2.addClass("d-none").prop("disabled",true);
    }
  });

  $("a.btn-chiudi-trans").click(function(e){
    var $this = $(this);
    var act = $this.data("act") || 0;
    var tot = $this.data("tot") || 0;
    if (act != tot) {
      e.preventDefault();

      swal({
        title: "Il numero di coupon non corrisponde",
        text: "Attenzione, il numero di coupon indicato quando hai creato la busta non corrisponde al numero di coupon attualmente inseriti, se prosegui e chiudi la busta, il numero coupon totale verrà aggiornato a "+act+".",
        icon: "warning",
        buttons: true,
        dangerMode: true,
      })
      .then(function(willDelete) {
        if (willDelete) {
          location.href = $this.attr("href");          
        }
      });

    }
  });

  var eanlookup = null;

  var eanrequest = {
    url: "doeanlookup.php",
    data: {v: $("#prod1 [name='cercaean']").data("valore")}
  };

  $(".eanlookup").each(function(){

    var me = $(this);
    var grp = me.data("grp");
    var form = me.parents("fieldset");

    eanlookup = me.autocomplete({
      bootstrapVersion:4,
      request: eanrequest,
      searchStart: function(input) {
        eanrequest.v = $("[name='valore']").val(); 
      },
      input: function(input){
        $("[rel='"+grp+"']").val('');
      },
      result: function(data) {
        var lst = [];
        var valore = $("[name='valore']").val();
        data.forEach(function(item){          
          if (item.valore==valore) lst.push(item.txt);
        });
        return lst;
      },
      transfer: function(item) {
        var val = $(item).text();
        var info = val.split(' - ');
        $("[rel='"+grp+"']").val('');
        $.get("./doeanlookup.php",{q:info[0],d:1,g:grp,v:$("[name='valore']").val()},function(data){
          if (data) {
            var xg = (grp=='f2') ? '2' : ''; 
            for (var a in data) {
              var vv = data[a];
              form.find("[name='"+a+xg+"'][rel='"+grp+"']").val(vv);
            }
            var valore = $("[name='valore']").val();
            form.find("[name='valsconto']").val(valore);
            form.find("[name='valsconto2']").val(valore);
            form.find("[name='provaacquisto"+xg+"']").focus();
            $("fieldset#pulsanti").removeClass("d-none");
          }
        },"json");
        return info[0];
      }
    });

  });

  $("fieldset#prod1,fieldset#prod2").addClass("d-none");

  $("[name='ean']").each(function(){
    var me = $(this);
    var vv = me.val();
    var valore = $("[name='valore']").val();
    if (vv) me.parents("fieldset").removeClass("d-none");
    if (vv) $("fieldset#pulsanti").removeClass("d-none");
    if (valore==12) $("fieldset#prod2").removeClass("d-none").prop("disabled",false);
  });

  if (window.buttons) {
    var v;
    for(var a in window.buttons) {
      v = window.buttons[a];
      $("a."+a+"[data-value='"+v+"']").addClass("selected");
    }
  }

  var doAlert = function(msg,alerttype) {
    if(!alerttype) alerttype = "";
    swal("",msg,alerttype);
  }

  window.app.doAlert = doAlert;

})();