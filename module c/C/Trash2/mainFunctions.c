#include "mainFunctions.h"
#include <stdio.h>
#include <stdlib.h>
#include <math.h>
#include <stdbool.h>
#include "structure.h"

double* sort(double *t, int size) {

}

double* listingTimes(sParam param, double* t) {
	int nbT = 0;
	for (int i = 0; i < param.nbObjects; i++) {
		nbT = param.object[i].nbFaces;
	}
	t = (double*)malloc(nbT * sizeof(double));
	int cpt = 0;
	for (int i = 0; i < param.nbObjects; i++) {
		for (int j = 0; j < param.object[i].nbFaces; j++) {
			t[cpt] = -((param.object[i].paramEqua.x[1] * param.object[i].planEqua[j].a + param.object[i].paramEqua.y[1] * param.object[i].planEqua[j].b + param.object[i].paramEqua.z[1] * param.object[i].planEqua[j].c + param.object[i].planEqua[j].d) / (param.object[i].planEqua[j].a * param.object[i].paramEqua.x[0] + param.object[i].planEqua[j].b * param.object[i].paramEqua.y[0] + param.object[i].planEqua[j].c * param.object[i].paramEqua.z[0]));
			cpt++;
		}
	}
	return t;
}

sPos doesCollide() {

}

bool isInside() {

}

