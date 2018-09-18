function ajax_handler(_action , _params)
{
    var _url = getBaseUrl()+"ajax";

    var result = [];

    var request = $.ajax({
        method: "POST",
        url: _url,
        data: { 
            action: _action ,
            params: _params
            },
        success: function(data)
        {
            result['data'] =  data;
            result['error'] =  false;
            console.log("AJAX : "+_action+" : success");
        },
        error: function(data) 
        {
            result['data'] =  data;
            result['error'] =  true;
            console.log("AJAX : "+_action+" : error!");
        }, 
        async: false,
    });
    return result;
}

function form_submit_btn(element)
{
    var id = element.id;
    var userid = $(element).data('userid');//['name'];
    var id_sp = id.split('_');

    func = id_sp[0]+'_'+id_sp[1];
    act = id_sp[2];

    // console.log(func)
    // console.log(act)


    if(act =='edit')
    {
        $('div[id=div_verifyStocks_stockCountEdit][data-userid='+userid+']').toggleClass('hidden');
        $('div[id=div_verifyStocks_stockCountEdit][data-userid='+userid+'] input').toggleClass('is_selected');
    }
    else
    {
        window[func](act,userid);
    }
}


var checkPhone = 1; 
var checkName = 1; 

$(document).ready(function() {

    console.log(getBaseUrl());

    $('form').submit(function(event) 
    {
        event.preventDefault();
        var action = event['target']['id']; 
        console.log(action);
        window[action](event['target']);
    });

    $('.delete').click(function()
    {
        var obj = $(this).data('delete-obj');
        var id = $(this).data('delete-id');

        var params = {
            obj: obj , 
            id: id , 
        };
        console.log(params);
        var action = 'delete_object';
        result = ajax_handler(action , params);
        console.log(result);
    });

    // console.log(Date);
    // console.log(Date.UTC(2010, 0, 1));

    $(function () {

        var unit = 'واحد';
        var captionName = 'پارامتر قابل اندازه گیری';
        var projectName = 'نام پروژه';
        var data1 = [
            [Date.UTC(2010, 0, 1), 29.9],
            [Date.UTC(2010, 2, 1), 91.5],
            [Date.UTC(2010, 3, 1), 106.4]
        ];
        var data2 = [
            [Date.UTC(2010, 0, 2), 28.9],
            [Date.UTC(2010, 1, 24), 72.5],
            [Date.UTC(2010, 3, 1), 100.4]
        ];
        var data1Name = 'پیش بینی';
        var data2Name = 'عملکرد';

		Highcharts.setOptions({
			chart: {
				style: {
					fontSize: '12px',
                    textAlign:'right'
				}
			}
        });
        
		$('#myChart').highcharts({
			credits: {
				enabled: false
			},				
			title: {
				text: captionName,
				x: -20,
				style: {
					fontWeight: 'bold'
				}
			},				
			subtitle: {
				text: projectName,
				x: -20			
            },
            
            xAxis: {
                labels: {
                    formatter: function () {
                        var label = this.axis.defaultLabelFormatter.call(this);
        
                        // Use thousands separator for four-digit numbers too
                        // if (/^[0-9]{4}$/.test(label)) {
                        //     return Highcharts.numberFormat(this.value, 0);
                        // }
                        return this.value;
                    }
                }
            },
            yAxis: {
                title: {
                    text: unit
                },
            },
            series: [{
                name: data1Name,
                color: '#24305E',
                data: data1,
            },{
                name: data2Name,
                color: '#E05038',
                data: data2,
            }],
			legend: {
				rtl: true
            },
			tooltip: {
                // valueSuffix: unit,
                // formatter: function () {
                //     var s = '<span class="mb-1 p-1 text-small text-bold">'+this.x/1000000+'</span>';

                //     $.each(this.points, function () {
                //         s += '<br/><span class="p-1 mb-2 text-normal text-bold" >'
                //             + this.series.name + ': '
                //             + this.y  + unit
                //             + '</span>';
                //     });
        
                //     return s;
                // },
        
                // crosshairs: {
                //     width: 1,
                // },
                // shared: true,
                // useHTML: true,
                // backgroundColor: '#FFFFFF',
                // borderWidth: 0,
                // borderRadius: 10,
                // borderColor: '#AAA'
                shared: true,
                useHTML: true,
                headerFormat: '<small>{point.key}</small><table style="direction: rtl">',
                pointFormat: '<tr style="color: {series.color}"><td class="text-small text-bold" >{series.name}: </td>' +
                    '<td class="text-bold" style="text-align: left">{point.y} '+unit+'</td></tr>',
                footerFormat: '</table>',
                rtl: true
        
			},
		});
	});
})

