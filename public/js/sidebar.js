$( document ).ready(function() {
    $('.left-sidebar ul li[class!="active"]').hover(function() {
        //$(this).prepend('<div class="arrow-right arrow-gray"></div>');
        $(this).find("a").css({
            'background-color': '#ccc',
            'margin-right': '10px',
            'color': '#001569'
        });
    }, function() {
        //$(this).find('div.arrow-right').remove();
        $(this).find("a").css({
            'background-color': 'transparent',
            'margin-right': '0px',
            'color': '#fff'
        });
    });

    $('.right-sidebar ul li[class!="active"]').hover(function() {
        //$(this).prepend('<div class="arrow-left arrow-gray"></div>');
        $(this).find("a").css({
            'background-color': '#ccc',
            'margin-left': '10px',
            'color': '#001569'
        });
    }, function() {
        //$(this).find('div.arrow-left').remove();
        $(this).find("a").css({
            'background-color': 'transparent',
            'margin-left': '0px',
            'color': '#fff'
        });
    });
});