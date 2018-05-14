#include "mainFunctions.h"
#include <stdio.h>
#include <stdlib.h>
#include <math.h>
#include <stdbool.h>
#include "structure.h"
#define PI 3,1415926535
#define LESS

int compare(double const *a, double const *b) {
	if (floor(*a) > floor(*b)) {
		return 1;
	}
	else if (floor(*a) < floor(*b)) {
		return -1;
	}
	else if ((*a - floor(*a)) > (*b - floor(*b))) {
		return 1;
	}
	else if ((*a - floor(*a)) < (*b - floor(*b))) {
		return -1;
	}
	return 0;
}

void sort(double *t) {
	for (int i = 1; i < t[0]; i++) {
		for (int j = i; j < t[0]; j++) {
			if (compare(&t[i], &t[j + 1])==1) {
				double tmp = t[i];
				t[i] = t[j + 1];
				t[j + 1] = tmp;
			}
		}
	}
}

void showTab(double *t) {
	for (int i = 1; i <= t[0]; i++) {
		printf("%f ", t[i]);
	}
	printf("\n");
}

double* listingTimes(sParam param, double *t) {
	int nbT = 0;
	for (int i = 0; i < param.nbObjects; i++) {
		nbT += param.object[i].nbFaces;
	}
	if (t == NULL) {
		t = (double*)malloc((nbT + 1) * sizeof(double));
	}
	else {
		double *tmp = (double*)realloc( t, (nbT + 1) * sizeof(double));
		if (tmp != NULL) {
			t = tmp;
		}
		else {
			free(tmp);
			return NULL;
		}
	}	
	int cpt = 1;
	t[0] = nbT;
	for (int i = 0; i < param.nbObjects; i++) {
		for (int j = 0; j < param.object[i].nbFaces; j++) {
			t[cpt] = -((param.light.paramEqua.x[1] * param.object[i].face[j].planEqua.a + param.light.paramEqua.y[1] * param.object[i].face[j].planEqua.b + param.light.paramEqua.z[1] * param.object[i].face[j].planEqua.c + param.object[i].face[j].planEqua.d) / (param.object[i].face[j].planEqua.a * param.light.paramEqua.x[0] + param.object[i].face[j].planEqua.b * param.light.paramEqua.y[0] + param.object[i].face[j].planEqua.c * param.light.paramEqua.z[0]));
			cpt++;
		}
	}
	//qsort(t + 1, t[0], sizeof(t), compare);  Ne marche pas
	sort(t);
	return t;
}

void equaParamLight(sParam *param, double X, double Y) {
	sFace planImage;
	planImage.nbPeaks = 0;
	planImage.planEqua.a = 1;
	planImage.planEqua.b = 0;
	planImage.planEqua.c = 0;
	planImage.planEqua.d = -1;
	double nWidth = (param->image.width) / 256;
	double nHeight = (param->image.height) / 256;
	param->light.paramEqua.x[1] = 0;  //AAAAAAAAAAAAAAAAAAAH
	param->light.paramEqua.x[0] = 1;
	param->light.paramEqua.y[1] = param->viewerPos.y;
	param->light.paramEqua.y[0] = (nHeight / 2) - (X / 256);
	param->light.paramEqua.z[1] = param->viewerPos.z;
	param->light.paramEqua.z[0] = (nWidth / 2) - (Y / 256);
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
	}
	return false;
}

#ifdef MORE

int createImage(sPos posLight, sParam param) {
	sColor color;
	for (int w = 0; w < param.image.width; w++) {
		for (int h = 0; h < param.image.height; h++) {
			for (int i = 0; i < param.nbObjects; i++) {
				for (int j = 0; j < param.object[i].nbFaces; j++) {
					if (((posLight.x * param.object[i].face[j].planEqua.a) + (posLight.y* param.object[i].face[j].planEqua.b) + (posLight.z* param.object[i].face[j].planEqua.c)) == -(param.object[i].face[j].planEqua.d)) {
						
					}
				}
			}
		}
	}

}

#endif // MORE

