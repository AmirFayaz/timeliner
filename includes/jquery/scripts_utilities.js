function pageRedirect(page = null) {

    window.location.replace("http://localhost/timeliner/"+page);
    // console.log("http://localhost/timeliner/"+page);

}      

function pathToView(page = null) {

    return ("http://localhost/timeliner/application/views/"+page+".php");

}      

Number.prototype.formatMoney = function(c, d, t){
    var n = this, 
    c = isNaN(c = Math.abs(c)) ? 2 : c, 
    d = d == undefined ? "." : d, 
    t = t == undefined ? "," : t, 
    s = n < 0 ? "-" : "", 
    i = String(parseInt(n = Math.abs(Number(n) || 0).toFixed(c))), 
    j = (j = i.length) > 3 ? j % 3 : 0;
   return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
 };

 function getBaseUrl() {
    var u1 = ((window.location.pathname).split('/'))[1];
    var u0 = window.location.origin;
    return  u0+"/"+u1+"/";
 }

 function swal_error()
{
    swal({
        title: "بروز خطا",
        text: "مشکلی در سیستم به وجود آمده !",
        icon: "error",
        buttons: "خـب!",
        dangerMode: true,
    });

}

function swal_success(action = null)
{
    swal({
        title: "ثبت با موفقیت انجام شد !",
        type: "success",
        buttons: "خـب!",
        dangerMode: false,
    }).then(result =>
        {
            switch(action)
            {
                case 'reload' :
                case 'refresh' :
                    location.reload();
                    break;
                case null:
                case '':
                    break;
                default:
                    pageRedirect(action);
                    break;
            }
        });

}
