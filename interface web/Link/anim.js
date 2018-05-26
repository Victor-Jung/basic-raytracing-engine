$(document).ready(function() {
	
	
	function GET(param) {
		var vars = {};
		window.location.href.replace( location.hash, '' ).replace( 
			/[?&]+([^=&]+)=?([^&]*)?/gi, // regexp
			function( m, key, value ) { // callback
				vars[key] = value !== undefined ? value : '';
			}
		);

		if ( param ) {
			return vars[param] ? vars[param] : null;	
		}
		return vars;
	}
	
	var Get = GET();
	var iImage = 0;
	var name = Get['name'];
	var cptMax = 1;
	var cpt=0;
	var antialiasing = Get['antialiasing'];
	var nbImages = Get['nbImages'];
	var height = Get['height'];
	var width = Get['width'];
	var fps = $("#fps").val();
	var plus = "";
	if(antialiasing==1){
		plus="AA";
	}
	$("#video").css("height", height+"px");
	$("#video").css("width", width+"px");
	$("#load").html("Chargement...");
	$("#replay").on("click", play);
	
	function checkImg(){
		var tester=new Image();
		tester.onload=function() {}
		tester.onerror=function() {location.reload();}
		tester.src = name+(nbImages-1)+plus+".bmp";
	}	
	
	function load(){
		checkImg();
		$("#video").css("background", "url("+name+iImage+plus+".bmp) no-repeat");
		iImage++;	
		if(iImage>=nbImages){
			iImage=0;
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
		$("#help").css("opacity", "1");	
		$("#video").css("background", "url("+name+iImage+plus+".bmp) no-repeat");
		iImage++;
		if(iImage>=nbImages){
			iImage=0;
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
		iImage = 0;
		cpt=0;
		fps = $("#fps").val();
		clearInterval(loading);
		loading = setInterval(anim, 1000/fps);
	}
});