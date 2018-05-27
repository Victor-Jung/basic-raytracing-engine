#include "mainFunctions_PEUL.h"

sParamEqua calcParamEquaBetweenTwoPos(sPos pos, sPos light) { //le nom de la fonction est explicite :) 
	sPos vectorLightToPos;
	sParamEqua paramEquaLightToPos;


	vectorLightToPos.x = light.x - pos.x;
	vectorLightToPos.y = light.y - pos.y;
	vectorLightToPos.z = light.z - pos.z;


	paramEquaLightToPos.x[0] = vectorLightToPos.x;
	paramEquaLightToPos.x[1] = pos.x;

	paramEquaLightToPos.y[0] = vectorLightToPos.y;
	paramEquaLightToPos.y[1] = pos.y;

	paramEquaLightToPos.z[0] = vectorLightToPos.z;
	paramEquaLightToPos.z[1] = pos.z;

	return paramEquaLightToPos;
}


sPos* intersectLight_PEUL(sParamEqua paramEqua, double t, sPos *pos) { // même fonctionalité que pour intersectLight mais entre la droite d'equation donnée
	double x = paramEqua.x[0] * t + paramEqua.x[1];
	double y = paramEqua.y[0] * t + paramEqua.y[1];
	double z = paramEqua.z[0] * t + paramEqua.z[1];
	pos->x = x;
	pos->y = y;
	pos->z = z;
	return pos;
}


void* doesCollide_PEUL(sParam param, double t, sParamEqua paramEqua) { // même fonction que DoesCollide mais avect du rayon entre le point d'interection et la lumière
	sPos *pos = NULL;
	pos = (sPos*)malloc(sizeof(sPos));
	for (int i = 0; i < param.nbPolyhedrons; i++) {
		pos = intersectLight_PEUL(paramEqua, t, pos);
		double theta = 0;
		for (int k = 0; k < param.poly[i].nbFaces; k++) {
			for (int l = 0; l < param.poly[i].face[k].nbPeaks; l++) {
				if (l + 1 < param.poly[i].face[k].nbPeaks) {
					double xps = param.poly[i].face[k].peak[l].x - pos->x;
					double yps = param.poly[i].face[k].peak[l].y - pos->y;
					double zps = param.poly[i].face[k].peak[l].z - pos->z;
					double xpt = param.poly[i].face[k].peak[l + 1].x - pos->x;
					double ypt = param.poly[i].face[k].peak[l + 1].y - pos->y;
					double zpt = param.poly[i].face[k].peak[l + 1].z - pos->z;
					double lengthPs = pow(xps, 2) + pow(yps, 2) + pow(zps, 2);
					double lengthPt = pow(xpt, 2) + pow(ypt, 2) + pow(zpt, 2);
					theta += acos((xps*xpt + yps*ypt + zps*zpt) / sqrt(lengthPs*lengthPt));
				}
				else {
					double xps = param.poly[i].face[k].peak[l].x - pos->x;
					double yps = param.poly[i].face[k].peak[l].y - pos->y;
					double zps = param.poly[i].face[k].peak[l].z - pos->z;
					double xpt = param.poly[i].face[k].peak[0].x - pos->x;
					double ypt = param.poly[i].face[k].peak[0].y - pos->y;
					double zpt = param.poly[i].face[k].peak[0].z - pos->z;
					double lengthPs = (pow(xps, 2) + pow(yps, 2) + pow(zps, 2));
					double lengthPt = (pow(xpt, 2) + pow(ypt, 2) + pow(zpt, 2));
					theta += acos((xps*xpt + yps*ypt + zps*zpt) / sqrt(lengthPs*lengthPt));
				}
			}
			theta /= 2 * PI;  //precision environ egale a 2.6646.10^-15
			if (theta > 1.047197/*1.047197551196*/ && theta < 1.047198/*1.047197551197*/) {  //1.0471975511965976
				sPosFace *posFace = NULL;
				posFace = (sPosFace*)malloc(sizeof(sPosFace));
				posFace->position = pos;
				posFace->iPoly = i;
				posFace->iFace = k;
				return posFace;
			}
			theta = 0;
		}
	}
	free(pos);
	return false;
}

double *listingTimes_PEUL(sParam param, sPos posObj, double *t) { // liste les valeurs de t entre le point voulu et la lumière en partant en partant du point voulu vers la lumière
	int nbT = 0;
	sParamEqua shadowRay;
	shadowRay.x[0] = param.lightSource.x - posObj.x;
	shadowRay.x[1] = posObj.x;
	shadowRay.y[0] = param.lightSource.y - posObj.y;
	shadowRay.y[1] = posObj.y;
	shadowRay.z[0] = param.lightSource.z - posObj.z;
	shadowRay.z[1] = posObj.z;
	for (int i = 0; i < param.nbPolyhedrons; i++) {
		nbT += param.poly[i].nbFaces;
	}
	t = (double*)malloc((nbT + 1) * sizeof(double));
	int cpt = 1;
	t[0] = nbT;
	for (int i = 0; i < param.nbPolyhedrons; i++) {
		for (int j = 0; j < param.poly[i].nbFaces; j++) {

			/*if ((param.poly[i].face[j].planEqua.a * shadowRay.x[0] + param.poly[i].face[j].planEqua.b * shadowRay.y[0] + param.poly[i].face[j].planEqua.c * shadowRay.z[0]) == 0) {
				t[cpt] = -1;
			*/ //test pas forcément utile (ne pas oublier le else si on le réimplémente)

			t[cpt] = -((shadowRay.x[1] * param.poly[i].face[j].planEqua.a + shadowRay.y[1] * param.poly[i].face[j].planEqua.b + shadowRay.z[1] * param.poly[i].face[j].planEqua.c + param.poly[i].face[j].planEqua.d) / (param.poly[i].face[j].planEqua.a * shadowRay.x[0] + param.poly[i].face[j].planEqua.b * shadowRay.y[0] + param.poly[i].face[j].planEqua.c * shadowRay.z[0]));
			cpt++;
		}
	}
	sort(t);
	return t;
}

double* listingTimesWithParamEqua(sParam param, sParamEqua paramEqua, double *t) {
	int nbT = 0;
	for (int i = 0; i < param.nbPolyhedrons; i++) { // stock le nombre de plan dans nbT
		nbT += param.poly[i].nbFaces;
	}
	t = (double*)malloc((nbT + 1) * sizeof(double));
	int cpt = 1;
	t[0] = nbT; //la première valeur du tableau est le nombre de plan
	for (int i = 0; i < param.nbPolyhedrons; i++) {
		for (int j = 0; j < param.poly[i].nbFaces; j++) { // calcul de la valeur de t pour chaque plan rencontré
			t[cpt] = -((paramEqua.x[1] * param.poly[i].face[j].planEqua.a + paramEqua.y[1] * param.poly[i].face[j].planEqua.b + paramEqua.z[1] * param.poly[i].face[j].planEqua.c + param.poly[i].face[j].planEqua.d) / (param.poly[i].face[j].planEqua.a * paramEqua.x[0] + param.poly[i].face[j].planEqua.b * paramEqua.y[0] + param.poly[i].face[j].planEqua.c * paramEqua.z[0]));
			cpt++;
		}
	}
	//qsort(t + 1, t[0], sizeof(t), compare); // Ne marche pas
	sort(t); // trie le tableau par ordre croissant de valeur de t
	return t;
}

void* doesRayCollideWithAnyEllipse(sParam param, sParamEqua paramEqua) {

	for (int iEllipse = 0; iEllipse < param.nbEllipse; iEllipse++) {
		double F, G, H, delta;
		double t = 0;

		F = ((pow(paramEqua.x[0], 2)) / (pow(param.ellipse[iEllipse].alpha, 2))) + ((pow(paramEqua.y[0], 2)) / (pow(param.ellipse[iEllipse].beta, 2))) + ((pow(paramEqua.z[0], 2)) / (pow(param.ellipse[iEllipse].gamma, 2)));
		G = (((2 * paramEqua.x[1] * paramEqua.x[0]) - (2 * param.ellipse[iEllipse].a * paramEqua.x[0])) / (pow(param.ellipse[iEllipse].alpha, 2))) + (((2 * paramEqua.y[1] * paramEqua.y[0]) - (2 * param.ellipse[iEllipse].b * paramEqua.y[0])) / (pow(param.ellipse[iEllipse].beta, 2))) + (((2 * paramEqua.z[1] * paramEqua.z[0]) - (2 * param.ellipse[iEllipse].c * paramEqua.z[0])) / (pow(param.ellipse[iEllipse].gamma, 2)));
		H = ((pow(paramEqua.x[1], 2) - (2 * param.ellipse[iEllipse].a * paramEqua.x[1]) + pow(param.ellipse[iEllipse].a, 2)) / (pow(param.ellipse[iEllipse].alpha, 2))) + ((pow(paramEqua.y[1], 2) - (2 * param.ellipse[iEllipse].b * paramEqua.y[1]) + pow(param.ellipse[iEllipse].b, 2)) / (pow(param.ellipse[iEllipse].beta, 2))) + ((pow(paramEqua.z[1], 2) - (2 * param.ellipse[iEllipse].c * paramEqua.z[1]) + pow(param.ellipse[iEllipse].c, 2)) / (pow(param.ellipse[iEllipse].gamma, 2))) - 1;

		//résolution de polynôme de second degré
		delta = pow(G, 2) - (4 * F * H);

		if (delta > 0.01) {
			double t1 = 0, t2 = 0;

			sPosEllipse* intersectionPoint = NULL;
			intersectionPoint = (sPosEllipse*)malloc(sizeof(sPosEllipse));
			intersectionPoint->position = (sPos*)malloc(sizeof(sPos));

			t1 = (-G + sqrt(delta)) / (2 * F);
			t2 = (-G - sqrt(delta)) / (2 * F);

			if (t1 > t2) {
				t = t1;
			}
			else if (t2 >= t1) {
				t = t2;
			}
			else {
				return false;
			}

			if (t > 0.01) {
				intersectionPoint->position->x = param.light.paramEqua.x[0] * t + param.light.paramEqua.x[1];
				intersectionPoint->position->y = param.light.paramEqua.y[0] * t + param.light.paramEqua.y[1];
				intersectionPoint->position->z = param.light.paramEqua.z[0] * t + param.light.paramEqua.z[1];
				intersectionPoint->iEllipse = iEllipse;
				return intersectionPoint;
			}

		}
		else if (delta > -0.01) {

			sPosEllipse* intersectionPoint = NULL;
			intersectionPoint = (sPosEllipse*)malloc(sizeof(sPosEllipse));
			intersectionPoint->position = (sPos*)malloc(sizeof(sPos));

			t = -G / (2 * F);

			if (t > 0.01 && t <= 1) {
				intersectionPoint->position->x = param.light.paramEqua.x[0] * t + param.light.paramEqua.x[1];
				intersectionPoint->position->y = param.light.paramEqua.y[0] * t + param.light.paramEqua.y[1];
				intersectionPoint->position->z = param.light.paramEqua.z[0] * t + param.light.paramEqua.z[1];
				intersectionPoint->iEllipse = iEllipse;
				return intersectionPoint;
			}
		}
	}
	return false;
}

void* doesCollideEllipse(sParam param) {
	for (int iEllipse = 0; iEllipse < param.nbEllipse; iEllipse++) {
		double F, G, H, delta;
		double t = 0;
		/*
		F = pow(param.ellipse[iEllipse].beta * param.ellipse[iEllipse].gamma * param.light.paramEqua.x[0], 2) + pow(param.ellipse[iEllipse].gamma * param.ellipse[iEllipse].alpha * param.light.paramEqua.y[0], 2) + pow(param.ellipse[iEllipse].alpha * param.ellipse[iEllipse].beta * param.light.paramEqua.z[0], 2);
		G = 2 * pow(param.ellipse[iEllipse].beta * param.ellipse[iEllipse].gamma, 2) * param.light.paramEqua.x[0] * param.light.paramEqua.x[1] - 2 * pow(param.ellipse[iEllipse].beta * param.ellipse[iEllipse].gamma, 2) * param.light.paramEqua.x[0] * param.ellipse[iEllipse].a + 2 * pow(param.ellipse[iEllipse].alpha * param.ellipse[iEllipse].gamma, 2) * param.light.paramEqua.y[0] * param.light.paramEqua.y[1] - 2 * pow(param.ellipse[iEllipse].alpha * param.ellipse[iEllipse].gamma, 2) * param.light.paramEqua.y[0] * param.ellipse[iEllipse].b + 2 * pow(param.ellipse[iEllipse].beta * param.ellipse[iEllipse].alpha, 2) * param.light.paramEqua.z[0] * param.light.paramEqua.z[1] - 2 * pow(param.ellipse[iEllipse].beta * param.ellipse[iEllipse].alpha, 2) * param.light.paramEqua.z[0] * param.ellipse[iEllipse].b;
		H = pow(param.ellipse[iEllipse].beta * param.ellipse[iEllipse].gamma * param.ellipse[iEllipse].a, 2) + pow(param.ellipse[iEllipse].beta * param.ellipse[iEllipse].gamma * param.light.paramEqua.x[1], 2) - 2 * pow(param.ellipse[iEllipse].beta * param.ellipse[iEllipse].gamma, 2) * param.ellipse[iEllipse].a * param.light.paramEqua.x[1] + pow(param.ellipse[iEllipse].alpha * param.ellipse[iEllipse].gamma * param.ellipse[iEllipse].b, 2) + pow(param.ellipse[iEllipse].alpha * param.ellipse[iEllipse].gamma * param.light.paramEqua.y[1], 2) - 2 * pow(param.ellipse[iEllipse].alpha * param.ellipse[iEllipse].gamma, 2) * param.ellipse[iEllipse].b * param.light.paramEqua.y[1] + pow(param.ellipse[iEllipse].beta * param.ellipse[iEllipse].alpha * param.ellipse[iEllipse].b, 2) + pow(param.ellipse[iEllipse].beta * param.ellipse[iEllipse].alpha * param.light.paramEqua.y[1], 2) - 2 * pow(param.ellipse[iEllipse].beta * param.ellipse[iEllipse].alpha, 2) * param.ellipse[iEllipse].b * param.light.paramEqua.z[1] - pow(param.ellipse[iEllipse].alpha*param.ellipse[iEllipse].beta*param.ellipse[iEllipse].gamma, 2) - 1;
		*/

		F = (pow(param.light.paramEqua.x[0], 2) / pow(param.ellipse[iEllipse].alpha, 2)) + (pow(param.light.paramEqua.y[0], 2) / pow(param.ellipse[iEllipse].beta, 2)) + (pow(param.light.paramEqua.z[0], 2) / pow(param.ellipse[iEllipse].gamma, 2));
		G = (((2 * param.light.paramEqua.x[1] * param.light.paramEqua.x[0]) - (2 * param.ellipse[iEllipse].a * param.light.paramEqua.x[0])) / (pow(param.ellipse[iEllipse].alpha, 2))) + (((2 * param.light.paramEqua.y[1] * param.light.paramEqua.y[0]) - (2 * param.ellipse[iEllipse].b * param.light.paramEqua.y[0])) / (pow(param.ellipse[iEllipse].beta, 2))) + (((2 * param.light.paramEqua.z[1] * param.light.paramEqua.z[0]) - (2 * param.ellipse[iEllipse].c * param.light.paramEqua.z[0])) / (pow(param.ellipse[iEllipse].gamma, 2)));
		H = ((pow(param.light.paramEqua.x[1], 2) - (2 * param.ellipse[iEllipse].a * param.light.paramEqua.x[1]) + pow(param.ellipse[iEllipse].a, 2))/(pow(param.ellipse[iEllipse].alpha, 2))) + ((pow(param.light.paramEqua.y[1], 2) - (2 * param.ellipse[iEllipse].b * param.light.paramEqua.y[1]) + pow(param.ellipse[iEllipse].b, 2)) / (pow(param.ellipse[iEllipse].beta, 2))) + ((pow(param.light.paramEqua.z[1], 2) - (2 * param.ellipse[iEllipse].c * param.light.paramEqua.z[1]) + pow(param.ellipse[iEllipse].c, 2)) / (pow(param.ellipse[iEllipse].gamma, 2))) - 1;


		//résolution de polynôme de second degré
		delta = pow(G, 2) - (4*F*H);

		if (delta > 0.01) {
			double t1 = 0, t2 = 0;

			sPosEllipse* intersectionPoint = NULL;
			intersectionPoint = (sPosEllipse*)malloc(sizeof(sPosEllipse));
			intersectionPoint->position = (sPos*)malloc(sizeof(sPos));

			t1 = (-G - sqrt(delta)) / (2 * F);
			t2 = (-G + sqrt(delta)) / (2 * F);

			if (t1 >= t2 && t2 > 0) {
				t = t2;
			}
			else if (t1 > 0) {
				t = t1;
			}
			else if (t2 > 0) {
				t = t2;
			}
			// else {
			// 	return false;
			// }
			if (t != 0) {
				intersectionPoint->position->x = param.light.paramEqua.x[0] * t + param.light.paramEqua.x[1];
				intersectionPoint->position->y = param.light.paramEqua.y[0] * t + param.light.paramEqua.y[1];
				intersectionPoint->position->z = param.light.paramEqua.z[0] * t + param.light.paramEqua.z[1];
				intersectionPoint->iEllipse = iEllipse;
				return intersectionPoint;
			}

		}
		else if (delta > -0.01) {
			sPosEllipse* intersectionPoint = NULL;
			intersectionPoint = (sPosEllipse*)malloc(sizeof(sPosEllipse));
			intersectionPoint->position = (sPos*)malloc(sizeof(sPos));


			t = -G / (2 * F);

			intersectionPoint->position->x = param.light.paramEqua.x[0] * t + param.light.paramEqua.x[1];
			intersectionPoint->position->y = param.light.paramEqua.y[0] * t + param.light.paramEqua.y[1];
			intersectionPoint->position->z = param.light.paramEqua.z[0] * t + param.light.paramEqua.z[1];
			intersectionPoint->iEllipse = iEllipse;
			return intersectionPoint;
		}
	}
	return false;
}

int isInTheShadow(sPos pos, sParam param) {
	sParamEqua paramEquaLightToPos;
	double t = 0;
	double * tTab = NULL;
	int i = 1;

	paramEquaLightToPos = calcParamEquaBetweenTwoPos(pos, param.lightSource);

	if (doesRayCollideWithAnyEllipse(param, paramEquaLightToPos)) {
		return 1;
	}
	tTab = listingTimes_PEUL(param, pos, tTab);
	while (i < tTab[0]) {
		if (tTab[i] > 0.01 && tTab[i] <= 1) {
			if (doesCollide_PEUL(param, tTab[i], paramEquaLightToPos)) {
				free(tTab);
				return 1;
			}
		}
		i++;
	}
	free(tTab);
	return 0;
}

sPos findNormalisedVector(sPlanEqua planEqua) {
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

sParamEqua isReflectedRay(sParamEqua incidentRay, sPlanEqua planEqua) {
	double t = 0;
	double tD = 0; //"t" sur la droite D
	sPos pI; //point d'intersection entre le rayon et le plan
	sPos pA; //point sur rayon incident
	sPos pAPrime; // projection de A sur le plan
	sPos normalisedVector; //vecteur normal "n"au plan (pointant vers l'exterieur)
	sPos vectorAprimeI;
	sParamEqua D; //droite suivant n et passant par le point A
	sParamEqua reflectedRay;//equation du rayon réfléchi

	//calcul des coordonnées de I
	t = (-1)*(incidentRay.x[1] * planEqua.a + incidentRay.y[1] * planEqua.b + incidentRay.z[1] * planEqua.c + planEqua.d) / (incidentRay.x[0] * planEqua.a + incidentRay.y[0] * planEqua.b + incidentRay.z[0] * planEqua.c);
	pI.x = incidentRay.x[0] * t + incidentRay.x[1];
	pI.y = incidentRay.y[0] * t + incidentRay.y[1];
	pI.z = incidentRay.z[0] * t + incidentRay.z[1];

	normalisedVector = findNormalisedVector(planEqua);
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
	tD = (-1)*(D.x[1] * planEqua.a + D.y[1] * planEqua.b + D.z[1] * planEqua.c + planEqua.d) / (D.x[0] * planEqua.a + D.y[0] * planEqua.b + D.z[0] * planEqua.c);
	pAPrime.x = D.x[0] * tD + D.x[1];
	pAPrime.y = D.y[0] * tD + D.y[1];
	pAPrime.z = D.z[0] * tD + D.z[1];

	//calcul du rayon réfléchi
	reflectedRay.x[0] = pA.x + pI.x - pAPrime.x;
	reflectedRay.x[1] = pI.x;
	reflectedRay.y[0] = pA.y + pI.y - pAPrime.y;
	reflectedRay.y[1] = pI.y;
	reflectedRay.z[0] = pA.z + pI.z - pAPrime.z;
	reflectedRay.z[1] = pI.z;


	return reflectedRay;
}
