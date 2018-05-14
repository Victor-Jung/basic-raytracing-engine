#pragma once
#include <stdbool.h>
#include "structure.h"

int compare(void const *a, void const *b);

void showTab(double *t);

double* listingTimes(sParam param, double *t);

void* doesCollide(sParam param, double *t);