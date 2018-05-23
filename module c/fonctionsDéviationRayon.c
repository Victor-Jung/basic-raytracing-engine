#include<stdio.h>
#include<stdlib.h>
#include<math.h>
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
		int reflection;   // 0 si opaque, 1 si réfléchissant
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

double* calcAngleWithSnellDescartes (double teta[], sPos orientationVectorIncidentRay, sPos normalisedVector, double refractiveIndexA, double refractiveIndexB){
	double scalarProduct = 0;

//calcule de teta 1
	//produit scalaire n . -u
	scalarProduct = (-1)*normalisedVector.x*orientationVectorIncidentRay.x + (-1)*normalisedVector.y*orientationVectorIncidentRay.y + (-1)*normalisedVector.z*orientationVectorIncidentRay.z;
	//formule calcule d'angle à partir de la formule du produit scalaire avec les normes et l'angle
	teta[0] = acos(scalarProduct / sqrt( ( pow(normalisedVector.x,2) + pow(normalisedVector.y,2) + pow(normalisedVector.z,2) )*( pow(orientationVectorIncidentRay.x,2) + pow(orientationVectorIncidentRay.y,2) + pow(orientationVectorIncidentRay.z,2) ) ) );
//calcule de teta 2
	//Formule Snell-Descartes
	teta[1] = asin((refractiveIndexA/refractiveIndexB)*sin(teta[0]));

	return teta
}

int isTotallyReflected(double refractiveIndexA, double refractiveIndexB, double tetaA){
	double test = 0;
	test = 1 - pow(refractiveIndexA/refractiveIndexB, 2)*pow(1-cos(tetaA), 2);
	if (test < 0){
		return 1;
	}
	return 0
}


sParamEqua refractedRay(sParamEqua incidentRay, sFace face, double refractiveIndexA, double refractiveIndexB){
	sPos pI;
	sPos normalisedVector; //vecteur normal "n"au plan (pointant vers l'exterieur)
	sPos orientationVectorIncidentRay; //vecteur directeur du rayon incident
	sPos orientationVectorRefractedRay;//vecteur directeur du rayon réfracté
	double teta[2]; //angles (incident et réfracté)
	sPlanEqua planEqua;
	sParamEqua refractedRay;

	planEqua.a = face.planEqua.a;
	planEqua.b = face.planEqua.b;
	planEqua.c = face.planEqua.c;
	planEqua.d = face.planEqua.d;

//calcul des coordonnées de I
	t = (-1)*(incidentRay.x[1]*planEqua.a + incidentRay.y[1]*planEqua.b + incidentRay.z[1]*planEqua.c + planEqua.d) / (incidentRay.x[0]*planEqua.a + incidentRay.y[0]*planEqua.b + incidentRay.z[0]*planEqua.c)
	pI.x = incidentRay.x[0]*t+incidentRay.x[1];
	pI.y = incidentRay.y[0]*t+incidentRay.y[1];
	pI.z = incidentRay.z[0]*t+incidentRay.z[1];

//determination du vecteur normal au plan
	normalisedVector = normalisedVector(planEqua);
	orientationVectorIncidentRay.x = incidentRay.x[0];
	orientationVectorIncidentRay.y = incidentRay.y[0];
	orientationVectorIncidentRay.z = incidentRay.z[0];

//calcule des angles incident et réfracté
	teta = calcAngleWithSnellDescartes(teta, orientationVectorIncidentRay, normalisedVector, refractiveIndexA, refractiveIndexB);

//test de la réflexion complète
	if(isTotallyReflected){
		return reflectedRay(incidentRay, face);
	}

//determination du vecteur directeur du rayon réfracté
	if(((-1)*normalisedVector.x*orientationVectorIncidentRay.x + (-1)*normalisedVector.y*orientationVectorIncidentRay.y + (-1)*normalisedVector.z*orientationVectorIncidentRay.z) >=0 ){
		orientationVectorRefractedRay.x = (refractiveIndexA/refractiveIndexB) * orientationVectorIncidentRay.x + ((refractiveIndexA/refractiveIndexB)*cos(teta[0]) - cos(teta[1]))*normalisedVector.x;
		orientationVectorRefractedRay.y = (refractiveIndexA/refractiveIndexB) * orientationVectorIncidentRay.y + ((refractiveIndexA/refractiveIndexB)*cos(teta[0]) - cos(teta[1]))*normalisedVector.y;
		orientationVectorRefractedRay.z = (refractiveIndexA/refractiveIndexB) * orientationVectorIncidentRay.z + ((refractiveIndexA/refractiveIndexB)*cos(teta[0]) - cos(teta[1]))*normalisedVector.z;
	}
	else{
		orientationVectorRefractedRay.x = (refractiveIndexA/refractiveIndexB) * orientationVectorIncidentRay.x + ((refractiveIndexA/refractiveIndexB)*cos(teta[0]) + cos(teta[1]))*normalisedVector.x;
		orientationVectorRefractedRay.y = (refractiveIndexA/refractiveIndexB) * orientationVectorIncidentRay.y + ((refractiveIndexA/refractiveIndexB)*cos(teta[0]) + cos(teta[1]))*normalisedVector.y;
		orientationVectorRefractedRay.z = (refractiveIndexA/refractiveIndexB) * orientationVectorIncidentRay.z + ((refractiveIndexA/refractiveIndexB)*cos(teta[0]) + cos(teta[1]))*normalisedVector.z;
	}

//équation paramétrique du rayon réfracté
	refractedRay.x[0] = orientationVectorIncidentRay.x;
	refractedRay.x[1] = pI.x;
	refractedRay.x[0] = orientationVectorIncidentRay.y;
	refractedRay.x[1] = pI.y;
	refractedRay.x[0] = orientationVectorIncidentRay.z;
	refractedRay.x[1] = pI.z;

	return refractedRay;
}