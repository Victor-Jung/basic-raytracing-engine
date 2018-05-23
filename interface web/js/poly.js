$(document).ready(function() {
	var texte = "";
	var nbPoly = 0;
	var nbFaces = new Array(10);
	var nbPeaks = new Array(10);
	for(var i=0; i<10;i++){
		nbPeaks[i] = new Array(10);
	}
	//On initialise les tableaux
	
	for(var i = 0; i<=10; i++){
		texte+="<option>"+i+"</option>"; //creation des premieres options
	}
	
	$("#selectPoly").html(texte);
	
	$("#selectPoly").on("change", load);
	
	function load(){
		loadPoly();
		loadFace();
	}
	
	function loadPoly(){
		nbPoly = $("#selectPoly").val();
		texte = $("#allPolys").val();
		if(texte==undefined){
			texte=" ";
		}
		if(nbPoly==0){
			$("#allPolys").html("");
		}
		for(var i=1; i <= nbPoly; i++){
			texte += "<div id='poly"+i+"'><p>----Poly"+i+" <select id='selectFace"+i+"' name='selectFace"+i+"' class='selectFace'></p>";
			for(var j=1; j<=10; j++){
				texte+="<option";
				if(j==nbFaces[i]){
					texte+=" selected";
				}
				texte+=">"+j+"</option>";
			}
			texte+="</select><div id='allFaces"+i+"'></div>";
			texte+="</div>";
			$("#allPolys").html(texte);
		}
		$(".selectFace").on("change", loadFace);
	}		
	load();	//Premier chargement
	
	function loadFace(){
		for(var i=1; i<=nbPoly; i++){
			texte = $("#allFaces"+i).val();
			if(texte==undefined){
				texte=" ";
			}
			nbFaces[i] = $("#selectFace"+i).val();
			for(var j=1; j<=nbFaces[i]; j++){
				texte += "<div id='face"+j+"'><p>--------Face"+j+" <select id='selectPeak"+i+j+"' name='selectPeak"+i+j+"' class='selectPeak'></p>";
				for(var k=1; k<=10; k++){
					texte+="<option";
					if(k==nbPeaks[i][j]){
						texte+=" selected";
					}
					texte+=">"+k+"</option>";
				}
				texte+="</select><div id='allPeaks"+i+j+"'>";
				texte+="</div></div>";
			}
			$("#allFaces"+i).html(texte);
		}
		$(".selectPeak").on("change", loadPeak);
		loadPeak();
	}
	
	function loadPeak(){
		var j=1;
		var k = 1;
		for(var i=1; i<=nbPoly; i++){
			texte = $("#allPeaks"+i+j).val();
			if(texte==undefined){
				texte=" ";
			}
			nbFaces[i] = $("#selectFace"+i).val();
			for(j=1; j<=nbFaces[i]; j++){
				texte = $("#allPeaks"+i+j).val();
				nbPeaks[i][j] = $("#selectPeak"+i+j).val();
				for(var l=1; l<=nbPeaks[i][j]; l++){
					texte+= "<p>------------Sommet"+l+"</p>";
				};
				$("#allPeaks"+i+j).html(texte);
			}
		}
	}
});