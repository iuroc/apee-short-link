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
    return key_val
}

$(document).ready(function () {
    
})