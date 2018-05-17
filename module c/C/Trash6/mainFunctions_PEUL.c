#include <stdio.h>
#include <stdlib.h>
#include <math.h>
#include "structure.h"
#include "mainFunctions.h"
#include "mainFunctions_PEUL.h"
#define PI 3,1415926535

sParamEqua calcParamEquaBetweenTwoPos(sPos pos, sPos light) {
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

int testTvalueFromParamEqua(sPos pos, sParamEqua paramEqua) {
	double t[3];
	int xTrue = 0, yTrue = 0, zTrue = 0;
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
sPos* intersectLight_PEUL(sParamEqua paramEqua, double t) {
	double x = paramEqua.x[0] * t + paramEqua.x[1];
	double y = paramEqua.y[0] * t + paramEqua.y[1];
	double z = paramEqua.z[0] * t + paramEqua.z[1];
	sPos *pos = (sPos*)malloc(sizeof(sPos));
	pos->x = x;
	pos->y = y;
	pos->z = z;
	return pos;
}


//ATTENTION CHANGER LE NOM !!!!!!!!!!!!!!!!
void* doesCollide_PEUL(sParam param, double t, sParamEqua paramEqua) {
	for (int i = 0; i < param.nbObjects; i++) {
		sPos *pos = NULL;
		pos = intersectLight_PEUL(paramEqua, t);
		double theta = 0;
		for (int k = 0; k < param.object[i].nbFaces; k++) {
			for (int l = 0; l < param.object[i].face[k].nbPeaks; l++) {
				if (l + 1 < param.object[i].face[k].nbPeaks) {
					double xps = param.object[i].face[k].peak[l].x - pos->x;
					double yps = param.object[i].face[k].peak[l].y - pos->y;
					double zps = param.object[i].face[k].peak[l].z - pos->z;
					double xpt = param.object[i].face[k].peak[l + 1].x - pos->x;
					double ypt = param.object[i].face[k].peak[l + 1].y - pos->y;
					double zpt = param.object[i].face[k].peak[l + 1].z - pos->z;
					double lengthPs = pow(xps, 2) + pow(yps, 2) + pow(zps, 2);
					double lengthPt = pow(xpt, 2) + pow(ypt, 2) + pow(zpt, 2);
					theta += acos((xps*xpt + yps*ypt + zps*zpt) / sqrt(lengthPs*lengthPt));
				}
				else {
					double xps = param.object[i].face[k].peak[l].x - pos->x;
					double yps = param.object[i].face[k].peak[l].y - pos->y;
					double zps = param.object[i].face[k].peak[l].z - pos->z;
					double xpt = param.object[i].face[k].peak[0].x - pos->x;
					double ypt = param.object[i].face[k].peak[0].y - pos->y;
					double zpt = param.object[i].face[k].peak[0].z - pos->z;
					double lengthPs = (pow(xps, 2) + pow(yps, 2) + pow(zps, 2));
					double lengthPt = (pow(xpt, 2) + pow(ypt, 2) + pow(zpt, 2));
					theta += acos((xps*xpt + yps*ypt + zps*zpt) / sqrt(lengthPs*lengthPt));
				}
			}
			theta /= 2 * PI;  //precision environ egale a 2.6646.10^-15
			if (theta > 1.047197/*1.047197551196*/ && theta < 1.047198/*1.047197551197*/) {  //1.0471975511965976
				return pos;
			}
			theta = 0;
		}
		free(pos);

	}
	return false;
}

double *listingTimes_PEUL(sParam param, sPos posObj, double *t) {
	int nbT = 0;
	sParamEqua shadowRay;
	shadowRay.x[0] = param.lightSource.x - posObj.x;
	shadowRay.x[1] = posObj.x;
	shadowRay.y[0] = param.lightSource.y - posObj.y;
	shadowRay.y[1] = posObj.y;
	shadowRay.z[0] = param.lightSource.z - posObj.z;
	shadowRay.z[1] = posObj.z;
	for (int i = 0; i < param.nbObjects; i++) {
		nbT += param.object[i].nbFaces;
	}
	t = (double*)malloc((nbT + 1) * sizeof(double));
	int cpt = 1;
	t[0] = nbT;
	for (int i = 0; i < param.nbObjects; i++) {
		for (int j = 0; j < param.object[i].nbFaces; j++) {
			if ((param.object[i].face[j].planEqua.a * shadowRay.x[0] + param.object[i].face[j].planEqua.b * shadowRay.y[0] + param.object[i].face[j].planEqua.c * shadowRay.z[0]) == 0) {
				t[cpt] = -1;
			}
			else {
				t[cpt] = -((shadowRay.x[1] * param.object[i].face[j].planEqua.a + shadowRay.y[1] * param.object[i].face[j].planEqua.b + shadowRay.z[1] * param.object[i].face[j].planEqua.c + param.object[i].face[j].planEqua.d) / (param.object[i].face[j].planEqua.a * shadowRay.x[0] + param.object[i].face[j].planEqua.b * shadowRay.y[0] + param.object[i].face[j].planEqua.c * shadowRay.z[0]));
			}
			cpt++;
		}
	}
	//qsort(t + 1, t[0], sizeof(t), compare);  Ne marche pas
	sort(t);
	return t;
}

int isInTheShadow(sPos pos, sParam param) {
	sParamEqua paramEquaLightToPos;
	double t = 0;
	double * tTab = NULL;
	int i = 1;

	paramEquaLightToPos = calcParamEquaBetweenTwoPos(pos, param.lightSource);

	if (testTvalueFromParamEqua(pos, paramEquaLightToPos)) {
		t = (pos.x - paramEquaLightToPos.x[1]) / paramEquaLightToPos.x[0];
	}
	else {
		printf("Erreur de valeur\n");
		return -1;
	}

	tTab = listingTimes_PEUL(param, pos, tTab);
	while (i < tTab[0]) {
		if (tTab[i] > 0) {
			if (doesCollide_PEUL(param, tTab[i], paramEquaLightToPos)) {
				return 1;
			}
		}
		i++;
	}
	return 0;
}