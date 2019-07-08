
$("#selectConsultores").bootstrapDualListbox({
      filterTextClear: '',
      filterPlaceHolder: 'filtrar',
      moveSelectedLabel: '',
      moveAllLabel: '',
      removeSelectedLabel: '',
      removeAllLabel: '',
      infoText: '',
});
$(function () {
    var dualListContainer = $('#selectConsultores').bootstrapDualListbox('getContainer');
    dualListContainer.find('.moveall i').removeClass().addClass('fa fa-arrow-right');
    dualListContainer.find('.removeall i').removeClass().addClass('fa fa-arrow-left');
    dualListContainer.find('.move i').removeClass().addClass('fa fa-arrow-right');
    dualListContainer.find('.remove i').removeClass().addClass('fa fa-arrow-left');
});

/*
   Retorna la lista de consultores
*/
$.ajax({
    url: urlUsersConsultores,
    type: 'get',
    dataType:'json'
})
.done(function(data) {
    $("#selectConsultores").children().remove();
    $.each(data, function (key,val) {
     $("#selectConsultores").append(
        $("<option></option>",{
            value: val.co_usuario,
            text: val.no_usuario,
        }) 
        )
 });
    $("#selectConsultores").bootstrapDualListbox('refresh', true);

}).fail(function(data) {
    console.log('error'+data);
});
     
/*
   Muestra el listado 
*/
$('#btnRelatorio').click(function() {
    $('#table').empty();
    $('#showPizza').hide();
    $('#showGrafico').hide();
    const consultores = $('#selectConsultores').val();
    const fromDate = $('#startDate').val();
    const endDate =  $('#endDate').val();
    var data = {'consultores':consultores, 'fromDate':fromDate, 'endDate':endDate};
    $.ajax({
        headers:{'X-CSRF-TOKEN': token},
        url: urlRelatorio,
        data: data,
        type: 'post',
        dataType:'json'
    })
    .done(function(data) {
        var i = 0;
        if(data.status != false){
            $.each(data, function (key,value) {

                let styleValorNeto = "";
                let styleCuxtoFixo = "";
                let styleComissao = "";
                let styleLucro = "";

                let totalValorNeto = parseFloat(value.dataTotaDesempenho.totalValorNeto);
                let totalCuxtoFixo = parseFloat(value.dataTotaDesempenho.totalCuxtoFixo);
                let totalComissao = parseFloat(value.dataTotaDesempenho.totalComissao);
                let totalLucro = parseFloat(value.dataTotaDesempenho.totalLucro);

                if(totalValorNeto<0){
                    styleValorNeto = "color:red";
                }
                if(totalCuxtoFixo<0){
                    styleCuxtoFixo = "color:red";
                }
                if(totalComissao<0){
                    styleComissao = "color:red";
                }
                if(totalLucro<0){
                    styleLucro = "color:red";
                }

                $('#table').append(
                  '<table  class="table table-bordered">'
                  +'<thead>'
                    +'<tr><th colspan="5">'+value.dataUser[0].no_usuario+'</th></tr>'
                    +'<tr>'
                    +'<th scope="col">Período</th>'
                    +'<th scope="col">Receita Líquida</th>'
                    +'<th scope="col">Custo Fixo</th>'
                    +'<th scope="col">Comissão</th>'
                    +'<th scope="col">Lucro</th>'
                    +'</tr>'
                 +'</thead>'
                 +'<tbody id='+key+'></tbody>'
                 +'<tfoot>'
                    +'<tr>'
                    +'<td class="font-13"><b>Saldo</b></td>'
                    +'<td style='+styleValorNeto+'>'+'R$'+' '+value.dataTotaDesempenho.totalValorNeto+'</td>'
                    +'<td style='+styleCuxtoFixo+'>'+'R$'+' '+value.dataTotaDesempenho.totalCuxtoFixo+'</td>'
                    +'<td style='+styleComissao+'>'+'R$'+' '+value.dataTotaDesempenho.totalComissao+'</td>'
                    +'<td style='+styleLucro+'>'+'R$'+' '+value.dataTotaDesempenho.totalLucro+'</td>'
                    +'</tr>'
                 +'</tfoot>'
               +'</table>');

                $.each(value.dataDesempenho, function (key,val) {
                 let styleLucroMes = ""
                 let lucroMes = parseFloat(val.lucroMes);
                 if(lucroMes<0){
                    styleLucroMes = "color:red";
                 }
                $('#'+i).append(
                    '<tr>'
                      +'<td>'+val.mes+'</td>'
                      +'<td>'+'R$'+' '+val.totalValorNetoMes+'</td>'
                      +'<td>'+'R$'+' '+val.cuxtoFixoMes+'</td>'
                      +'<td>'+'R$'+' '+val.totalComissaoMes+'</td>'
                      +'<td style='+styleLucroMes+'>'+'R$'+' '+val.lucroMes+'</td>'
                    +'</tr>' )
            });
                i++;
            });
        }else{
            swal({
                title: "No se pudo realizar esta operación",
                text:  "Debe seleccionar el periodo y los consultores",
                type: "error"
            });
        }
    })
    .fail(function(data) {
      console.log('error'+data);
  });
});

/*calendario inical*/
 $('#startDate').datetimepicker({
     format: 'MM-YYYY',
     useCurrent: false
 });  

/*calendario final*/        
 $('#endDate').datetimepicker({
     format: 'MM-YYYY',
     useCurrent: false,
     maxDate: new Date()
 });  

/*
   Retorna un color de forma aleatoria
*/
 function getRandomColor() {
    var letters = '0123456789ABCDEF'.split('');
    var color = '#';
    for (var i = 0; i < 6; i++ ) {
        color += letters[Math.floor(Math.random() * 16)];
    }
    return color;
}

/*
   Muestra la grafica de barra
*/
$('#btnGrafico').click(function() {
    $('#table').empty();
    $('#showPizza').hide();
    $('#showGrafico').show();
    
    $('#graficoChart').remove(); 
    $('#showGrafico').append('<canvas id="graficoChart" width="400" height="150"></canvas>');

    const consultores = $('#selectConsultores').val();
    const fromDate = $('#startDate').val();
    const endDate =  $('#endDate').val();
   
    var data = {'consultores':consultores, 'fromDate':fromDate, 'endDate':endDate};

    $.ajax({
        headers:{'X-CSRF-TOKEN': token},
        url: urlGrafico,
        type: 'post',
        data:data
    }).done(function(data) {

        if(data.status != false){
       
        var labels = data.months; //etiquetas
        var dataChart = new Array(); //valores de data
        var dataCuxtoFixo = new Array(); 
        let color = getRandomColor();

        // costo medio
        $.each(data.months, function(i, item) {
            dataCuxtoFixo.push(data.cuxtoFixoMedio);
        });
        let fromMonthYear = data.months[0];
        let endMonthYear = data.months[data.months.length-1];
   
        let dataSets = {
            label: 'Cuxto medio',
            data: dataCuxtoFixo,
            type: "line",
            borderColor: color,
        };

        dataChart.push(dataSets);

        $.each(data.dataSets, function(i, item) {
            let nameConsultor = data.dataUser[i][0].no_usuario;
            let color = getRandomColor();
            let dataSets = {
                label: nameConsultor,
                data: item,
                backgroundColor: color,
                borderColor: color,
                borderWidth: 1
            };

            dataChart.push(dataSets);
        });


        var ctx = document.getElementById('graficoChart').getContext('2d');
        var chartBar = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: dataChart
            },
            options: {
                scales: {
                    xAxes: [{
                        display: true,
                        ticks: {
                            beginAtZero: true,
                            steps: 2,
                            stepValue: 2,
                            max: 32000,
                            valueFormatString: "$#,###,#0",
                        }
                    }],
                    yAxes: [{
                        display: true,
                        ticks: {
                            beginAtZero: true,
                            steps: 2,
                            stepValue: 2,
                            max: 32000,
                            valueFormatString: "$#,###,#0",
                        }
                    }]
                },
                title: {
                    display: true,
                    text: `Perfomance Comercial ${fromMonthYear} a ${endMonthYear}`
                }
            }
        });

  }else{
    swal({
        title: "No se pudo realizar esta operación",
        text:  "Debe seleccionar el periodo y los consultores",
        type: "error"
    });
   }
});

});


/*
   Muestra la grafica en formato de pizza
*/
$('#btnPizza').click(function() {
    $('#table').empty();
    $('#showPizza').show();
    $('#showGrafico').hide();

    $('#pizzaChart').remove(); 
    $('#showPizza').append('<canvas id="pizzaChart" width="400" height="150"></canvas>');

    const consultores = $('#selectConsultores').val();
    const fromDate = $('#startDate').val();
    const endDate =  $('#endDate').val();

    var data = {'consultores':consultores, 'fromDate':fromDate, 'endDate':endDate};

    $.ajax({
        headers:{'X-CSRF-TOKEN': token},
        url: urlPizza,
        type: 'post',
        data:data
    }).done(function(data) {
        if(data.status != false){

        var labels = new Array(); //etiquetas
        var colors = Array();
        var dataChart = data.dataPercentaje; //valores de data
        $.each(data.dataUser, function(i, item) {
            let nameConsultor = item[0].no_usuario;
            let color = getRandomColor();
            labels.push(nameConsultor);
            colors.push(color);
        });     

        var ctxPizza = document.getElementById("pizzaChart");

        var oilData = {
            labels: labels,
            datasets: [{
                data: dataChart,
                backgroundColor: colors
            }]
        };

        var pieChart = new Chart(ctxPizza, {
            type: 'pie',
            data: oilData,
            options: {
                 title: {
                    display: true,
                    text: `Paticipação na Receita`
                }
            }
        });
    }else{
        swal({
            title: "No se pudo realizar esta operación",
            text:  "Debe seleccionar el periodo y los consultores",
            type: "error"
        });
    }
});

});
