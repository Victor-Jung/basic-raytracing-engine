#pragma once
#include "bmp.h"

typedef struct sFormula_ sFormula;
struct sFormula_ {
	int nbX;
	double *x;
	int nbY;
	double *y;
	int nbZ;
	double *z;
};

typedef struct sParametricEquation_ sParamEqua;
struct sParametricEquation_ {
	double x[2];
	double y[2];
	double z[2];
};

typedef struct sPosition_ sPos;
struct sPosition_ {
	double x;
	double y;
	double z;
};

typedef struct sPlanEquation_ sPlanEqua;
struct sPlanEquation_ {
	double a;
	double b;
	double c;
	double d;
};

typedef struct sObject_ sObject;
struct sObject_ {
	sFormula formula;
	sParamEqua paramEqua;
	int nbFaces;
	sPlanEqua *planEqua;
	int nbPeaks;
	sPos *peak;
};

typedef struct sImage_ sImage;
struct sImage_ {
	char *name;
	unsigned int width;
	unsigned int height;
	sColor background;
};

typedef struct sLight_ sLight;
struct sLight_ {
	float lightFactor;
};

typedef struct sParam_ sParam;
struct sParam_ {
	sImage image;

	int nbObjects;
	sObject *object;

	sLight light;
};

int loadFromFile(sParam *param);