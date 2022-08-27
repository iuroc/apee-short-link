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
        key_val = key_val.replaceAll(i, '/' + btoa(i + '_apee'))
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
    try {
        new URL(url)
    } catch {
        $('#input-url').addClass('is-invalid').focus()
        return
    }
    if (password.search(/^\w{0,20}$/) == -1) {
        $('#input-password').addClass('is-invalid').focus()
        return
    } else if (desc.length > 200) {
        $('#input-desc').addClass('is-invalid').focus()
        return
    } else if (guoqi < 0 || guoqi > 10000) {
        $('#input-day').addClass('is-invalid').focus()
        return
    } else if (end && end.search(/^\w{6,20}$/) == -1) {
        console.log(password.search(/^\w{0,20}$/))
        $('#input-end').addClass('is-invalid').focus()
        return
    }
    $('.page-home .btns .createWork').attr('disabled', 'disabled').html('正在生成中...')
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
        f: key_val,
        g: end
    }, function (data) {
        $('.page-home .btns .createWork').removeAttr('disabled', 'disabled').html('生成链接')
        if (data.code == 200) {
            clearForm()
            Poncon.load.result = true
            location.hash = '/result'
            var html = makeHtml(data)
            $('.page-result .line-list').html(html)
            return
        }
        alert(data.msg)
    })
}
function makeHtml(data) {
    var keyNames = ['短链接', '分享链接', '原链接', '密码', '有效期', '描述']
    var values = [
        `<a class="short-url" href="${location.origin}/${data.data.end}${data.data.password ? `/${data.data.password}` : ''}" target="_blank">${location.origin}/${data.data.end}${data.data.password ? `/${data.data.password}` : ''}</a>`,
        `<a href="#/share/${data.data.end}" target="_blank" class="text-danger">${location.origin}/#/share/${data.data.end}</a>`,
        `${data.data.url}`,
        `${data.data.password}`,
        `${data.data.guoqi == 0 ? '永久有效' : '剩余 ' + data.data.guoqi + ' 天'}`,
        `${$(data.data.desc).text()}`
    ]
    for (var i = 0, html = ''; i < keyNames.length; i++) {
        if (values[i]) {
            html += `<div class="mb-2 text-truncate"><b>${keyNames[i]}：</b>${values[i]}</div>`
        }
    }
    return html
}
function clearForm() {
    $('.page-home input').val('').removeClass('is-invalid')
}
function share_load(end, password, order) {
    $.get('api/go_url.php', {
        end: end,
        type: 'json',
        password: password
    }, function (data) {
        if (data.code == 200) {
            var html = makeHtml(data)
            $('.page-share .show-password').show()
            $('.page-share .show-nopassword').hide()
            $('.page-share .line-list').html(html)
            return
        } else if (data.code == 901) {
            if (order == 'click') {
                $('.page-share .show-nopassword .input-group').addClass('is-invalid')
            } else {
                $('.page-share .show-nopassword').show()
                $('.page-share .show-password').hide()
            }
            return
        }
        location.hash = ''
    })
}
function share_submitPassword() {
    var password = $('.page-share .input-password').val()
    var end = location.hash.split('/')[2]
    history.replaceState({}, null, '#/share/' + end + '/' + password)
    share_load(end, password, 'click')
}
var Poncon = {
    data: {},
    load: {},
}
history.scrollRestoration = 'manual'
$(document).ready(function () {
    new ClipboardJS('.copybtn')
    router(location.hash)
    function router(hash) {
        hash = hash.split('/')
        var target = hash[1]
        // target非法状态
        if (!target || !target.match(/^\w+$/)) {
            target = 'home'
        }
        $('.page-oyp').css('display', 'none')
        var Page = $('.page-' + target)
        Page.css('display', 'block')
        // 控制侧边选项卡阴影
        // $('.oyp-action, .oyp-action-sm').removeClass('oyp-active')
        // $('.tab-' + target).addClass('oyp-active')
        if (target == 'home') {
            history.replaceState({}, null, './')
        } else if (target == 'result') {
            if (!Poncon.load.result) {
                location.hash = ''
            }
        } else if (target == 'share') {
            var end = hash[2]
            var password = hash[3]
            share_load(end, password)
        } else {
            location.hash = ''
        }
    }
    document.body.ondragstart = () => { return false }
    window.addEventListener('hashchange', function (event) {
        var hash = new URL(event.newURL).hash
        router(hash)
    })
    $('input').bind('keyup', function () {
        $(this).removeClass('is-invalid')
        $(this).parent().removeClass('is-invalid')
    })
})