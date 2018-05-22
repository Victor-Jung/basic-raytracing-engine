#include <stdio.h>
#include <stdlib.h>
#include <math.h>
#include "structure.h"

#define PI 3,1415926535

//equation d'une sphère : (x - x0)² + (y - y0)² + (z - z0)² = r² avec x,y,z inconnues et x0,y0,z0(position du centre de la sphere) et r(rayon) connus
typedef struct sSphere_ sSphere;
struct sSPhere{
	sPos center;
	double r;

};

//voir avec Paul pour modifier les entrer en mettant param
//renvoie un pointeur position si le rayon entre en collision avec la sphere ou false sinon
void* doesCollideSphere (sSphere sphere, sParamEqua lightRay){
	double alpha, beta, gamma, delta;
	double t = 0;
//equation de la sphere devient (A²+B²+C²)t² + 2(A(a-x0) + B(b-y0) + C(c-z0))t + (x0-2a)x0 + (y0-2b)y0 + (z0-2c)z0 + a² + b² + c² - r² = 0
//								  alpha					beta								gamma
//reverifier les calculs
	alpha = pow(lightRay.x[0],2) + pow(lightRay.y[0],2) + pow(lightRay.z[0],2);
	beta = 2*(lightRay.x[0]*(lightRay.x[1] - sphere.center.x) + lightRay.y[0]*(lightRay.y[1] - sphere.center.y) + lightRay.z[0]*(lightRay.z[1] - sphere.center.z));
	gamma = (sphere.center.x - 2*lightRay.x[1])*lightRay.x[0] + (sphere.center.y - 2*lightRay.y[1])*lightRay.y[0] + (sphere.center.z - 2*lightRay.z[1])*lightRay.z[0] + pow(lightRay.x[1],2) + pow(lightRay.y[1],2) + pow(lightRay.z[1],2) - pow(sphere.r,2);
//résolution de polynôme de second degré
	delta = pow(beta,2) - 4*alpha*gamma;

	if(delta > 0){
		double t1 = 0, t2 = 0;

		sPos* intersectionPoint = NULL;
		intersectionPoint = (sPos*) malloc(sizeof(sPos));

		t1 = (-beta - sqrt(delta))/2*alpha;
		t2 = (-beta + sqrt(delta))/2*alpha;

		if(t1 >= t2){
			t = t2;
		}
		else{
			t = t1;
		}

		intersectionPoint->x = lightRay.x[0]*t + lightRay.x[1];
		intersectionPoint->y = lightRay.y[0]*t + lightRay.y[1];
		intersectionPoint->z = lightRay.z[0]*t + lightRay.z[1];

		return intersectionPoint;

	}
	else if(delta == 0){
		sPos* intersectionPoint = NULL;
		intersectionPoint = (sPos*) malloc(sizeof(sPos));

		t = -beta / 2*alpha;

		intersectionPoint->x = lightRay.x[0]*t + lightRay.x[1];
		intersectionPoint->y = lightRay.y[0]*t + lightRay.y[1];
		intersectionPoint->z = lightRay.z[0]*t + lightRay.z[1];

		return intersectionPoint;

	}
	else{
		return false;
	}
}

sPlanEqua makeTangentPlanFromSphere(sPos collisionPoint, sPos centerOfSphere){
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