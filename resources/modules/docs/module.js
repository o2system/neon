$(document).ready(function(){
    $('body').addClass('bg-white');
    let sidebarDocumentationsWidth = $('.documentations-sidebar').parent('.col-md-3').width();
    $('.documentations-sidebar').width(sidebarDocumentationsWidth);

    // Generate TOC
    let listIndex = 0;
    let listPaddings = {
        h1: "pl-0",
        h2: "pl-1",
        h3: "pl-2",
        h4: "pl-3",
        h5: "pl-4",
        h6: "pl-5"
    };

    $('.documentations-content').find('h1, h2, h3, h4, h5, h6').each(function() {
        //insert an anchor to jump to, from the TOC link.               
        $(this).attr('id', 'content-' + listIndex );
        let listItem = '<li class="' + listPaddings[ $(this)[0].nodeName.toLowerCase() ] + '"><a href="#content-' + listIndex + '">' + $(this).text() + '</a></li>';
        $(listItem).appendTo(".toc-nav");
        listIndex++;
    });

    let images = document.querySelectorAll('.img-browser');

    for(let image of images) {
        let imageContainer = document.createElement('div');
        imageContainer.classList.add('browser');
        imageContainer.innerHTML = '<div class="top-toolbar"><span class="dot"></span><span class="dot"></span><span class="dot"></span></div><div class="content">' + image.outerHTML + '</div>';

        image.parentElement.insertBefore(imageContainer, image);
        image.remove();
    }
});