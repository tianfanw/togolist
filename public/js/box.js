$(document).ready(function(){
	
	$('.log-box').click(function(){

		$('.box').animate({'opacity':'1.00'}, 300);
		$('.backdrop').animate({'opacity':'.60'}, 300);
		$('.backdrop, .box').css('display', 'block');
		var _scrollHeight = $(document).scrollTop(),
            _windowHeight = $(window).height(),
            _windowWidth = $(window).width(),
            _popupHeight = $('.box').height(),
            _popupWeight = $('.box').width();
            _posiTop = (_windowHeight - _popupHeight)/2 + _scrollHeight;  
            _posiLeft = (_windowWidth - _popupWeight)/2;  
        $('.box').css({'left': _posiLeft + 'px','top':_posiTop + 'px'});
	});
	
	$('.user_name').click(function(){

		$('.user_meun').animate({'opacity':'1.00'}, 300);
		$('.backdrop').animate({'opacity':'.60'}, 300);
		$('.backdrop, .user_meun').css('display', 'block');
		var _scrollHeight = $(document).scrollTop(),
            _windowHeight = $(window).height(),
            _windowWidth = $(window).width(),
            _popupHeight = $('.user_meun').height(),
            _popupWeight = $('.user_meun').width();
            _posiTop = (_windowHeight - _popupHeight)/2 + _scrollHeight;  
            _posiLeft = (_windowWidth - _popupWeight)/2;  
        $('.user_meun').css({'left': _posiLeft + 'px','top':_posiTop + 'px'});
	});
	
	$('.new_post').click(function(){

		$('.new_post_box').animate({'opacity':'1.00'}, 300);
		$('.backdrop').animate({'opacity':'.60'}, 300);
		$('.backdrop, .new_post_box').css('display', 'block');
		var _scrollHeight = $(document).scrollTop(),
            _windowHeight = $(window).height(),
            _windowWidth = $(window).width(),
            _popupHeight = $('.new_post_box').height(),
            _popupWeight = $('.new_post_box').width();
            _posiTop = (_windowHeight - _popupHeight)/2 + _scrollHeight;  
            _posiLeft = (_windowWidth - _popupWeight)/2;  
        $('.new_post_box').css({'left': _posiLeft + 'px','top':_posiTop + 'px'});
	});
	
	
	$('.backdrop').click(function(){
		
		$('.backdrop, .box, .cage, .new_post_box, .user_meun').animate({'opacity':'0'}, 300, function(){
			$('.backdrop, .box, .cage, .new_post_box, .user_meun').css('display', 'none');
			$('body').css({overflow:'auto'});
		});
		
	});
		
});


$(window).load(function(){
	$('.grid img').mouseover(function(){
			$(this).css({
			'cursor':'pointer',
		});
	});
	$('.grid img').mouseout(function(){
		    $(this).css({
			'cursor':'default'
		});
	});
	$('.grid img').click(function(){
	    
		$('.backdrop').animate({'opacity':'.80'}, 300);
		$('.cage').animate({'opacity':'1.00'}, 300);
		$('.backdrop, .cage').css('display', 'block');

		var imageUrl = $(this).attr('src');
		$('#mainImage').attr('src', imageUrl);
		
		var _scrollHeight = $(document).scrollTop(),
            _windowHeight = $(window).height(),
            _windowWidth = $(window).width(),
            _popupHeight = $('#mainImage').height(),
            _popupWeight = $('#mainImage').width();
            _posiTop = (_windowHeight - _popupHeight)/2 + _scrollHeight;  
            _posiLeft = (_windowWidth - _popupWeight)/2;  
        $('.cage').css({'left': _posiLeft + 'px','top':_posiTop + 'px'});
        $('.backdrop').css({'top':'_scrollHeight'});
		
	});	
	
	
});