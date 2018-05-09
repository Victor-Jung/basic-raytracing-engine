#pragma once

#ifndef _BMP_H
#define _BMP_H

typedef struct pixel
{
	unsigned char r, g, b;
} pixel;

typedef struct image
{
	int w, h;
	pixel* dat;
} image;

image* loadBMP(const char* fichier);
int saveBMP(image*, const char* fichier);
image* newBMP(int w, int h);
image* copyBMP(image*);
void setPixel(image*, int i, int j, pixel p);
pixel getPixel(image*, int i, int j);
void deleteBMP(image*);

#endif