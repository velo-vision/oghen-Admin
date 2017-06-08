/**
 * C3 charts page
 */

 var fecha;
 var total;
 var chart;

 fecha = ['x'];
 total = ['Satisfaccion General.'];

$( ".filtros" ).click(function() {
  var enc = $(".encuestas").val();
  var preg = $(".preguntas").val();
  var ru = $(".rubros").val();
  var emp = $(".empleados").val();
  var fi = $(".fechainicio").val();
  var ff = $(".fechafinal").val();






  if(fi == '' || ff == ''){
    alert('Seleccione una fecha');
    return;
  }else{
    $(".cssload-container").css("opacity","1");
    $("#chart").css("opacity","1");

  }

setTimeout(function(){
 $.ajax({
    url:'http://oghen.com/admin/getInfo.php?tipo=filtro&idencuesta='+enc+'&idpregunta='+preg+'&idrubro='+ru+'&idempleado='+emp+'&fechainicio='+fi+'&fechafinal='+ff,
    contentType: "application/json",
    type:'POST',
    dataType: "json",
    data:{
    },
    success:function(data){
     if(data != 'ERROR'){

      var pn = 0;
      var dia;
      var d;
      var l = 0;
      var v = [];

       if(preg != 'none'){
         //Likert
         if(data[0].cons_tipopregunta == 1){

           fecha = ['x'];
           total = ['Muy bueno'];
           total1 = ['Bueno'];
           total2 = ['Regular'];
           total3 = ['Malo'];
           total4 = ['Muy malo'];

           var mb = 0;
           var b = 0;
           var r = 0;
           var m = 0;
           var mm = 0;
           //console.log(data.length);
           for(var i=0; i < data.length; i++){

             if(i != 0 && dia != data[i].fecha){
               fecha.push(dia);
               total.push(mb);
               total1.push(b);
               total2.push(r);
               total3.push(m);
               total4.push(mm);

               mb = 0;
               b = 0;
               r = 0;
               m = 0;
               mm = 0;
               dia = '';

               dia = data[i].fecha;
             }

             if(dia == data[i].fecha){
               if(data[i].opcion == 'Muy bueno'){
                 mb++;
               }
               if(data[i].opcion == 'Bueno'){
                 b++;
               }
               if(data[i].opcion == 'Regular'){
                 r++;
               }
               if(data[i].opcion == 'Malo'){
                 m++;
               }
               if(data[i].opcion == 'Muy malo'){
                 mm++;
               }
             }

             if(i == data.length-1){
               fecha.push(dia);
               total.push(mb);
               total1.push(b);
               total2.push(r);
               total3.push(m);
               total4.push(mm);
             }

             if(i == 0){
               if(data[i].opcion == 'Muy bueno'){
                 mb++;
               }
               if(data[i].opcion == 'Bueno'){
                 b++;
               }
               if(data[i].opcion == 'Regular'){
                 r++;
               }
               if(data[i].opcion == 'Malo'){
                 m++;
               }
               if(data[i].opcion == 'Muy malo'){
                 mm++;
               }
             }

             dia = data[i].fecha;

           }

           setTimeout(function(){
             $(".cssload-container").css("opacity","0");
           }, 500);
           setTimeout(function(){
             $("#chart").css("opacity","1");
           }, 1000);

            chart.load({
              columns: [
                fecha,
                total,
                total1,
                total2,
                total3,
                total4
              ],
              unload: true,
              colors: {
                  'Muy bueno': '#2FAA07',
                  'Bueno': '#B3E223',
                  'Regular': '#FFD41D',
                  'Malo': '#FFAE1F',
                  'Muy malo': '#FF2727',
              },
            });

         }

         if(data[0].cons_tipopregunta == 2){
           fecha = [];
           fecha[0] = ['x'];
           var r = 0;
           var d = data;

           var o = {"0":"1","1":"2","2":"3","3":"4"};

           var tFechas = $.map(data['fechas'], function(el) { return el; })

           //fecha.push(tFechas);
           var tNombre;

           console.log(tFechas);

           for(var i=0 ; i<tFechas.length ; i++){
             fecha[0].push(tFechas[i]);
           }
          console.log(fecha);
           for(var i=0 ; i<data['ids'].length ; i++){
             v[i] = [];
             v[i].push(data['ids'][i].opcion);
             //console.log(data['ids'][i].opcion);
           }
           //console.log(v);
           for(var i=0 ; i<data['respuestas'].length ; i++){
             for(var j=0 ; j<data['ids'].length ; j++){
               if(data['respuestas'][i][data['ids'][j].id]){
                 v[j].push(data['respuestas'][i][data['ids'][j].id].total);
               }
               else{
                 v[j].push(0);
               }
             }
           }

           setTimeout(function(){
             $(".cssload-container").css("opacity","0");
           }, 500);
           setTimeout(function(){
             $("#chart").css("opacity","1");
           }, 1000);
          console.log(v);
          var tempVar = [];
          tempVar = fecha.concat(v);
           chart.load({
             columns : tempVar,

             unload: true,
           });

         }
         //Si/No
         if(data[0].cons_tipopregunta == 3){

           fecha = ['x'];
           total = ['Si'];
           total1 = ['No'];

           var s = 0;
           var n = 0;
           for(var i=0; i < data.length; i++){

             d = data[i].fecha.split(" ");

             if(i != 0 && dia != d[0]){
               fecha.push(dia);
               total.push(s);
               total1.push(n);

               s = 0;
               n = 0;
               dia = '';

               dia = d[0];
             }


             if(dia == d[0]){
               //console.log(dia);
               if(data[i].opcion == 'Si'){

                 s++;
               }
               if(data[i].opcion == 'No'){

                 n++;
               }
             }

            if(i == data.length-1){
              fecha.push(dia);
              total.push(s);
              total1.push(n);
            }

             if(i == 0){

               if(data[i].opcion == 'Si'){

                 s++;
               }
               if(data[i].opcion == 'No'){

                 n++;
               }
             }

             dia = d[0];

           }

           setTimeout(function(){
             $(".cssload-container").css("opacity","0");
           }, 500);
           setTimeout(function(){
             $("#chart").css("opacity","1");
           }, 1000);

            chart.load({
              columns: [
                fecha,
                total,
                total1
              ],
              unload: true,
              colors: {
                  'Si': '#2FAA07',
                  'No': '#FF2727'
              },
            });

         }
       }else{

         fecha = ['x'];
         total = ['Satisfaccion General'];
         for(var i=0; i < data.length; i++){
           d = data[i].fecha.split(" ");

           if(dia == d[0]){
             pn = pn + parseInt(data[i].ponderacion);
             l++;
           }
           if(i != 0 && dia != d[0] || i == data.length-1){
             l++;
             pn = pn / l;
             pn = Math.round(pn);
             total.push(pn);
             fecha.push(dia);
             pn = 0;
             dia = '';
             l = 0;
           }

           if(i == 0){
             pn = parseInt(data[i].ponderacion);
           }
           dia = d[0];
         }

         setTimeout(function(){
           $(".cssload-container").css("opacity","0");
         }, 500);
         setTimeout(function(){
           $("#chart").css("opacity","1");
         }, 1000);

         chart.load({
           columns: [
             fecha,
             total
           ],
           unload: true,
         });

       }
       //fn();

       }else{
        setTimeout(function(){
          $(".cssload-container").css("opacity","0");

        }, 500);
        alert('No se encontro informacion');
      }

      $.ajax({
         url:'http://oghen.com/admin/getInfo.php?tipo=datosComentarios&idencuesta='+enc+'&idpregunta='+preg+'&idempleado='+emp+'&fechainicio='+fi+'&fechafinal='+ff,
         contentType: "application/json",
         type:'POST',
         dataType: "json",
         data:{
         },
         success:function(datos){
           console.log(datos);
          //  setTimeout
            setTimeout(function(){
              $('#encuestasTotales').animateNumber({ number: Object.keys(datos.datos).length });
              setTimeout(function(){
                $('#respuestasTotales').animateNumber({ number: (datos.generales.preguntas*Object.keys(datos.datos).length) });
                setTimeout(function(){
                  $('#buenosTotales').animateNumber({ number: datos.generales.buenos });
                  setTimeout(function(){
                    $('#malosTotales').animateNumber({ number: datos.generales.malos });
                  }, 500);
                }, 500);
              }, 500);
            }, 500);

           $("#comment").html("");
           var cadena = "";
           $.each(datos.datos, function(index, value){

             cadena = '<li class="comentariosBloque '+((value.calificacion > 2)? "malos":"buenos")+'" style="margin-bottom:5px;">';
             cadena += '<a href="javascript:;" onclick="getEncuesta('+value.id+','+value.idencuesta+')" style="padding: 0px;">';
             cadena += '<p style=" margin-left: 5px; overflow: auto;" class="';
             if(value.calificacion == 1) cadena += 'bg-success-darkerComentario';
             if(value.calificacion == 2) cadena += 'bg-success-lightComentario';
             if(value.calificacion == 3) cadena += 'bg-warning-darkerComentario';
             if(value.calificacion == 4) cadena += 'bg-danger-lightComentario';
             if(value.calificacion == 5) cadena += 'bg-warning-darkerComentario';
             cadena += ' notification-message">';
             cadena += '<span class="row"><span class="col-md-2" style="text-align:center; font-size:17px;">';
             cadena += designCode(value.calificacion, "iconos")+'</span><span class="col-md-10">';
             cadena += '<strong>'+value.nombreLocal+'</strong><span style="display:block; float:right;">'+value.fecha+'</span>';
             cadena += '<span style="color:#5d5d5d; display:block;  border-top:thin solid rgba(255, 255, 255, 0.65);">';
             if(value.comentario == "") cadena += 'Sin comentario';
             else cadena += value.comentario;
             cadena += '</span></span>';
             cadena += '<span class="row"><span class="col-md-3"><span class="fa fa-users" style="margin-right:10px;">'+value.nombre+'</span></span><span class="col-md-6">';
             cadena += '<span class="fa fa-envelope" style="margin-right:10px;"></span>'+value.email;
             cadena += '</span><span class="col-md-3"><span class="fa fa-phone" style="margin-right:10px;"></span>'+value.telefono;
             cadena += '</span></span>';
             $("#comment").append(cadena);
           });
         },
         error:function(w,t,f){
           console.log('http://oghen.com/admin/getInfo.php?tipo=datosComentarios&idencuesta='+enc+'&idpregunta='+preg+'&idempleado='+emp+'&fechainicio='+fi+'&fechafinal='+ff);
         }
      });


      // Ajax for the NPS Calculation

      $.ajax({
         url:'http://oghen.com/admin/getInfo.php?tipo=nps&idencuesta='+enc+'&idpregunta='+preg+'&idempleado='+emp+'&fechainicio='+fi+'&fechafinal='+ff,
         contentType: "application/json",
         type:'POST',
         dataType: "json",
         data:{
         },
         success:function(datos){
          console.log(datos);
          $("#npsPercent").html(datos.nps+"%");
          var data = [{
            label: 'Promoters '+datos.promoters,
            data: datos.promoters,
            color: "#75C468"
          }, {
            label: 'Passive '+datos.passives,
            data: datos.passives,
            color: "#FED734"
          }, {
            label: 'Detractors '+datos.detractors,
            data: datos.detractors,
            color: "#E26C5E"
          }];
          $.plot($('.pie-chart'), data, {
            series: {
              pie: {
                show: true,
                //innerRadius: 0.6,
                stroke: {
                  width: 0
                },
                label: {
                  show: false,

                }
              }
            },
            legend: {
              show: true,
            }
          });
         },
         error:function(w,t,f){

         }
      });


    },
    error:function(w,t,f){
    }
 });

 }, 2000);
});

(function ($) {
  'use strict';
  chart = c3.generate({
      padding: {
        right: 20
      },
      data: {
          x: 'x',
          xFormat: '%Y-%m-%d',
          columns: [
              fecha,
              total,
          ]
      },
      axis: {
          x: {
              type: 'timeseries',
              localtime: false,
              tick: {
                  format: '%Y-%m-%d'
              }
          }
      },
      point: {
          r: 3
      },
  });
})(jQuery);
