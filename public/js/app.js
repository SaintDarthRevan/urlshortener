$(function() {
    $('#copyHashBtn').click(function() {
        $('#shortUrl')[0].select();
        document.execCommand('copy');
        $('#shortUrl').append(' ');
        $('#shortUrl').val().slice(0, -1);
    });
});