#define _CRT_SECURE_NO_WARNINGS
#include "structure.h"
#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#define sizeName 30

int nbLine(FILE* f) {
	int c;
	int line = 0;
	while ((c = getc(f)) != EOF) {
		if (c == '\n')
			++line;
	}
	rewind(f);
	return line;
}

void showStruct(sParam param) {
	printf("Name: %s\n", param.image.name);
	printf("Height: %d\n", param.image.height);
	printf("Width: %d\n", param.image.width);
	printf("Background-r: %d\n", param.image.background.r);
	printf("Background-g: %d\n", param.image.background.g);
	printf("Background-b: %d\n", param.image.background.b);
	printf("Nb Objects: %d\n", param.nbObjects);
	printf("Light Factor: %f\n", param.light.lightFactor);
	for (int i = 0; i < param.nbObjects; i++) {
		printf("Object %d:\n", param.nbObjects);
		for (int j = 0; j < param.object[i].formula.nbX; j++) {
			printf("	x^%d: %f\n", j + 1, param.object[i].formula.x[j]);
		}
		for (int j = 0; j < param.object[i].formula.nbY; j++) {
			printf("	y^%d: %f\n", j + 1, param.object[i].formula.y[j]);
		}
		for (int j = 0; j < param.object[i].formula.nbZ; j++) {
			printf("	z^%d: %f\n", j + 1, param.object[i].formula.z[j]);
		}
		for (int j = 0; j < param.object[i].nbPeaks; j++) {
			printf("	x%d: %f\n", j + 1, param.object[i].peak[j].x);
			printf("	y%d: %f\n", j + 1, param.object[i].peak[j].y);
			printf("	z%d: %f\n", j + 1, param.object[i].peak[j].z);
		}
	}

}

int loadFromFile(sParam *param) {
	FILE *f = fopen("data.txt", "r");
	char line[100];
	int sizeFile = nbLine(f); //on recupere le nombre de lignes du fichier
	fscanf(f, "%s", line);
	for(int i = 0; i < sizeFile; i++) {
		fscanf(f, "%s", line);
		if (!strcmp(line, "Name:")) {
			i++;
			fscanf(f, "%s", line);
			param->image.name = _strdup(line);
		}
		else if (!strcmp(line, "Height:")) {
			i++;
			fscanf(f, "%s", line);
			param->image.height = atoi(line);
		}
		else if (!strcmp(line, "Width:")) {
			i++;
			fscanf(f, "%s", line);
			param->image.width = atoi(line);
		}
		else if (!strcmp(line, "Background-color:")) {
			i++;
			for (int j = 0; j < 3; j++) {
				fscanf(f, "%s", line);
				i++;
				if (!strcmp(line, "r:")) {
					fscanf(f, "%s", line);
					i++;
					param->image.background.r = atoi(line);
				}
				else if (!strcmp(line, "g:")) {
					fscanf(f, "%s", line);
					i++;
					param->image.background.g = atoi(line);
				}
				else if (!strcmp(line, "b:")) {
					fscanf(f, "%s", line);
					i++;
					param->image.background.b = atoi(line);
				}
			}
		}
		else if (!strcmp(line, "Brightness:")) {
			i++;
			fscanf(f, "%s", line);
			param->light.lightFactor = atof(line);
		}
		else if (!strcmp(line, "Numberofobjects:")) {
			i++;
			fscanf(f, "%s", line);
			param->nbObjects = atoi(line);
			param->object = (sObject*)malloc(param->nbObjects*sizeof(sObject));
			for (int j = 0; j < param->nbObjects; j++) {
				fscanf(f, "%s", line);
				i++;
				char buffer[2];
				sprintf(buffer, "%d", j + 1);
				char searched[sizeName];
				strcpy(searched, "Object");
				strcat(searched, buffer);
				strcat(searched, ":");
				if (strcmp(line, searched)) {
					return 0;
				}
				fscanf(f, "%s", line);
				i++;
				fscanf(f, "%s", line);
				i++;
				for (int k = 0; k < 3; k++) {
					fscanf(f, "%s", line);
					i++;
					if (k > 0) {
						fscanf(f, "%s", line);
						i++;
					}
					int l = atoi(line);
					if (k == 0) {
						param->object[j].formula.x = (double*)malloc(l * sizeof(double));
						param->object[j].formula.nbX = l;
					}
					if (k == 1) {
						param->object[j].formula.y = (double*)malloc(l * sizeof(double));
						param->object[j].formula.nbY = l;
					}
					if (k == 2) {
						param->object[j].formula.z = (double*)malloc(l * sizeof(double));
						param->object[j].formula.nbZ = l;
					}
					int cpy = l;
					for (l; l > 0; l--) {
						fscanf(f, "%s", line);
						i++;
						char points[sizeName], nb[sizeName];
						strcpy(points, ":");
						sprintf(nb, "%d", cpy - l + 1);
						if (!strcmp(line, strcat(nb, points))) {
							fscanf(f, "%s", line);
							i++;
							if (k == 0) {
								param->object[j].formula.x[cpy-l] = atof(line);
							}
							if (k == 1) {
								param->object[j].formula.y[cpy - l] = atof(line);
							}
							if (k == 2) {
								param->object[j].formula.z[cpy - l] = atof(line);
							}
						}
					}
				}
				fscanf(f, "%s", line);
				i++;
				if (!strcmp(line, "Numberofpeaks:")) {
					fscanf(f, "%s", line);
					i++;
					int nbPeaks = atoi(line);
					param->object[j].peak = (sPos*)malloc(nbPeaks * sizeof(sPos));
					param->object[j].nbPeaks = nbPeaks;
					for (int k = 0; k < nbPeaks; k++) {
						fscanf(f, "%s", line);
						i++;
						fscanf(f, "%s", line);
						i++;
						param->object[j].peak[k].x = atof(line);
						fscanf(f, "%s", line);
						i++;
						fscanf(f, "%s", line);
						i++;
						param->object[j].peak[k].y = atof(line);
						fscanf(f, "%s", line);
						i++;
						fscanf(f, "%s", line);
						i++;
						param->object[j].peak[k].z = atof(line);
					}
				}
			}
		}
		else { 
			return 0; 
		}
	}
	return 1;
}