function toPersianNum( num, dontTrim ) {

    var i = 0,

        dontTrim = dontTrim || false,

        num = dontTrim ? num.toString() : num.toString().trim(),
        len = num.length,

        res = '',
        pos,

        persianNumbers = typeof persianNumber == 'undefined' ?
            ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'] :
            persianNumbers;

    for (; i < len; i++)
        if (( pos = persianNumbers[num.charAt(i)] ))
            res += pos;
        else
            res += num.charAt(i);

    return res;
}

// function toEnglishNum( num, dontTrim ) {
function toEnglishNum( num, str = false ) {
    var i = 0,
        j = 0,
        // dontTrim = dontTrim || false,
        dontTrim = false,
        num = dontTrim ? num.toString() : num.toString().trim(),
        len = num.length,
        res = '',
        pos,
        persianNumbers = typeof persianNumber == 'undefined' ?
            [ '۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹' ] :
            persianNumbers;

    for ( ; i < len; i++ )
    {
        if ( ~( pos = persianNumbers.indexOf( num.charAt( i ) ) ) )
            {res += pos;}
        else
            {res += num.charAt( i );}
    }
    
    
    if(str)
    {
        return res;
    }
    else
    {
        return parseInt(res);
    }
};

function isInt(value, unsigned = true)
{
    if(Math.floor(value) == value && $.isNumeric(value)) 
    {
        if(unsigned) 
        {
            if(value>=0) return true;
            else return false;
        }
        return true;
    }
    return false;
}