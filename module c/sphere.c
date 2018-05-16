#include <stdio.h>
#include <stdlib.h>
#include <math.h>
#include "structure.h"

#define PI 3,1415926535

//equation d'une sphère : (x - X)² + (y - Y)² + (z - Z)² = r² avec x,y,z inconnues et X,Y,Z(position du centre de la sphere) et r(rayon) connus
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
//equation de la sphere devient (A²+B²+C²)t² + 2(A(a-X) + B(b-Y) + C(c-Z))t + (X-2a)X + (Y-2b)Y + (Z-2c)Z + a² + b² + c² - r² = 0
//								  alpha					beta								gamma
	alpha = lightRay.x[0] + lightRay.y[0] + lightRay.z[0];
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
	else if(delta = 0){
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


