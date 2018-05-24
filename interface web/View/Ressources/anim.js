$(document).ready(function() {
	var iImage = 0;
	var name = "temporaire";
	var cptMax = 1;
	var cpt=0;
	var nbImages = 50;
	var fps = $("#fps").val();
	
	$("#load").html("Chargement...");
	$("#replay").on("click", play);
	
	function load(){
		$("#video").css("background", "url(.../Link/"+name+iImage+"AA.bmp)");
		iImage++;	
		if(iImage>=nbImages){
			iImage=1;
			cpt++;
			if(cpt==4){
				cpt=0;
				clearInterval(loading);
				loading = setInterval(anim, 1000/fps);
			}
		}
	}
	
	function anim(){
		$("#load").html("Chargement terminé");
		$("#video").css("opacity", "1");	
		$("#video").css("background", "url(Anim1/"+iImage+".bmp)");
		iImage++;
		if(iImage>50){
			iImage=1;
			cpt++;
			if(cpt==cptMax){
				clearInterval(loading);
				$("#replay").css("opacity", "1");
				document.onkeypress=function(e){
					e=e||window.event;
					var key=e.which?e.which:event.keyCode;
					if (key==13){
						clearInterval(loading);
						play();
					}
				}
			}
		}
	}
	
	var loading = setInterval(load, 10);
	
	function play(){
		iImage = 1;
		cpt=0;
		fps = $("#fps").val();
		clearInterval(loading);
		loading = setInterval(anim, 1000/fps);
	}
});