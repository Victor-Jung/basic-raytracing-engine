#define _CRT_SECURE_NO_WARNINGS
#include "mainFunctions.h"
#include <stdio.h>
#include <stdlib.h>
#include <math.h>
#include <stdbool.h>
#include <string.h>
#include "mainFunctions_PEUL.h"
#define ALIASING 1
#define PI 3,1415926535
#define INTENSITY 90 //donne le nombre maximum d'unité où la lumière va se propager, si le rendu est trop sombre
#define MORE		 //il faut augmenter la valeur et vice versa.

int compare(double const *a, double const *b) { //fonction de comparaison entre 2 double
	if (floor(*a) > floor(*b)) {
		return 1;
	}
	else if (floor(*a) < floor(*b)) {
		return -1;
	}
	else if ((*a - floor(*a)) > (*b - floor(*b))) {
		return 1;
	}
	else if ((*a - floor(*a)) < (*b - floor(*b))) {
		return -1;
	}
	return 0;
}

void sort(double *t) { // trie un tableau de valeurs double en partant de l'indice 1
	for (int i = 1; i < t[0]; i++) {
		for (int j = i; j < t[0]; j++) {
			if (compare(&t[i], &t[j + 1])==1) {
				double tmp = t[i];
				t[i] = t[j + 1];
				t[j + 1] = tmp;
			}
		}
	}
}

void showTab(double *t) { //fonction de débog du tableau de t
	for (int i = 1; i <= t[0]; i++) {
		printf("%.15f ", t[i]);
	}
	printf("\n");
}

double* listingTimes(sParam param, double *t) { 
	int nbT = 0;
	for (int i = 0; i < param.nbPolyhedrons; i++) { // stock le nombre de plan dans nbT
		nbT += param.poly[i].nbFaces; 
	}
	t = (double*)malloc((nbT + 1) * sizeof(double));
	int cpt = 1;
	t[0] = nbT; //la première valeur du tableau est le nombre de plan
	for (int i = 0; i < param.nbPolyhedrons; i++) {
		for (int j = 0; j < param.poly[i].nbFaces; j++) { // calcul de la valeur de t pour chaque plan rencontré
			t[cpt] = -((param.light.paramEqua.x[1] * param.poly[i].face[j].planEqua.a + param.light.paramEqua.y[1] * param.poly[i].face[j].planEqua.b + param.light.paramEqua.z[1] * param.poly[i].face[j].planEqua.c + param.poly[i].face[j].planEqua.d) / (param.poly[i].face[j].planEqua.a * param.light.paramEqua.x[0] + param.poly[i].face[j].planEqua.b * param.light.paramEqua.y[0] + param.poly[i].face[j].planEqua.c * param.light.paramEqua.z[0]));
			cpt++;
		}
	}
	//qsort(t + 1, t[0], sizeof(t), compare); // Ne marche pas
	sort(t); // trie le tableau par ordre croissant de valeur de t
	return t;
}

void equaParamLight(sParam *param, double X, double Y) { //retourne l'équation paramétrique du rayon de lumière qui passe par le pixel (X;Y) de l'image
	/*sFace planImage;
	planImage.nbPeaks = 0;
	planImage.planEqua.a = 1;
	planImage.planEqua.b = 0;
	planImage.planEqua.c = 0;
	planImage.planEqua.d = -1;*/
	double nWidth = (param->image.width) / 256;
	double nHeight = (param->image.height) / 256;
	param->light.paramEqua.x[1] = param->viewerPos.x;
	param->light.paramEqua.x[0] = 1; // x = 1 => plan image
	param->light.paramEqua.y[1] = param->viewerPos.y;
	param->light.paramEqua.y[0] = (nHeight / 2) - (X / 256);
	param->light.paramEqua.z[1] = param->viewerPos.z;
	param->light.paramEqua.z[0] = (nWidth / 2) - (Y / 256);
}

sPos* intersectLight(sParam param, double t) { // retourne l'intersection entre le rayon lumineux à la valeur t
	double x = param.light.paramEqua.x[0] * t + param.light.paramEqua.x[1];
	double y = param.light.paramEqua.y[0] * t + param.light.paramEqua.y[1];
	double z = param.light.paramEqua.z[0] * t + param.light.paramEqua.z[1];
	sPos *pos = (sPos*)malloc(sizeof(sPos));
	pos->x = x;
	pos->y = y;
	pos->z = z;
	return pos;
}

void* doesCollide(sParam param, double *t) { // le rayon de lumière entre t il en collision avec un des polygone ?
	for (int j = 1; j <= t[0]; j++) {
		for (int i = 0; i < param.nbPolyhedrons; i++) { // pour chaque face valeur de t stocké 
			sPosFace *pos = (sPosFace*)malloc(sizeof(sPosFace));
			pos->position = intersectLight(param, t[j]); // stock la position du point incident avec le plan actuel
			double theta = 0;
			for (int k = 0; k < param.poly[i].nbFaces; k++) { 
				for (int l = 0; l < param.poly[i].face[k].nbPeaks; l++) { // pour chaque sommet du polygone ciblé
					if (l + 1 < param.poly[i].face[k].nbPeaks) { // calcul de chaque angle orienté
						double xps = param.poly[i].face[k].peak[l].x - pos->position->x;
						double yps = param.poly[i].face[k].peak[l].y - pos->position->y;
						double zps = param.poly[i].face[k].peak[l].z - pos->position->z;
						double xpt = param.poly[i].face[k].peak[l + 1].x - pos->position->x;
						double ypt = param.poly[i].face[k].peak[l + 1].y - pos->position->y;
						double zpt = param.poly[i].face[k].peak[l + 1].z - pos->position->z;
						double lengthPs = pow(xps, 2) + pow(yps, 2) + pow(zps, 2);
						double lengthPt = pow(xpt, 2) + pow(ypt, 2) + pow(zpt, 2);
						theta += acos((xps*xpt + yps*ypt + zps*zpt) / sqrt(lengthPs*lengthPt));
					}
					else { // ce else est juste pour que l'angle entre le dernier sommet et le premier sommet de la liste soit calculé
						double xps = param.poly[i].face[k].peak[l].x - pos->position->x;
						double yps = param.poly[i].face[k].peak[l].y - pos->position->y;
						double zps = param.poly[i].face[k].peak[l].z - pos->position->z;
						double xpt = param.poly[i].face[k].peak[0].x - pos->position->x;
						double ypt = param.poly[i].face[k].peak[0].y - pos->position->y;
						double zpt = param.poly[i].face[k].peak[0].z - pos->position->z;
						double lengthPs = (pow(xps, 2) + pow(yps, 2) + pow(zps, 2));
						double lengthPt = (pow(xpt, 2) + pow(ypt, 2) + pow(zpt, 2));
						theta += acos((xps*xpt + yps*ypt + zps*zpt) / sqrt(lengthPs*lengthPt));
					}
				}
				theta /= 2 * PI;  //precision environ egale a 2.6646.10^-15
				//printf("%.16f\n", theta); //1.0471975511965976
				if (theta > 1.047197/*1.047197551196*/ && theta < 1.047198/*1.047197551197*/) {  // si l'angle est compris dans l'intervalle alors on retoune le numéro de la face et du polyhèdre qui lui est incident en plus de la positon du point d'intersection
					pos->iFace = k;
					pos->iPoly = i;
					return pos;
				}
				theta = 0;
			}
			free(pos);
		}
	}
	return false; // sinon on return false
}

#ifdef MORE

sColor pixelAvg(sColor pixel, sFile *imageStart, sFile *imageEnd, int w, int h, sParam param) { // fonction d'antialiasing, fais la moyenne des N pixel autour du pixel ciblé et crée la nouvelle image
	int averageR = 0;
	int averageG = 0;
	int averageB = 0;
	int nbColMax = param.image.width;
	int nbLigneMax = param.image.height;
	int n = 0;
	for (int i = w - ALIASING; i <= w + ALIASING; i++) {
		for (int j = h - ALIASING; j <= h + ALIASING; j++) {
			if ((i >= 0 && j >= 0) && i != nbColMax + 1 && j != nbLigneMax + 1 && (i != w || j != h)) {
				//if(imageStart->data[nbColMax * j + i].r - )
				averageR = averageR + imageStart->data[nbColMax * j + i].r;
				averageG = averageG + imageStart->data[nbColMax * j + i].g;
				averageB = averageB + imageStart->data[nbColMax * j + i].b;
				n++;
			}
		}
	}
	if (n != 0) {
		pixel.r = (int)(averageR / n);
		pixel.g = (int)(averageG / n);
		pixel.b = (int)(averageB / n);
		return pixel;
	}
}

double distBetweenTwoPoints(sPos pos1, sPos pos2) {
	double dist = sqrt(pow(pos2.x - pos1.x,2) + pow(pos2.y - pos1.y, 2) + pow(pos2.z - pos1.z, 2));
	return dist;
}

sColor mirrorFace(sParamEqua paramEqua, sParam param) {

	int cptPoly = -1;
	int cptFace = -1;
	int cptSphere = -1;
	int cptEllipse = -1;

	sPosFace *posPointObjet = NULL;
	sPosEllipse *posPointEllipse = NULL;

	double distance = 0;
	sColor p;
	float lightFactor = param.light.lightFactor;

	double *t = NULL;
	t = listingTimesWithParamEqua(param, paramEqua, t);

	if ((posPointObjet = doesCollide_PEUL(param, *t, paramEqua)) || (posPointEllipse = doesRayCollideWithAnyEllipse(param, paramEqua))) {
		if (posPointObjet && !posPointEllipse) {// juste l'objet
			cptPoly = posPointObjet->iPoly;
			cptFace = posPointObjet->iFace;
			if (param.poly[cptPoly].face[cptFace].isMirror) {
				sParamEqua paramEquaReflected;
				paramEquaReflected = isReflectedRay(param.light.paramEqua, param.poly[cptPoly].face[cptFace].planEqua);
				p = mirrorFace(paramEquaReflected, param);
				return p;
			}
			if (param.shadows) {
				if (isInTheShadow(*(posPointObjet->position), param)) {
					lightFactor -= 0.3;
				}
			}
			p.r = param.poly[cptPoly].face[cptFace].color.r * lightFactor;
			p.g = param.poly[cptPoly].face[cptFace].color.g * lightFactor;
			p.b = param.poly[cptPoly].face[cptFace].color.b * lightFactor;
			return p;
		}
		else if (posPointEllipse && !posPointObjet) {
			cptEllipse = posPointEllipse->iEllipse;
			if (param.shadows) {
				if (isInTheShadow(*(posPointEllipse->position), param)) {
					lightFactor -= 0.3;
				}
			}
			p.r = param.ellipse[cptEllipse].color.r * lightFactor;
			p.g = param.ellipse[cptEllipse].color.g * lightFactor;
			p.b = param.ellipse[cptEllipse].color.b * lightFactor;
			return p;
		}
		else {
			if (posPointObjet->position->x < posPointEllipse->position->x) {
				if (param.poly[cptPoly].face[cptFace].isMirror) {
					sParamEqua paramEquaReflected;
					paramEquaReflected = isReflectedRay(param.light.paramEqua, param.poly[cptPoly].face[cptFace].planEqua);
					p = mirrorFace(paramEquaReflected, param);
					return p;
				}
				p.r = param.poly[cptPoly].face[cptFace].color.r * lightFactor;
				p.g = param.poly[cptPoly].face[cptFace].color.g * lightFactor;
				p.b = param.poly[cptPoly].face[cptFace].color.b * lightFactor;
				return p;
			}
			else{
				p.r = param.ellipse[cptEllipse].color.r * lightFactor;
				p.g = param.ellipse[cptEllipse].color.g * lightFactor;
				p.b = param.ellipse[cptEllipse].color.b * lightFactor;
				return p;
			}
		}
		free(t);
		free(posPointObjet);
		free(posPointEllipse);
	}
	else {
		p.r = param.image.background.r;
		p.g = param.image.background.g;
		p.b = param.image.background.b;
		return p;
	}
}


int createImage(sPos posLight, sParam param, int CPT) {
	sFile* I = newBMP(param.image.width, param.image.height);
	int cptPoly = -1;
	int cptFace = -1;
	int cptSphere = -1;
	int cptEllipse = -1;
	for (int w = 1; w <= param.image.width; w++) {
		for (int h = 1; h <= param.image.height; h++) {
			double *t = NULL;
			equaParamLight(&param, w, h); ///////BOUCLE
			t = listingTimes(param, t);
			//showTab(t);
			sPosFace *posPointObjet = NULL;
			sPosEllipse *posPointEllipse = NULL;
			double distance = 0;
			sColor p;
			float lightFactor = param.light.lightFactor;
			//				posPointObjet = doesCollide(param, t);
			//				posPointSphere = doesCollideSphere(param); 
			//				distance = distBetweenTwoPoints(param.lightSource, *(posPointObjet->position));
			//				lightFactor *= ((INTENSITY - distance) / INTENSITY);

			if ((posPointObjet = doesCollide(param, t)) || (posPointEllipse = doesCollideEllipse(param))) {
				if (posPointObjet && !posPointEllipse) {// juste l'objet
					cptPoly = posPointObjet->iPoly;
					cptFace = posPointObjet->iFace;
					if (param.poly[cptPoly].face[cptFace].isMirror) {
						sParamEqua paramEquaReflected;
						paramEquaReflected = isReflectedRay(param.light.paramEqua, param.poly[cptPoly].face[cptFace].planEqua);
						p = mirrorFace(paramEquaReflected, param);
						setcolor(I, w - 1, h - 1, p);
					}
					else {
						if (param.shadows) {
							if (isInTheShadow(*(posPointObjet->position), param)) {
								lightFactor -= 0.3;
							}
						}
						p.r = param.poly[cptPoly].face[cptFace].color.r * lightFactor;
						p.g = param.poly[cptPoly].face[cptFace].color.g * lightFactor;
						p.b = param.poly[cptPoly].face[cptFace].color.b * lightFactor;
						setcolor(I, w - 1, h - 1, p);
					}
				}
				else if (posPointEllipse && !posPointObjet) {
					cptEllipse = posPointEllipse->iEllipse;
					if (param.shadows) {
						if (isInTheShadow(*(posPointEllipse->position), param)) {
							lightFactor -= 0.3;
						}
					}
					p.r = param.ellipse[cptEllipse].color.r * lightFactor;
					p.g = param.ellipse[cptEllipse].color.g * lightFactor;
					p.b = param.ellipse[cptEllipse].color.b * lightFactor;
					setcolor(I, w - 1, h - 1, p);
				}
				else{
					if (posPointObjet->position->x > posPointEllipse->position->x) {
						p.r = param.ellipse[cptEllipse].color.r * lightFactor;
						p.g = param.ellipse[cptEllipse].color.g * lightFactor;
						p.b = param.ellipse[cptEllipse].color.b * lightFactor;
						setcolor(I, w - 1, h - 1, p);
					}
					else {
						if (param.poly[cptPoly].face[cptFace].isMirror) {
							sParamEqua paramEquaReflected;
							paramEquaReflected = isReflectedRay(param.light.paramEqua, param.poly[cptPoly].face[cptFace].planEqua);
							mirrorFace(paramEquaReflected, param);
						}
						p.r = param.poly[cptPoly].face[cptFace].color.r * lightFactor;
						p.g = param.poly[cptPoly].face[cptFace].color.g * lightFactor;
						p.b = param.poly[cptPoly].face[cptFace].color.b * lightFactor;
						setcolor(I, w - 1, h - 1, p);
					}
				}
				free(t);
				free(posPointObjet);
				free(posPointEllipse);
			}
			else {
				p.r = param.image.background.r;
				p.g = param.image.background.g;
				p.b = param.image.background.b;
				setcolor(I, w - 1, h - 1, p);
			}
		}
	}
	char cptS[3];
	sprintf(cptS, "%d", CPT);
	if (param.antialiasing) {
		char nameJ[50];
		char path[50];
		strcpy(path, "Link\\");
		sFile* J = newBMP(param.image.width, param.image.height);
		sColor p;
		for (int w = 0; w < param.image.width; w++) {
			for (int h = 0; h < param.image.height; h++) {
				p = I->data[(int)param.image.height * w + h];
				p = pixelAvg(p, I, J, w, h, param);
				setcolor(J, w, h, p);
			}
		}
		strcpy(nameJ, param.image.name);
		strcat(nameJ, cptS);
		strcat(nameJ, "AA.bmp");
		strcat(path, nameJ);
		saveBMP(J, path);
		deleteBMP(J);
	}
	else {
		char nameI[50];
		char path[50];
		strcpy(path, "Link\\");
		strcpy(nameI, param.image.name);
		strcat(nameI, cptS);
		strcat(nameI, ".bmp");
		strcat(path, nameI);
		saveBMP(I, path);
		deleteBMP(I);
		return 1;
	}
}

#endif // MORE