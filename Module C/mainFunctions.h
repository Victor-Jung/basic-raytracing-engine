#pragma once
#include "structure.h"
#include "mainFunctions_PEUL.h"
#define ALIASING 1
#define PI 3,1415926535
#define INTENSITY 90 //donne le nombre maximum d'unité où la lumière va se propager, si le rendu est trop sombre
#define MORE		 //il faut augmenter la valeur et vice versa.


double* listingTimes(sParam param, double *t);

void* doesCollide(sParam param, double *t);

void equaParamLight(sParam *param, double X, double Y);

int createImage(sPos posLight, sParam param, int CPT);

void sort(double *t); // trie un tableau de valeurs double en partant de l'indice 1

int compare(double const *a, double const *b); //fonction de comparaison entre 2 double