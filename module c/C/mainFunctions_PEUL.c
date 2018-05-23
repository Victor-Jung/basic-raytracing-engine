#include <stdio.h>
#include <stdlib.h>
#include <stdbool.h>
#include <math.h>
#include "mainFunctions.h"
#include "mainFunctions_PEUL.h"
#define PI 3.1415926535

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

//ATTENTION CHANGER LE NOM !!!!!!!!!!!!!!!!!!
sPos* intersectLight_PEUL(sParamEqua paramEqua, double t, sPos *pos) { // même fonctionalité que pour intersectLight mais entre la droite d'equation donnée
	double x = paramEqua.x[0] * t + paramEqua.x[1];
	double y = paramEqua.y[0] * t + paramEqua.y[1];
	double z = paramEqua.z[0] * t + paramEqua.z[1];
	pos->x = x;
	pos->y = y;
	pos->z = z;
	return pos;
}


//ATTENTION CHANGER LE NOM !!!!!!!!!!!!!!!!
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
				free(pos);
				return true;
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


int isInTheShadow(sPos pos, sParam param) {
	sParamEqua paramEquaLightToPos;
	double t = 0;
	double * tTab = NULL;
	int i = 1;

	paramEquaLightToPos = calcParamEquaBetweenTwoPos(pos, param.lightSource);

	/*if (testTvalueFromParamEqua(pos, paramEquaLightToPos)) { // test non essentiel = calcul en trop
		t = (pos.x - paramEquaLightToPos.x[1]) / paramEquaLightToPos.x[0];
	}
	else {
		printf("Erreur de valeur\n");
		return -1;
	}*/

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


///////////////////////////

//voir avec Paul pour modifier les entrer en mettant param
//renvoie un pointeur position si le rayon entre en collision avec la sphere ou false sinon
void* doesCollideSphere(sParam param) {
	for (int iSphere = 0; iSphere < param.nbSpheres; iSphere++) {
		double alpha, beta, gamma, delta;
		double t = 0;
		//equation de la sphere devient (A²+B²+C²)t² + 2(A(a-x0) + B(b-y0) + C(c-z0))t + (x0-2a)x0 + (y0-2b)y0 + (z0-2c)z0 + a² + b² + c² - r² = 0
		//								  alpha					beta								gamma
		//reverifier les calculs 
		alpha = pow(param.light.paramEqua.x[0], 2) + pow(param.light.paramEqua.y[0], 2) + pow(param.light.paramEqua.z[0], 2);
		beta = (2 * param.light.paramEqua.x[0] * param.light.paramEqua.x[1]) - (2 * param.light.paramEqua.x[0] * param.sphere[iSphere].center.x) + (2 * param.light.paramEqua.y[0] * param.light.paramEqua.y[1]) - (2 * param.light.paramEqua.y[0] * param.sphere[iSphere].center.y) + (2 * param.light.paramEqua.z[0] * param.light.paramEqua.z[1]) - (2 * param.light.paramEqua.z[0] * param.sphere[iSphere].center.z);
		gamma = (pow(param.light.paramEqua.x[1], 2) - 2 * param.light.paramEqua.x[1] * param.sphere[iSphere].center.x + pow(param.sphere[iSphere].center.x, 2)) + (pow(param.light.paramEqua.y[1], 2) - 2 * param.light.paramEqua.y[1] * param.sphere[iSphere].center.y + pow(param.sphere[iSphere].center.y, 2)) + (pow(param.light.paramEqua.z[1], 2) - 2 * param.light.paramEqua.z[1] * param.sphere[iSphere].center.z + pow(param.sphere[iSphere].center.z, 2)) - pow(param.sphere[iSphere].r, 2);
		//résolution de polynôme de second degré
		delta = pow(beta, 2) - (4 * alpha*gamma);

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

void* doesCollideEllipse (sParam param){
	for (int iEllipse = 0; iEllipse < param.nbEllipse; iEllipse++){
		double F, G, H, delta;
		double t = 0;

		F = pow(param.ellipse[iEllipse].beta * param.ellipse[iEllipse].gamma * param.light.paramEqua.x[0],2) + pow(param.ellipse[iEllipse].gamma * param.ellipse[iEllipse].alpha * param.light.paramEqua.y[0],2) + pow(param.ellipse[iEllipse].alpha * param.ellipse[iEllipse].beta * param.light.paramEqua.z[0],2);
		G = 2 * pow(param.ellipse[iEllipse].beta * param.ellipse[iEllipse].gamma,2) * param.light.paramEqua.x[0] * param.light.paramEqua.x[1] - 2 * pow(param.ellipse[iEllipse].beta * param.ellipse[iEllipse].gamma,2) * param.light.paramEqua.x[0] * param.ellipse[iEllipse].a + 2 * pow(param.ellipse[iEllipse].alpha * param.ellipse[iEllipse].gamma,2) * param.light.paramEqua.y[0] * param.light.paramEqua.y[1] - 2 * pow(param.ellipse[iEllipse].alpha * param.ellipse[iEllipse].gamma,2) * param.light.paramEqua.y[0] * param.ellipse[iEllipse].b + 2 * pow(param.ellipse[iEllipse].beta * param.ellipse[iEllipse].alpha,2) * param.light.paramEqua.z[0] * param.light.paramEqua.z[1] - 2 * pow(param.ellipse[iEllipse].beta * param.ellipse[iEllipse].alpha,2) * param.light.paramEqua.z[0] * param.ellipse[iEllipse].b;
		H = pow(param.ellipse[iEllipse].beta * param.ellipse[iEllipse].gamma * param.ellipse[iEllipse].a,2) + pow(param.ellipse[iEllipse].beta * param.ellipse[iEllipse].gamma * param.light.paramEqua.x[1],2) - 2 * pow(param.ellipse[iEllipse].beta * param.ellipse[iEllipse].gamma,2) * param.ellipse[iEllipse].a * param.light.paramEqua.x[1] + pow(param.ellipse[iEllipse].alpha * param.ellipse[iEllipse].gamma * param.ellipse[iEllipse].b,2) + pow(param.ellipse[iEllipse].alpha * param.ellipse[iEllipse].gamma * param.light.paramEqua.y[1],2) - 2 * pow(param.ellipse[iEllipse].alpha * param.ellipse[iEllipse].gamma,2) * param.ellipse[iEllipse].b * param.light.paramEqua.y[1] + pow(param.ellipse[iEllipse].beta * param.ellipse[iEllipse].alpha * param.ellipse[iEllipse].b,2) + pow(param.ellipse[iEllipse].beta * param.ellipse[iEllipse].alpha * param.light.paramEqua.y[1],2) - 2 * pow(param.ellipse[iEllipse].beta * param.ellipse[iEllipse].alpha,2) * param.ellipse[iEllipse].b * param.light.paramEqua.z[1] - pow(param.ellipse[iEllipse].alpha*param.ellipse[iEllipse].beta*param.ellipse[iEllipse].gamma,2);
	//résolution de polynôme de second degré
		delta = pow(G,2) - 4*F*H;

		if(delta > 0.01){
			double t1 = 0, t2 = 0;

			sPosEllipse* intersectionPoint = NULL;
			intersectionPoint = (sPosEllipse*)malloc(sizeof(sPosEllipse));
			intersectionPoint->position = (sPos*)malloc(sizeof(sPos));

			t1 = (-G - sqrt(delta))/2*F;
			t2 = (-G + sqrt(delta))/2*F;

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
			if(t != 0){
				intersectionPoint->position->x = param.light.paramEqua.x[0] * t + param.light.paramEqua.x[1];
				intersectionPoint->position->y = param.light.paramEqua.y[0] * t + param.light.paramEqua.y[1];
				intersectionPoint->position->z = param.light.paramEqua.z[0] * t + param.light.paramEqua.z[1];
				intersectionPoint->iEllipse = iEllipse;
				return intersectionPoint;				
			}

		}
		else if(delta > -0.01){
			sPosEllipse* intersectionPoint = NULL;
			intersectionPoint = (sPosEllipse*)malloc(sizeof(sPosEllipse));
			intersectionPoint->position = (sPos*)malloc(sizeof(sPos));
		

			t = -G / 2*F;

			intersectionPoint->position->x = param.light.paramEqua.x[0] * t + param.light.paramEqua.x[1];
			intersectionPoint->position->y = param.light.paramEqua.y[0] * t + param.light.paramEqua.y[1];
			intersectionPoint->position->z = param.light.paramEqua.z[0] * t + param.light.paramEqua.z[1];
			intersectionPoint->iEllipse = iEllipse;
			return intersectionPoint;
		}
	}
	return false;
}


sPlanEqua makeTangentPlanFromEllipse(sEllipse ellipse, sPos collisionPoint){
	sPlanEqua tangentPlan;
	sPos gradientVectorAtCollisionPoint;

	gradientVectorAtCollisionPoint.x = 2*(collisionPoint.x - ellipse.a)/pow(ellipse.alpha,2);
	gradientVectorAtCollisionPoint.y = 2*(collisionPoint.y - ellipse.b)/pow(ellipse.beta,2);
	gradientVectorAtCollisionPoint.z = 2*(collisionPoint.z - ellipse.c)/pow(ellipse.gamma,2);

	tangentPlan.a = gradientVectorAtCollisionPoint.x;
	tangentPlan.b = gradientVectorAtCollisionPoint.y;
	tangentPlan.c = gradientVectorAtCollisionPoint.z;

	tangentPlan.d = (-1)*(tangentPlan.a*collisionPoint.x + tangentPlan.b*collisionPoint.y + tangentPlan.c*collisionPoint.z);


	return tangentPlan;
}