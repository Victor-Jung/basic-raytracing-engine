#pragma once

sParamEqua calcParamEquaBetweenTwoPos(sPos pos, sPos light);

int testTvalueFromParamEqua(sPos pos, sParamEqua paramEqua);

sPos* intersectLight_PEUL(sParamEqua paramEqua, double t, sPos *pos);

void* doesCollide_PEUL(sParam param, double t, sParamEqua paramEqua);

int isInTheShadow(sPos pos, sParam param);


void* doesCollideSphere(sParam param);
sPlanEqua makeTangentPlanFromSphere(sPos collisionPoint, sPos centerOfSphere);

void* doesCollideEllipse(sParam param);
sPlanEqua makeTangentPlanFromEllipse(sEllipse ellipse, sPos collisionPoint);

sPos findNormalisedVector(sPlanEqua planEqua);

sParamEqua reflectedRay(sParamEqua incidentRay, sPlanEqua planEqua);

double* calcAngleWithSnellDescartes(double* teta, sPos orientationVectorIncidentRay, sPos normalisedVector, double refractiveIndexA, double refractiveIndexB);

int isTotallyReflected(double refractiveIndexA, double refractiveIndexB, double tetaA);

void* refractedRay(sParamEqua incidentRay, sFace face, double refractiveIndexA, double refractiveIndexB);

void* doesRayCollideWithAnyEllipse(sParam param, sParamEqua paramEqua);

void* doesRayCollideWithAnySphere(sParam param, sParamEqua paramEqua);

double* listingTimesWithParamEqua(sParam param, sParamEqua paramEqua, double *t);