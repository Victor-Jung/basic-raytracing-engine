#pragma once
#include "structure.h"

sPos findNormalisedVector(sPlanEqua planEqua);

void* reflectedRay(sParamEqua incidentRay, sFace face);

double* calcAngleWithSnellDescartes(double* teta, sPos orientationVectorIncidentRay, sPos normalisedVector, double refractiveIndexA, double refractiveIndexB);

int isTotallyReflected(double refractiveIndexA, double refractiveIndexB, double tetaA);

void* refractedRay(sParamEqua incidentRay, sFace face, double refractiveIndexA, double refractiveIndexB);