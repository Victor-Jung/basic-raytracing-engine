$(document).ready(function() {
	function openWin(title) {
		myWindow = window.open(title, "myWindow", "width=400, height=200");
	}
	$("#faces").on("change", load);
	
	document.onkeypress=function(e){
		e=e||window.event;
		var key=e.which?e.which:event.keyCode;
		if (key==13){
			load();
		}
		else{
			alert("Non, c'est pas cette touche, patate");
		}
	}
	
	function load(){
		var nbFaces = $("#faces").val();
		var niou="";
		$("#show").text("");
		for(var i=1; i<=nbFaces; i++){
			niou += "<p>test" + i + "</p><br/><input type='text' placeholder='" + i + " Ecrivez du caca'/>";
		}
		$("#show").html(niou);
		openWin("load.html");
	}
});