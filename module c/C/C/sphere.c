#include <stdio.h>
#include <stdlib.h>
#include <math.h>
#include <stdbool.h>
#include "structure.h"

#define PI 3,1415926535


//voir avec Paul pour modifier les entrer en mettant param
//renvoie un pointeur position si le rayon entre en collision avec la sphere ou false sinon
void* doesCollideSphere(sParam param) {
	for (int iSphere = 0; iSphere < param.nbSpheres; iSphere++) {
		double alpha, beta, gamma, delta;
		double t = 0;
		//equation de la sphere devient (A²+B²+C²)t² + 2(A(a-x0) + B(b-y0) + C(c-z0))t + (x0-2a)x0 + (y0-2b)y0 + (z0-2c)z0 + a² + b² + c² - r² = 0
		//								  alpha					beta								gamma
		//reverifier les calculs
		alpha = pow(param.light.paramEqua.x[0], 2) + pow(param.light.paramEqua.y[0], 2) + pow(param.light.paramEqua.z[0], 2);
		beta = 2 * (param.light.paramEqua.x[0] * (param.light.paramEqua.x[1] - param.sphere[iSphere].center.x) + param.light.paramEqua.y[0] * (param.light.paramEqua.y[1] - param.sphere[iSphere].center.y) + param.light.paramEqua.z[0] * (param.light.paramEqua.z[1] - param.sphere[iSphere].center.z));
		gamma = (param.sphere[iSphere].center.x - 2 * param.light.paramEqua.x[1])*param.light.paramEqua.x[0] + (param.sphere[iSphere].center.y - 2 * param.light.paramEqua.y[1])*param.light.paramEqua.y[0] + (param.sphere[iSphere].center.z - 2 * param.light.paramEqua.z[1])*param.light.paramEqua.z[0] + pow(param.light.paramEqua.x[1], 2) + pow(param.light.paramEqua.y[1], 2) + pow(param.light.paramEqua.z[1], 2) - pow(param.sphere[iSphere].r, 2);
		//résolution de polynôme de second degré
		delta = pow(beta, 2) - 4 * alpha*gamma;

		if (delta > 0) {
			double t1 = 0, t2 = 0;

			sPosSphere* intersectionPoint = NULL;
			intersectionPoint = (sPosSphere*)malloc(sizeof(sPosSphere));

			t1 = (-beta - sqrt(delta)) / 2 * alpha;
			t2 = (-beta + sqrt(delta)) / 2 * alpha;

			if (t1 >= t2) {
				t = t2;
			}
			else {
				t = t1;
			}

			intersectionPoint->position->x = param.light.paramEqua.x[0] * t + param.light.paramEqua.x[1];
			intersectionPoint->position->y = param.light.paramEqua.y[0] * t + param.light.paramEqua.y[1];
			intersectionPoint->position->z = param.light.paramEqua.z[0] * t + param.light.paramEqua.z[1];

			return intersectionPoint;

		}
		else if (delta = 0) {
			sPosSphere* intersectionPoint = NULL;
			intersectionPoint = (sPosSphere*)malloc(sizeof(sPosSphere));

			t = -beta / 2 * alpha;

			intersectionPoint->position->x = param.light.paramEqua.x[0] * t + param.light.paramEqua.x[1];
			intersectionPoint->position->y = param.light.paramEqua.y[0] * t + param.light.paramEqua.y[1];
			intersectionPoint->position->z = param.light.paramEqua.z[0] * t + param.light.paramEqua.z[1];

			return intersectionPoint;

		}
	}
	return false;
}

sPlanEqua makeTangentPlanFromSphere(sPos collisionPoint, sPos centerOfSphere) {
	sPos radiusVector;
	sPlanEqua tangentPlan;

	radiusVector.x = collisionPoint.x - centerOfSphere.x;
	radiusVector.y = collisionPoint.y - centerOfSphere.y;
	radiusVector.z = collisionPoint.z - centerOfSphere.z;

	tangentPlan.a = radiusVector.x;
	tangentPlan.b = radiusVector.y;
	tangentPlan.c = radiusVector.z;

	tangentPlan.d = (tangentPlan.a*collisionPoint.x + tangentPlan.b*collisionPoint.y + tangentPlan.c*collisionPoint.z) * (-1);

	return tangentPlan;
}