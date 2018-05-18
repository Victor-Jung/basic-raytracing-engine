#include <stdio.h>
#include <stdlib.h>
#include <math.h>
#include "structure.h"

#define PI 3,1415926535

//equation d'une ellipse (x-a)²/alpha²  +  (y-b)²/beta²   +  (z-c)²/gamma²  =  1    alpha, beta et gamma donnent les longueurs des rayons dans les 3 directions,  (a,b,c) sont les coordonnées du centre de l'ellipse
typedef struct sEllipse_ sEllipse;
struct sEllipse_{
	double a;
	double b;
	double c;

	double alpha;
	double beta;
	double gamma;
};


void* doesCollideEllipse (sEllipse ellipse, sParamEqua lightRay){
	double F, G, H, delta;
	double t = 0;

	F = pow(ellipse.beta*ellipse.gamma*lightRay.x[0],2) + pow(ellipse.gamma*ellipse.alpha*lightRay.y[0],2) + pow(ellipse.alpha*ellipse.beta*lightRay.z[0],2);
	G = 2*(pow(ellipse.beta*ellipse.gamma,2)*lightRay.x[0]*(lightRay.x[1] - ellipse.a) + pow(ellipse.alpha*ellipse.gamma,2)*lightRay.y[0]*(lightRay.y[1] - ellipse.b) + pow(ellipse.beta*ellipse.alpha,2)lightRay.z[0]*(lightRay.z[1] - ellipse.c));
	H = pow(ellipse.beta*ellipse.gamma,2)*((ellipse.a - 2*lightRay.x[1])*ellipse.a+pow(lightRay.x[1],2)) + pow(ellipse.alpha*ellipse.gamma,2)*((ellipse.b - 2*lightRay.y[1])*ellipse.b+pow(lightRay.y[1],2)) + pow(ellipse.beta*ellipse.alpha,2)*((ellipse.c - 2*lightRay.z[1])*ellipse.c+pow(lightRay.z[1],2)) - pow(ellipse.gamma*ellipse.beta*ellipse.alpha,2);
//résolution de polynôme de second degré
	delta = pow(G,2) - 4*F*H;

	if(delta > 0){
		double t1 = 0, t2 = 0;

		sPos* intersectionPoint = NULL;
		intersectionPoint = (sPos*) malloc(sizeof(sPos));

		t1 = (-G - sqrt(delta))/2*F;
		t2 = (-G + sqrt(delta))/2*F;

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

		t = -G / 2*F;

		intersectionPoint->x = lightRay.x[0]*t + lightRay.x[1];
		intersectionPoint->y = lightRay.y[0]*t + lightRay.y[1];
		intersectionPoint->z = lightRay.z[0]*t + lightRay.z[1];

		return intersectionPoint;

	}
	else{
		return false;
	}
}