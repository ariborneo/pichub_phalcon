$(function(){

    var ul = $('#upload ul');

    $('#drop a').click(function(){
        $(this).parent().find('input').click();
    });

    $('#upload').fileupload({

        dropZone: $('#drop'),

        add: function (e, data) {

            var error;
            var size = data.files[0].size;
            var type = data.files[0].type;

            if(!check_type(type)) error = "Wrong type";
            else if(!check_size(size)) error = "Wrong size";

            if(error)
            {
                alert(error);
            }
            else
            {
                var tpl = $('<li class="working"><input type="text" value="0" data-width="48" data-height="48"'+
                    ' data-fgColor="#0788a5" data-readOnly="1" data-bgColor="#3e4043" /><p></p><span></span></li>');

                // Append the file name and file size
                tpl.find('p').text(data.files[0].name).append('<i>' + formatFileSize(data.files[0].size) + '</i>');

                // Add the HTML to the UL element
                data.context = tpl.appendTo(ul);

                // Initialize the knob plugin
                tpl.find('input').knob();

                // Listen for clicks on the cancel icon
                tpl.find('span').click(function(){
                    if(tpl.hasClass('working')){
                        jqXHR.abort();
                    }
                    tpl.fadeOut(function(){
                        tpl.remove();
                    });
                });

                // Automatically upload the file once it is added to the queue
                var jqXHR = data.submit();
            }
        },

        progress: function(e, data){

            // Calculate the completion percentage of the upload
            var progress = parseInt(data.loaded / data.total * 100, 10);

            // Update the hidden input field and trigger a change
            // so that the jQuery knob plugin knows to update the dial
            data.context.find('input').val(progress).change();

            if(progress == 100){
                data.context.removeClass('working');
                //alert("uploaded");
            }
        },

        fail:function(e, data){
            data.context.addClass('error');
        },

        done: function(e, data){
            console.log(data.result);
            var obj = JSON.parse(data.result);
            if(obj.status == "success")
            {
                data.context.find("span").css("right", "15px");
                data.context.find("div canvas").replaceWith('<img src="'+obj.c_path+'" width="48px">');
                data.context.find("p").css("width", "190px").css("left", "75px").html('<a href="/show/'+obj.code+'" target="_blank">'+obj.code+'</a><i>'+formatFileSize(obj.filesize)+'</i>');
            }
        }

    });

    $(document).on('drop dragover', function (e) {
        e.preventDefault();
    });

    function formatFileSize(bytes) {
        if (typeof bytes !== 'number') {
            return '';
        }
        if (bytes >= 1000000000) {
            return (bytes / 1000000000).toFixed(2) + ' GB';
        }
        if (bytes >= 1000000) {
            return (bytes / 1000000).toFixed(2) + ' MB';
        }
        return (bytes / 1000).toFixed(2) + ' KB';
    }

    function check_type($type)
    {
        var accepted = Array("image/jpeg", "image/png", "image/gif");
        if(accepted.indexOf($type) != -1)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    function check_size($size)
    {
        if($size / 1000000 <= 10)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

});