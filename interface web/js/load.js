$(document).ready(function() {
	var timePerPixel = 0.0000101725;
	var time = 0;
	var pixelHeight = 0;
	var pixelWidth = 0;
	var pixels = 0;
	var cpt = 0;
	$("#button").on("click", load);
	function showLoad(){
		$("#show").text("Loading :"+parseInt(cpt/pixels*100)+"%");
		if(parseInt(cpt/pixels) == 1){
			clearInterval(interval);
		}
		cpt+=1100;
	}
	
	function load(){
		pixelHeight = $("#height").val();
		pixelWidth = $("#width").val();	
		pixels = pixelHeight * pixelWidth;
		time = pixels * timePerPixel;	
		var interval = setInterval(showLoad, (timePerPixel*1000)*(1000/pixels*100));
	}
});