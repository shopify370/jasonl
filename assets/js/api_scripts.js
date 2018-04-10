$(document).ready(function(e) {

    $(document).on('click', '.load-form', function() {
        $.ajax({
            url: $(this).attr('href'),
            cache: true,
//            data: $(this).serialize(),
            type: 'GET',
//            dataType: 'html',
            beforeSend: function(xhr) {
//							$('#api_res').html('<div style="display:table-cell; vertical-align:middle; text-align:center;">Loading... <img src="assets/img/ajax-loader.gif" /></div>');

            },
            success: function(data) {
                $('#form-view').html(data);
                var _form = $('#form-view form'),
                        _html = "",
                        _link = _form.attr('action'),
                        _request_type = _form.attr('method') ?_form.attr('method'):"GET";
                
                if(typeof _link == "undefined")
                    _link = this.url;
                
                _html = "URL: <a id='request-url' href='" + _link + "' target='_blank' >" + _link + "</a>"//<button onclick='copy(\"request-url\");' >copy</button>";
                        +"<br>"
                        +"REQUEST TYPE: "+_request_type;
                $('#request').html(_html);
                $('#results').html("");
                $('<input>').attr('type','hidden').attr('name','_app_').appendTo(_form);
//                _form.append();
            }
        });
        return false;
    })
    $(document).on('submit', 'form', function() {
        $(this).ajaxSubmit(
                {
            url: $(this).attr('action'),
            cache: false,
            type: $(this).attr('method'),
            dataType: 'json',
            beforeSend: function(xhr) {
                setHeader(xhr)
                $('#results').html('<div style="display:table-cell; vertical-align:middle; text-align:center;"><img src="'+base_url+'assets/img/ajax-loader.gif" /></div>');

            },
            success: function(data) {
                $('#results').html(JSON.stringify(data, null, 4));
                _resize_response_view();
            }
        }
        );
//        $.ajax({
//            url: $(this).attr('action'),
//            cache: false,
//            data: $(this).serialize(),
//            type: 'POST',
//            dataType: 'json',
//            beforeSend: function(xhr) {
//                $('#results').html('<div style="display:table-cell; vertical-align:middle; text-align:center;"><img src="'+base_url+'assets/img/ajax-loader.gif" /></div>');
//
//            },
//            success: function(data) {
//                $('#results').html(JSON.stringify(data, null, 4));
//                _resize_response_view();
//            }
//        });
        return false;
    });
    
    $(window).resize(function(){
        _resize_main_nav();
        _resize_response_view();
    })
_resize_main_nav()
});

function _resize_main_nav()
{
        $('#sidebar .mainNav').height($('#sidebar').height()-68);
    
}

function _resize_response_view()
{
    if($.trim($('#results').text()).length) {
        $('#results').css({"max-height":$(window).height()-$("#results").offset().top-25});
    }
    
}
function setHeader(xhr) {
    var  _authentication_key = $("[name='authentication_key']").val();

    if (typeof _authentication_key !== 'undefined')
        xhr.setRequestHeader('authentication_key', _authentication_key);

    
      }

function copy(_id)
{
    _id.innerText = copytext.innerText;
    Copied = holdtext.createTextRange();
    Copied.execCommand("Copy");

}