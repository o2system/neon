var imgCount = 0;
var wrapThumb = $('.prod-add-item-list-thumb');


function uploadProductMain(obj) {
    if (obj.files && obj.files[0]) {
        let reader = new FileReader();

        for (let i = 0; i < 1; i++) {
            var thumbImg = $('<div></div>').addClass('prod-item-thumb');
           
            for (let i = 0; i < 1; i++) { 
                var img = "<img id=\"imgProdThumb"+imgCount+"\" class=\"thumb\" src=\"\" />";
                var removeBtn = $('<a></a>').addClass('btn btn-light removeProd').append("<i class=\"far fa-trash-alt\"></i>");
                thumbImg.append(img, removeBtn);
            }
            wrapThumb.append(thumbImg); 
        }
        reader.onload = function(e) {
            let wrap = $('.prod-item-thumb').find('#imgProdThumb'+imgCount);
            wrap.attr('src', e.target.result);
            console.log("wrap", wrap);
        }
        reader.readAsDataURL(obj.files[0]);
    }
}

$('.prod-add-input-item').each(function () {
    let btnUpload = $(this).find('input[type=file]');
    btnUpload.on('change', function () {
        imgCount++;
        uploadProductMain(this);
        $(this).parent().addClass('hide-cs');
    })
})

$('.prod-add-item-list-thumb').each(function() {
   var btnRmv = $(this).find('.removeProd');
    $(this).on('click', '.removeProd', function () {
        $(this).parent().remove();
        var inputProd = $('.prod-add-item-list .hide-cs');

        for (let i = inputProd.length; i >= 0; i--) {
            inputProd.last(i).removeClass('hide-cs');
        }
    })
})

var imgSingleCount = 0;
function uploadProductImg(obj) {
    if (obj.files && obj.files[0]) {
        let reader = new FileReader();

        reader.onload = function(e) {
            let wrap = $('.input-upload-cs-circle').find('img');
            wrap.attr('src', e.target.result);
            console.log("wrap", wrap);
        }

        reader.readAsDataURL(obj.files[0]);
        console.log("reader",reader);
        $('.upload-cs-wrap').css('background-color', '#fff');
    }
}

$(document).on('change', '.uploadProdBtn input', function() {
    uploadProductImg(this);
})