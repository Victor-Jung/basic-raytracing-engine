#pragma once
#include <stdbool.h>
#include "structure.h"

void showTab(double *t);

double* listingTimes(sParam param, double *t);

void* doesCollide(sParam param, double *t);

void equaParamLight(sParam *param, double X, double Y);

int createImage(sPos posLight, sParam param);

void sort(double *t);