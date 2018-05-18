#include "mainFunctions.h"
#include <stdio.h>
#include <stdlib.h>
#include <math.h>
#include <stdbool.h>
#include "structure.h"
#define PI 3,1415926535
#define MORE

#ifdef MORE

int compare(void const *a, void const *b) {
	if (a > b) {
		return 1;
	}
	else if (a < b) {
		return -1;
	}
	return 0;
}

void showTab(double *t) {
	for (int i = 1; i <= t[0]; i++) {
		printf("%f ", t[i]);
	}
	printf("\n");
}

double* listingTimes(sParam param, double* t) {
	int nbT = 0;
	for (int i = 0; i < param.nbObjects; i++) {
		nbT += param.object[i].nbFaces;
	}
	t = (double*)malloc((nbT + 1) * sizeof(double));
	int cpt = 1;
	t[0] = nbT;
	for (int i = 0; i < param.nbObjects; i++) {
		for (int j = 0; j < param.object[i].nbFaces; j++) {
			t[cpt] = -((param.light.paramEqua.x[1] * param.object[i].face[j].planEqua.a + param.light.paramEqua.y[1] * param.object[i].face[j].planEqua.b + param.light.paramEqua.z[1] * param.object[i].face[j].planEqua.c + param.object[i].face[j].planEqua.d) / (param.object[i].face[j].planEqua.a * param.light.paramEqua.x[0] + param.object[i].face[j].planEqua.b * param.light.paramEqua.y[0] + param.object[i].face[j].planEqua.c * param.light.paramEqua.z[0]));
			cpt++;
		}
	}
	qsort(t + 1, nbT, sizeof(*t), compare);
	showTab(t);
	return t;
}

sPos* intersectLight(sParam param, double t) {
	double x = param.light.paramEqua.x[0] * t + param.light.paramEqua.x[1];
	double y = param.light.paramEqua.y[0] * t + param.light.paramEqua.y[1];
	double z = param.light.paramEqua.z[0] * t + param.light.paramEqua.z[1];
	sPos *pos=(sPos*)malloc(sizeof(sPos));
	pos->x = x;
	pos->y = y;
	pos->z = z;
	return pos;
}

void* doesCollide(sParam param, double *t) {
	for (int i = 0; i < param.nbObjects; i++) {
		for (int j = 1; j <= t[0]; j++) {
			sPos *pos = NULL;
			pos = intersectLight(param, t[j]);
			double theta = 0;
			for (int k = 0; k < param.object[i].nbFaces; k++) {
				for (int l = 0; l < param.object[i].face[k].nbPeaks; l++) {
					if (l + 1 < param.object[i].face[k].nbPeaks) {
						double xps = param.object[i].face[k].peak[l + 1].x - pos->x;
						double yps = param.object[i].face[k].peak[l + 1].y - pos->y;
						double zps = param.object[i].face[k].peak[l + 1].z - pos->z;
						double xpt = param.object[i].face[k].peak[l + 1].x - pos->x;
						double ypt = param.object[i].face[k].peak[l + 1].y - pos->y;
						double zpt = param.object[i].face[k].peak[l + 1].z - pos->z;
						double lengthPs = sqrt(pow(xps, 2) + pow(yps, 2) + pow(zps, 2));
						double lengthPt = sqrt(pow(xpt, 2) + pow(ypt, 2) + pow(zpt, 2));
						theta += acos((xps*xpt + yps*ypt + zps*zpt) / (lengthPs*lengthPt));
					}
					else {
						double xps = param.object[i].face[k].peak[0].x - pos->x;
						double yps = param.object[i].face[k].peak[0].y - pos->y;
						double zps = param.object[i].face[k].peak[0].z - pos->z;
						double xpt = param.object[i].face[k].peak[0].x - pos->x;
						double ypt = param.object[i].face[k].peak[0].y - pos->y;
						double zpt = param.object[i].face[k].peak[0].z - pos->z;
						double lengthPs = sqrt(pow(xps, 2) + pow(yps, 2) + pow(zps, 2));
						double lengthPt = sqrt(pow(xpt, 2) + pow(ypt, 2) + pow(zpt, 2));
						theta += acos((xps*xpt + yps*ypt + zps*zpt) / (lengthPs*lengthPt));
					}
				}
			}
			theta /= 2 * PI;
			if (theta >= 0.99 && theta <= 1.01) {
				return pos;
			}
		}
	}
	return false;
}

#endif // MORE

