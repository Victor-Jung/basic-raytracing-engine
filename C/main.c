#define _CRT_SECURE_NO_WARNINGS
#include<stdio.h>
#include<stdlib.h>
#include<string.h>
#include "mainFunctions.h"

int main() {
	/*int i, j;
	unsigned int height = 1000, width = 1000;
	sFile* I = newBMP(width, height);
	for (i = 0; i<width; i++)
	{
		for (j = 0; j<height; j++)
		{
			sColor p;
			p.r = 255;
			p.g = 255-i*j*cos(j*i);
			p.b = 255*sin(i*j);
			setcolor(I, i, j, p);
		}
	}
	//sFile* I = Charger("test.bmp");
	sColor p;
	p.r = 255;
	p.g = 255;
	p.b = 255;
	setcolor(I, 20, 52, p);
	setcolor(I, 75, 24, p);
	setcolor(I, 215, 127, p);
	saveBMP(I, "test.bmp");
	deleteBMP(I);
	return 0;*/
	sParam param;
	if (!loadFromFile(&param)) {
		freeAll(&param);
		return 0;
	}
	showStruct(param);
	if (createImage(param.lightSource, param)) {
		freeAll(&param);
		return 1;
	}
	return 0;
}

