#pragma once
#include <stdbool.h>
#include "structure.h"

void showTab(double *t);

double* listingTimes(sParam param, double *t);

void* doesCollide(sParam param, double *t);

void equaParamLight(sParam *param, double X, double Y);

int createImage(sPos posLight, sParam param, int CPT);

void sort(double *t); // trie un tableau de valeurs double en partant de l'indice 1

int compare(double const *a, double const *b); //fonction de comparaison entre 2 double