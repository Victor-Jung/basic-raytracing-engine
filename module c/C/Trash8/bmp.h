#pragma once

typedef struct sColor_ sColor;
struct sColor_ {
	unsigned char r, g, b;
};

typedef struct sFile_ sFile;
struct sFile_ {
	int w, h;
	sColor* data;
};

sFile* loadBMP(const char* fichier);
int saveBMP(sFile*, const char* fichier);
sFile* newBMP(int w, int h);
sFile* copyBMP(sFile*);
void setcolor(sFile*, int i, int j, sColor p);
sColor getcolor(sFile*, int i, int j);
void deleteBMP(sFile*);