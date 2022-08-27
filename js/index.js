function encodeStr(array) {
    var key_val = ''
    array.forEach(i => {
        key_val += btoa(encodeURIComponent(i))
    })
    ks = []
    ks[0] = 'Y'
    ks[1] = 'M'
    ks[2] = '='
    ks[3] = 'A'
    ks[4] = 'N'
    ks.forEach(i => {
        key_val = key_val.replaceAll(i, '/' + btoa('apee_' + i))
    })
    return btoa(encodeURIComponent(key_val))
}

function createWork() {
    var url = $('#input-url').val()
    var password = $('#input-password').val()
    var desc = $('#input-desc').val()
    var guoqi = $('#input-day').val() || 0
    var end = $('#input-end').val()
    // 校验参数
    if (url.search(/^https?:\/\/(\w|\.|-)+:?\d*?(\/.*)?$/) == -1 && !url.length < 2084) {
        $('#input-url').addClass('is-invalid').focus()
        return
    } else if (password.search(/^\w{0,20}$/) == -1) {
        $('#input-password').addClass('is-invalid').focus()
        return
    } else if (desc.length > 200) {
        $('#input-desc').addClass('is-invalid').focus()
        return
    } else if (guoqi < 0 || guoqi > 365) {
        $('#input-day').addClass('is-invalid').focus()
        return
    } else if (end && end.search(/^\w{6,20}$/) == -1) {
        console.log(password.search(/^\w{0,20}$/))
        $('#input-end').addClass('is-invalid').focus()
        return
    }
    var array = []
    array.push(url)
    array.push(password)
    array.push(guoqi)
    var d = new Date().getTime()
    array.push(d)
    var key_val = encodeStr(array)
    $.post('api/add_url.php', {
        a: url,
        b: password,
        c: desc,
        d: guoqi,
        e: d,
        f: key_val
    })
}

$(document).ready(function () {
    $('input').bind('keyup', function () {
        $(this).removeClass('is-invalid')
    })
})