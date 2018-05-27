#include <stdio.h>
#include <stdlib.h>
#include <math.h>
#include <stdbool.h>
#include "mainFunctions.h"
#include "mainFunctions_PEUL.h"
#define PI 3,1415926535

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

int testTvalueFromParamEqua(sPos pos, sParamEqua paramEqua) { // test si un point appartient à l'equation paramétrique
	double t[3];
	int xTrue = 0, yTrue = 0, zTrue = 0; // bcp de conditions pour éviter de diviser par 0 :(
	if (paramEqua.x[0] != 0) {
		t[0] = (pos.x - paramEqua.x[1]) / paramEqua.x[0];
		xTrue = 1;
	}
	if (paramEqua.y[0] != 0) {
		t[1] = (pos.y - paramEqua.y[1]) / paramEqua.y[0];
		yTrue = 1;
	}
	if (paramEqua.z[0] != 0) {
		t[2] = (pos.z - paramEqua.z[1]) / paramEqua.z[0];
		zTrue = 1;
	}

	if (((t[0] != t[1]) && !zTrue) || (!zTrue && !yTrue && xTrue) || ((t[0] != t[2]) && !yTrue) || (!yTrue && !xTrue && zTrue) || ((t[2] != t[1]) && !xTrue) || (!xTrue && !zTrue && yTrue)) {
		return 0;
	}

	return 1;

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


/*void* doesRayCollideWithAnySphere(sParam param, sParamEqua paramEqua) {// le but est de renvoyer 1 si l'eq paramétrique touche une sphère avec t > 0 et t < 1
	double alpha, beta, gamma, delta;
	double t = 0, t1 = 0, t2 = 0;
	for (int iSphere = 0; iSphere < param.nbSpheres; iSphere++) {

		alpha = pow(paramEqua.x[0], 2) + pow(paramEqua.y[0], 2) + pow(paramEqua.z[0], 2);
		beta = (2 * paramEqua.x[0] * paramEqua.x[1]) - (2 * paramEqua.x[0] * param.sphere[iSphere].center.x) + (2 * paramEqua.y[0] * paramEqua.y[1]) - (2 * paramEqua.y[0] * param.sphere[iSphere].center.y) + (2 * paramEqua.z[0] * paramEqua.z[1]) - (2 * paramEqua.z[0] * param.sphere[iSphere].center.z);
		gamma = (pow(paramEqua.x[1], 2) - 2 * paramEqua.x[1] * param.sphere[iSphere].center.x + pow(param.sphere[iSphere].center.x, 2)) + (pow(paramEqua.y[1], 2) - 2 * paramEqua.y[1] * param.sphere[iSphere].center.y + pow(param.sphere[iSphere].center.y, 2)) + (pow(paramEqua.z[1], 2) - 2 * paramEqua.z[1] * param.sphere[iSphere].center.z + pow(param.sphere[iSphere].center.z, 2)) - pow(param.sphere[iSphere].r, 2);

		delta = pow(beta, 2) - (4 * alpha*gamma);

		

		if (delta > 0.01) {

			sPosSphere* intersectionPoint = NULL;
			intersectionPoint = (sPosSphere*)malloc(sizeof(sPosSphere));
			intersectionPoint->position = (sPos*)malloc(sizeof(sPos));

			t1 = (-beta - sqrt(delta)) / (2 * alpha);
			t2 = (-beta + sqrt(delta)) / (2 * alpha);

			if (t1 > t2) {
				t = t1;
			}
			else if (t2 >= t1) {
				t = t2;
			}
			else {
				return false;
			}

			if (t > 0.01 && t <= 1) {
				intersectionPoint->position->x = param.light.paramEqua.x[0] * t + param.light.paramEqua.x[1];
				intersectionPoint->position->y = param.light.paramEqua.y[0] * t + param.light.paramEqua.y[1];
				intersectionPoint->position->z = param.light.paramEqua.z[0] * t + param.light.paramEqua.z[1];
				intersectionPoint->iSphere = iSphere;
				return intersectionPoint;
			}

		}
		else if (delta > -0.01) {

			sPosSphere* intersectionPoint = NULL;
			intersectionPoint = (sPosSphere*)malloc(sizeof(sPosSphere));
			intersectionPoint->position = (sPos*)malloc(sizeof(sPos));

			t = -beta / (2 * alpha);

			if (t > 0.01 && t <= 1) {
				intersectionPoint->position->x = param.light.paramEqua.x[0] * t + param.light.paramEqua.x[1];
				intersectionPoint->position->y = param.light.paramEqua.y[0] * t + param.light.paramEqua.y[1];
				intersectionPoint->position->z = param.light.paramEqua.z[0] * t + param.light.paramEqua.z[1];
				intersectionPoint->iSphere = iSphere;
				return intersectionPoint;
			}
		}
	}
	return false;
}*/

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

/*void* doesCollideSphere(sParam param) {
	for (int iSphere = 0; iSphere < param.nbSpheres; iSphere++) {
		double alpha, beta, gamma, delta;
		double t = 0;
		//equation de la sphere devient (A²+B²+C²)t² + 2(A(a-x0) + B(b-y0) + C(c-z0))t + (x0-2a)x0 + (y0-2b)y0 + (z0-2c)z0 + a² + b² + c² - r² = 0
		//								  alpha					beta								gamma
		alpha = pow(param.light.paramEqua.x[0], 2) + pow(param.light.paramEqua.y[0], 2) + pow(param.light.paramEqua.z[0], 2);
		beta = (2 * param.light.paramEqua.x[0] * param.light.paramEqua.x[1]) - (2 * param.light.paramEqua.x[0] * param.sphere[iSphere].center.x) + (2 * param.light.paramEqua.y[0] * param.light.paramEqua.y[1]) - (2 * param.light.paramEqua.y[0] * param.sphere[iSphere].center.y) + (2 * param.light.paramEqua.z[0] * param.light.paramEqua.z[1]) - (2 * param.light.paramEqua.z[0] * param.sphere[iSphere].center.z);
		gamma = (pow(param.light.paramEqua.x[1], 2) - 2 * param.light.paramEqua.x[1] * param.sphere[iSphere].center.x + pow(param.sphere[iSphere].center.x, 2)) + (pow(param.light.paramEqua.y[1], 2) - 2 * param.light.paramEqua.y[1] * param.sphere[iSphere].center.y + pow(param.sphere[iSphere].center.y, 2)) + (pow(param.light.paramEqua.z[1], 2) - 2 * param.light.paramEqua.z[1] * param.sphere[iSphere].center.z + pow(param.sphere[iSphere].center.z, 2)) - pow(param.sphere[iSphere].r, 2);
		//résolution de polynôme de second degré
		delta = pow(beta, 2) - (4*alpha*gamma);

		if (delta > 0.01) {
			double t1 = 0, t2 = 0;

			sPosSphere* intersectionPoint = NULL;
			intersectionPoint = (sPosSphere*)malloc(sizeof(sPosSphere));
			intersectionPoint->position = (sPos*)malloc(sizeof(sPos));
			t1 = (-beta - sqrt(delta)) / (2 * alpha);
			t2 = (-beta + sqrt(delta)) / (2 * alpha);

			if (t1 >= t2 && t2 > 0) {
				t = t2;
			}
			else if (t1 > 0) {
				t = t1;
			}
			else if (t2 > 0) {
				t = t2;
			}
			else {
				return false;
			}

			intersectionPoint->position->x = param.light.paramEqua.x[0] * t + param.light.paramEqua.x[1];
			intersectionPoint->position->y = param.light.paramEqua.y[0] * t + param.light.paramEqua.y[1];
			intersectionPoint->position->z = param.light.paramEqua.z[0] * t + param.light.paramEqua.z[1];
			intersectionPoint->iSphere = iSphere;
			return intersectionPoint;

		}
		else if (delta > -0.01) {
			sPosSphere* intersectionPoint = NULL;
			intersectionPoint = (sPosSphere*)malloc(sizeof(sPosSphere));
			intersectionPoint->position = (sPos*)malloc(sizeof(sPos));

			t = -beta / (2 * alpha);

			intersectionPoint->position->x = param.light.paramEqua.x[0] * t + param.light.paramEqua.x[1];
			intersectionPoint->position->y = param.light.paramEqua.y[0] * t + param.light.paramEqua.y[1];
			intersectionPoint->position->z = param.light.paramEqua.z[0] * t + param.light.paramEqua.z[1];
			intersectionPoint->iSphere = iSphere;

			return intersectionPoint;

		}
	}
	return false;
}*/

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

sPlanEqua makeTangentPlanFromSphere(sPos collisionPoint, sPos centerOfSphere) {
	sPos radiusVector;
	sPlanEqua tangentPlan;

	radiusVector.x = collisionPoint.x - centerOfSphere.x;
	radiusVector.y = collisionPoint.y - centerOfSphere.y;
	radiusVector.z = collisionPoint.z - centerOfSphere.z;

	tangentPlan.a = radiusVector.x;
	tangentPlan.b = radiusVector.y;
	tangentPlan.c = radiusVector.z;

	tangentPlan.d = (tangentPlan.a*collisionPoint.x + tangentPlan.b*collisionPoint.y + tangentPlan.c*collisionPoint.z) * (-1);

	return tangentPlan;
}

sPlanEqua makeTangentPlanFromEllipse(sEllipse ellipse, sPos collisionPoint) {
	sPlanEqua tangentPlan;
	sPos gradientVectorAtCollisionPoint;

	gradientVectorAtCollisionPoint.x = 2 * (collisionPoint.x - ellipse.a) / pow(ellipse.alpha, 2);
	gradientVectorAtCollisionPoint.y = 2 * (collisionPoint.y - ellipse.b) / pow(ellipse.beta, 2);
	gradientVectorAtCollisionPoint.z = 2 * (collisionPoint.z - ellipse.c) / pow(ellipse.gamma, 2);

	tangentPlan.a = gradientVectorAtCollisionPoint.x;
	tangentPlan.b = gradientVectorAtCollisionPoint.y;
	tangentPlan.c = gradientVectorAtCollisionPoint.z;

	tangentPlan.d = (-1)*(tangentPlan.a*collisionPoint.x + tangentPlan.b*collisionPoint.y + tangentPlan.c*collisionPoint.z);


	return tangentPlan;
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

double* calcAngleWithSnellDescartes(double *teta, sPos orientationVectorIncidentRay, sPos normalisedVector, double refractiveIndexA, double refractiveIndexB) {
	double scalarProduct = 0;
	teta = (double*)malloc(2 * sizeof(double));
	//calcule de teta 1
	//produit scalaire n . -u
	scalarProduct = (-1)*normalisedVector.x*orientationVectorIncidentRay.x + (-1)*normalisedVector.y*orientationVectorIncidentRay.y + (-1)*normalisedVector.z*orientationVectorIncidentRay.z;
	//formule calcule d'angle à partir de la formule du produit scalaire avec les normes et l'angle
	teta[0] = acos(scalarProduct / sqrt((pow(normalisedVector.x, 2) + pow(normalisedVector.y, 2) + pow(normalisedVector.z, 2))*(pow(orientationVectorIncidentRay.x, 2) + pow(orientationVectorIncidentRay.y, 2) + pow(orientationVectorIncidentRay.z, 2))));
	//calcule de teta 2
	//Formule Snell-Descartes
	teta[1] = asin((refractiveIndexA / refractiveIndexB)*sin(teta[0]));

	return teta;
}

int isTotallyReflected(double refractiveIndexA, double refractiveIndexB, double tetaA) {
	double test = 0;
	test = 1 - pow(refractiveIndexA / refractiveIndexB, 2)*pow(1 - cos(tetaA), 2);
	if (test < 0) {
		return 1;
	}
	return 0;
}


void* isRefractedRay(sParamEqua incidentRay, sFace face, double refractiveIndexA, double refractiveIndexB) {
	sPos pI;
	sPos normalisedVector; //vecteur normal "n"au plan (pointant vers l'exterieur)
	sPos orientationVectorIncidentRay; //vecteur directeur du rayon incident
	sPos orientationVectorRefractedRay;//vecteur directeur du rayon réfracté
	double *teta = NULL; //angles (incident et réfracté)
	sPlanEqua planEqua;
	sParamEqua refractedRay;

	planEqua.a = face.planEqua.a;
	planEqua.b = face.planEqua.b;
	planEqua.c = face.planEqua.c;
	planEqua.d = face.planEqua.d;

	//calcul des coordonnées de I
	double t = (-1)*(incidentRay.x[1] * planEqua.a + incidentRay.y[1] * planEqua.b + incidentRay.z[1] * planEqua.c + planEqua.d) / (incidentRay.x[0] * planEqua.a + incidentRay.y[0] * planEqua.b + incidentRay.z[0] * planEqua.c);
	pI.x = incidentRay.x[0] * t + incidentRay.x[1];
	pI.y = incidentRay.y[0] * t + incidentRay.y[1];
	pI.z = incidentRay.z[0] * t + incidentRay.z[1];

	//determination du vecteur normal au plan
	normalisedVector = findNormalisedVector(planEqua);
	orientationVectorIncidentRay.x = incidentRay.x[0];
	orientationVectorIncidentRay.y = incidentRay.y[0];
	orientationVectorIncidentRay.z = incidentRay.z[0];

	//calcule des angles incident et réfracté
	teta = calcAngleWithSnellDescartes(teta, orientationVectorIncidentRay, normalisedVector, refractiveIndexA, refractiveIndexB);

	//test de la réflexion complète
	if (isTotallyReflected) {
		return false;
	}

	//determination du vecteur directeur du rayon réfracté
	if (((-1)*normalisedVector.x*orientationVectorIncidentRay.x + (-1)*normalisedVector.y*orientationVectorIncidentRay.y + (-1)*normalisedVector.z*orientationVectorIncidentRay.z) >= 0) {
		orientationVectorRefractedRay.x = (refractiveIndexA / refractiveIndexB) * orientationVectorIncidentRay.x + ((refractiveIndexA / refractiveIndexB)*cos(teta[0]) - cos(teta[1]))*normalisedVector.x;
		orientationVectorRefractedRay.y = (refractiveIndexA / refractiveIndexB) * orientationVectorIncidentRay.y + ((refractiveIndexA / refractiveIndexB)*cos(teta[0]) - cos(teta[1]))*normalisedVector.y;
		orientationVectorRefractedRay.z = (refractiveIndexA / refractiveIndexB) * orientationVectorIncidentRay.z + ((refractiveIndexA / refractiveIndexB)*cos(teta[0]) - cos(teta[1]))*normalisedVector.z;
	}
	else {
		orientationVectorRefractedRay.x = (refractiveIndexA / refractiveIndexB) * orientationVectorIncidentRay.x + ((refractiveIndexA / refractiveIndexB)*cos(teta[0]) + cos(teta[1]))*normalisedVector.x;
		orientationVectorRefractedRay.y = (refractiveIndexA / refractiveIndexB) * orientationVectorIncidentRay.y + ((refractiveIndexA / refractiveIndexB)*cos(teta[0]) + cos(teta[1]))*normalisedVector.y;
		orientationVectorRefractedRay.z = (refractiveIndexA / refractiveIndexB) * orientationVectorIncidentRay.z + ((refractiveIndexA / refractiveIndexB)*cos(teta[0]) + cos(teta[1]))*normalisedVector.z;
	}

	//équation paramétrique du rayon réfracté
	refractedRay.x[0] = orientationVectorIncidentRay.x;
	refractedRay.x[1] = pI.x;
	refractedRay.x[0] = orientationVectorIncidentRay.y;
	refractedRay.x[1] = pI.y;
	refractedRay.x[0] = orientationVectorIncidentRay.z;
	refractedRay.x[1] = pI.z;
	free(teta);
	return &refractedRay;
}