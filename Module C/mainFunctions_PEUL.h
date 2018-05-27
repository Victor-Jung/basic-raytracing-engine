#pragma once
#include "structure.h"
#include "mainFunctions.h"
#define PI 3,1415926535

sParamEqua calcParamEquaBetweenTwoPos(sPos pos, sPos light);

sPos* intersectLight_PEUL(sParamEqua paramEqua, double t, sPos *pos);

void* doesCollide_PEUL(sParam param, double t, sParamEqua paramEqua);

int isInTheShadow(sPos pos, sParam param);

void* doesCollideEllipse(sParam param);

sPos findNormalisedVector(sPlanEqua planEqua);

sParamEqua isReflectedRay(sParamEqua incidentRay, sPlanEqua planEqua);

void* doesRayCollideWithAnyEllipse(sParam param, sParamEqua paramEqua);

double* listingTimesWithParamEqua(sParam param, sParamEqua paramEqua, double *t);