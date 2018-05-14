#pragma once
#include "bmp.h"
#include <stdio.h>

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

typedef struct sFace_ sFace;
struct sFace_ {
	int nbPeaks;
	sPos *peak;
	sPlanEqua planEqua;
};

typedef struct sObject_ sObject;
struct sObject_ {
	sColor color;
	sFormula formula;
	int nbFaces;
	sFace *face;
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
	sParamEqua paramEqua;
};

typedef struct sParam_ sParam;
struct sParam_ {
	sImage image;

	int nbObjects;
	sObject *object;

	sLight light;
};

int nbLine(FILE *f);

void showStruct(sParam param);

int loadFromFile(sParam *param);