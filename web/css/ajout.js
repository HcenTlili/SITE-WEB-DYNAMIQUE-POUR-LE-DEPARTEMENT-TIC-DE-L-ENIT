$(document).ready(function() {
    $('.slideshow').cycle({
	    fx:     'shuffle', 
	    speed:  'fast', 
	    timeout: 0, 
	    next:   '#next2', 
	    prev:   '#prev2' 
	});
});