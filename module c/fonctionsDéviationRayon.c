#include<stdio.h>
#include<stdlib.h>
#include<math.h>
#include"mainFunctions.h"
#include"mainFunctionsPEUL.h"
#include"structure.h"

#define PI 3,1415926535


/*  //IDEES pour m'aider ;)

//pour les structures :

	typedef struct sFace_ sFace;		//structure déjà présente mais a compléter
	struct sFace_ {
		int nbPeaks;
		sPos *peak;
		sPlanEqua planEqua;
		double refractiveIndex;
		int reflection;   // 0 si opaque, 1 si réfléchi
	};

*/

sPos normalisedVector(sPlanEqua planEqua){
	sPos n;
	n.x = planEqua.a;
	n.y = planEqua.b;
	n.z = planEqua.c;

	return n;
}



//si le rayon réfléchi la lumiere
//		Affiche l'objet et renvoie la lumière vers un autre objet

//On considère que le rayon passe par la face réfléchissante

//fonction qui renvoie l'équation paramétrique d'un rayon réfléchi par une face d'un objet, prend en paramètres le rayon lumineux incident et la face réflechissante de l'objet
sParamEqua reflectedRay (sParamEqua incidentRay, sFace face){
	double t = 0;
	double tD = 0; //"t" sur la droite D
	sPos pI; //point d'intersection entre le rayon et le plan
	sPos pA; //point sur rayon incident
	sPos pAPrime; // projection de A sur le plan
	sPos normalisedVector; //vecteur normal "n"au plan (pointant vers l'exterieur)
	sPos vectorAprimeI;
	sPlanEqua planEqua;
	sParamEqua D; //droite suivant n et passant par le point A
	sParamEqua reflectedRay;//equation du rayon réfléchi

	planEqua.a = face.planEqua.a;
	planEqua.b = face.planEqua.b;
	planEqua.c = face.planEqua.c;
	planEqua.d = face.planEqua.d;
//calcul des coordonnées de I
	t = (-1)*(incidentRay.x[1]*planEqua.a + incidentRay.y[1]*planEqua.b + incidentRay.z[1]*planEqua.c + planEqua.d) / (incidentRay.x[0]*planEqua.a + incidentRay.y[0]*planEqua.b + incidentRay.z[0]*planEqua.c)
	pI.x = incidentRay.x[0]*t+incidentRay.x[1];
	pI.y = incidentRay.y[0]*t+incidentRay.y[1];
	pI.z = incidentRay.z[0]*t+incidentRay.z[1];

	normalisedVector = normalisedVector(planEqua);
//coordonnées de A
	pA.x = incidentRay.x[1];
	pA.y = incidentRay.y[1];
	pA.z = incidentRay.z[1];
//Equation de la droite D pour projeter A sur le plan
	D.x[0] = normalisedVector.x;
	D.x[1] = pA.x;
	D.y[0] = normalisedVector.y;
	D.y[1] = pA.y;
	D.z[0] = normalisedVector.z;
	D.z[1] = pA.z;
//calcul des coordonnées de A', projection de A sur le plan
	tD = (-1)*(D.x[1]*planEqua.a + D.y[1]*planEqua.b + D.z[1]*planEqua.c + planEqua.d) / (D.x[0]*planEqua.a + D.y[0]*planEqua.b + D.z[0]*planEqua.c)
	pAPrime.x = D.x[0]*tD+D.x[1];
	pAPrime.y = D.y[0]*tD+D.y[1];
	pAPrime.z = D.z[0]*tD+D.z[1];

//calcul du rayon réfléchi
	reflectedRay.x[0] = pA.x + pI.x - pAPrime.x;
	reflectedRay.x[1] = pI.x;
	reflectedRay.y[0] = pA.y + pI.y - pAPrime.y;
	reflectedRay.y[1] = pI.y;
	reflectedRay.z[0] = pA.z + pI.z - pAPrime.z;
	reflectedRay.z[1] = pI.z;


	return reflectedRay;
}


sParamEqua refractedRay(sParamEqua incidentRay, sFace face){
	sPos normalisedVector; //vecteur normal "n"au plan (pointant vers l'exterieur)
	sPlanEqua planEqua;

	planEqua.a = face.planEqua.a;
	planEqua.b = face.planEqua.b;
	planEqua.c = face.planEqua.c;
	planEqua.d = face.planEqua.d;

	normalisedVector = normalisedVector(planEqua);
}