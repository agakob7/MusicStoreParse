$(document).ready(function () {
    $("form#getdata").submit(function () {
        // Get the submit button element
        var btn = $(this).find("input[type=submit]:focus");
        postData(btn.attr('name'));
        return false;
    });
    $("#files_list").empty()

});


function updateButtons(remains, count) {

    $("input[name=retry]").hide();
    $("#products_cnt").hide();

    if (remains == null)
        return;

    if (remains == 0) {

        $("input[name=retry]").hide()
        $("#csv").show();
        $("#products_cnt").text("Produkty: " + count).show();
    }
    else {

        $("input[name=retry]").attr('value', 'Pobierz brakujące produkty:' + remains).show();
        $("#products_cnt").hide();
        $("#csv").hide();

    }

}

function showFiles() {



    if (!$("select[name=driver]").val()) {
        return alert("Podaj strone");
    }

    $("#files_list").empty();
    var rows, limit, parser_limit = 0;
    var pages = 0;

    limit = $("input[name=record]").val();

    rows = Cookie.Read('records', 0);
    parser_limit = Cookie.Read('result_limit', 0);

    if (rows <= 0)
        return;

    if (limit <= 0)
        limit = rows;

    if (parser_limit > 0)
        rows = parser_limit;

    if (limit > 0)
        pages = Math.ceil(rows / limit);


    for (i = 0; i < pages; i++) {

        var data = $("form").serializeArray();

        data.push({name: 'offset', value: i * limit});

        var button = $("<a target='_blank'>").addClass('btn btn-primary').append($("<span>").addClass("glyphicon glyphicon-download")).append('&nbsp;' + (i + 1));

        var file_html = $(button).attr("href", 'download.php?' + $.param(data));

        $("#files_list").append(file_html).append('&nbsp;').show();

    }

}

function postData(action) {


    $("#files_list").empty();
    $("#csv").hide();
    $("#products").hide();

    updateButtons(null);

    $.ajaxSetup({
        beforeSend: function () {
            $("#loading").removeClass("hidden");
        },
        complete: function () {
            $("#loading").addClass("hidden");
        }
    });

    var frm = $("form").serializeArray();

    frm.push({name: "action", value: action});


    $.post("ajax.php", frm, function (data) {

        if (data.error)
            alert(data.error);
        else {

            if (data.remains)
                updateButtons(data.remains, data.count);
            if (data.result_limit) {
                Cookie.Create('result_limit', data.result_limit);
            }
            if (data.remains <= 0 && data.count) {
                updateButtons(data.remains, data.count);
                Cookie.Create('records', data.count);
            }

        }

    }, "json");

    return true;
}

var Cookie = {

    Create: function (name, value, days) {

        var expires = "";

        if (days) {
            var date = new Date();
            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
            expires = "; expires=" + date.toGMTString();
        }

        document.cookie = name + "=" + value + expires + "; path=/";
    },

    Read: function (name, defvalue) {

        var nameEQ = name + "=";
        var ca = document.cookie.split(";");

        for (var i = 0; i < ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0) == " ") c = c.substring(1, c.length);
            if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
        }

        return defvalue;
    },

    Erase: function (name) {

        Cookie.create(name, "", -1);
    }

};