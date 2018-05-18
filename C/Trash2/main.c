#define _CRT_SECURE_NO_WARNINGS
#include<stdio.h>
#include<stdlib.h>
#include<string.h>
#include "mainFunctions.h"

int main() {
	sParam param;
	double *t = NULL;
	if (!loadFromFile(&param)) {
		return 0;
	}
	showStruct(param);
	t = listingTimes(param, t);
	system("pause");
	return 1;
}

/*int i, j;
unsigned int height = 800, width = 800;
image* I = newBMP(width, height);
for (i = 0; i<width; i++)
{
for (j = 0; j<height; j++)
{
color p;
p.r = 160;
p.g = 160;
p.b = 80;
setcolor(I, i, j, p);
}
}
//Image* I = Charger("test.bmp");
color p;
p.r = 255;
p.g = 255;
p.b = 255;
setcolor(I, 20, 52, p);
setcolor(I, 75, 24, p);
setcolor(I, 215, 127, p);
saveBMP(I, "test.bmp");
deleteBMP(I);*/