#include <stdio.h>
#include <stdlib.h>
#include "structure.h"
#include "mainFunctions.h"

sParamEqua calcParamEquaBetweenTwoPos(sPos pos, sPos light){
	sPos vectorLightToPos;
	sParamEqua paramEquaLightToPos;


	vectorLightToPos.x = pos.x - light.x;
	vectorLightToPos.y = pos.y - light.y;
	vectorLightToPos.z = pos.z - light.z;


	paramEquaLightToPos.x[0] = vectorLightToPos.x;
	paramEquaLightToPos.x[1] = light.x;

	paramEquaLightToPos.y[0] = vectorLightToPos.y;
	paramEquaLightToPos.y[1] = light.y;

	paramEquaLightToPos.z[0] = vectorLightToPos.z;
	paramEquaLightToPos.z[1] = light.z;

	return paramEquaLightToPos;
}

int testTvalueFromParamEqua(sPos pos, sParamEqua paramEqua){
	if(paramEqua.x[0] == 0 || paramEqua.y[0] == 0 || paramEqua.z[0] == 0){
		return 0;
	}
	double t[3];
	t[0] = (pos.x - paramEqua.x[1]) / paramEqua.x[0];
	t[1] = (pos.y - paramEqua.y[1]) / paramEqua.y[0];
	t[2] = (pos.z - paramEqua.z[1]) / paramEqua.z[0];

	if(t[0] != t[1] || t[0] != t[2] || t[2] != t[1]){
		return 0;
	}

	return 1;

}

//ATTENTION CHANGER LE NOM !!!!!!!!!!!!!!!!!!
sPos* intersectLight_PEUL(sParamEqua paramEqua, double t) {
	double x = paramEqua.x[0] * t + paramEqua.x[1];
	double y = paramEqua.y[0] * t + paramEqua.y[1];
	double z = paramEqua.z[0] * t + paramEqua.z[1];
	sPos *pos=(sPos*)malloc(sizeof(sPos));
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
			printf("%.16f\n", theta);
			if (theta == 1.0471975511965976) {  //1.0471975511965976
				return pos;
			}
			theta = 0;
		}
		free(pos);
		
	}	
	return false;
}


int isInTheShadow(sPos pos, sParam param){
	sParamEqua paramEquaLightToPos;
	double t = 0;
	double * tTab = NULL;
	int i = 1; 

	paramEquaLightToPos = calcParamEquaBetweenTwoPos(pos, param.lightSource);

	if(testTvalueFromParamEqua){
		t = (pos.x - paramEquaLightToPos.x[1]) / paramEquaLightToPos.x[0];
	}
	else{
		printf("Erreur de valeur\n");
		return -1;
	}

	tTab = listingTimes(param, tTab);

<<<<<<< HEAD
	while(t > tTab[i]){
=======
	while(t < tTab[i] && tTab[i] <=0){
>>>>>>> 4655457d2d8445034729f8b58b4044aed8d0f33b
		if(!doesCollide_PEUL){
			return 1;
		}
		i++;
		if(i == tTab[0]){
			break;
		}
	}
	return 0;
}



