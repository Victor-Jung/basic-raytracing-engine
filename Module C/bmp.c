#define _CRT_SECURE_NO_WARNINGS
#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <assert.h>
#include "bmp.h"

sFile* newBMP(int w, int h)
{
	sFile* I = malloc(sizeof(sFile));  //on alloue la memoire.
	I->w = w; //on enregistre width et height dans la structure.
	I->h = h;
	I->data = calloc(1, w*h * sizeof(sColor*)); //on initialise les pixels a 0 0 0.
	return I;
}

sFile* copyBMP(sFile* I)
{
	sFile* res;
	if (!I)
		return NULL;
	res = newBMP(I->w, I->h);
	memcpy(res->data, I->data, I->w*I->h * sizeof(sColor)); //on copie la structure I dans une nouvelle.
	return res;												//pour la garder en memoire.
}

void deleteBMP(sFile* I)
{
	if (I)
	{
		free(I->data);
		free(I);
	}
}

void setcolor(sFile* I, int i, int j, sColor p)
{
	assert(I && i >= 0 && i<I->w && j >= 0 && j<I->h);  //on verifie la condition.
	I->data[I->w*j + i] = p;  //on set le pixel a sa position dans l'image.
}

sColor getcolor(sFile* I, int i, int j)
{
	assert(I && i >= 0 && i<I->w && j >= 0 && j<I->h);
	return I->data[I->w*j + i];  //on retourn la couleur du pixel a la position donnee
}

// -------------------------------------------

#pragma pack(1)  // desative l'alignement m�moire
typedef int int32;
typedef short int16;

struct BMPImHead
{
	int32 size_imhead;
	int32 width;
	int32 height;
	int16 nbplans; // toujours 1
	int16 bpp;
	int32 compression;
	int32 sizeim;
	int32 hres;
	int32 vres;
	int32 cpalette;
	int32 cIpalette;
};

struct BMPHead
{
	char signature[2];
	int32 taille;
	int32 rsv;
	int32 offsetim;
	struct BMPImHead imhead;
};

sFile* loadBMP(const char* fichier)
{
	struct BMPHead head;
	sFile* I;
	int i, j, pitch;
	unsigned char bgrpix[3];
	char corrpitch[4] = { 0,3,2,1 };
	sColor p;
	FILE* F = fopen(fichier, "rb");
	if (!F)
		return NULL;
	fread(&head, sizeof(struct BMPHead), 1, F);
	if (head.signature[0] != 'B' || head.signature[1] != 'M')
		return NULL;  // mauvaise signature, ou BMP non support�.
	if (head.imhead.bpp != 24)
		return NULL;  // supporte que le 24 bits pour l'instant
	if (head.imhead.compression != 0)
		return NULL;  // rarrissime, je ne sais m�me pas si un logiciel �crit/lit des BMP compress�s. 
	if (head.imhead.cpalette != 0 || head.imhead.cIpalette != 0)
		return NULL; // pas de palette support�e, cependant, a bpp 24, il n'y a pas de palette.
	I = newBMP(head.imhead.width, head.imhead.height);
	pitch = corrpitch[(3 * head.imhead.width) % 4];
	for (j = 0; j<I->h; j++)
	{
		for (i = 0; i<I->w; i++)
		{
			fread(&bgrpix, 1, 3, F);
			p.r = bgrpix[2];
			p.g = bgrpix[1];
			p.b = bgrpix[0];
			setcolor(I, i, I->h - j - 1, p);
		}
		fread(&bgrpix, 1, pitch, F);
	}
	fclose(F);
	return I;
}

int saveBMP(sFile* I, const char* fichier)
{
	struct BMPHead head;
	sColor p;
	int i, j, tailledata, pitch;
	unsigned char bgrpix[3];
	char corrpitch[4] = { 0,3,2,1 };
	FILE* F = fopen(fichier, "wb");
	if (!F)
		return -1;
	memset(&head, 0, sizeof(struct BMPHead));
	head.signature[0] = 'B';
	head.signature[1] = 'M';
	head.offsetim = sizeof(struct BMPHead); // je vais toujours �crire sur le m�me moule.
	head.imhead.size_imhead = sizeof(struct BMPImHead);
	head.imhead.width = I->w;
	head.imhead.height = I->h;
	head.imhead.nbplans = 1;
	head.imhead.bpp = 24;
	pitch = corrpitch[(3 * head.imhead.width) % 4];
	tailledata = 3 * head.imhead.height*head.imhead.width + head.imhead.height*pitch;
	head.imhead.sizeim = tailledata;
	head.taille = head.offsetim + head.imhead.sizeim;
	fwrite(&head, sizeof(struct BMPHead), 1, F);
	for (j = 0; j<I->h; j++)
	{
		for (i = 0; i<I->w; i++)
		{
			p = getcolor(I, i, I->h - j - 1);
			bgrpix[0] = p.b;
			bgrpix[1] = p.g;
			bgrpix[2] = p.r;
			fwrite(&bgrpix, 1, 3, F);
		}
		bgrpix[0] = bgrpix[1] = bgrpix[2] = 0;
		fwrite(&bgrpix, 1, pitch, F);
	}
	fclose(F);
	return 0;
}