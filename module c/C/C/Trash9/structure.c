#define _CRT_SECURE_NO_WARNINGS
#include "structure.h"
#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#define sizeName 30


void freeAll(sParam *param) {
	free(param->poly);
	free(param->image.name);
	free(param->sphere);
}

int nbLine(FILE* f) {
	int c;
	int line = 0;
	while ((c=fgetc(f)) != EOF) {
		if (c == '\n') {
			++line;
		}
	}
	rewind(f);
	return line;
}


sVect vect(sPos a, sPos b) {
	sVect c;
	c.x = b.x - a.x;
	c.y = b.y - a.y;
	c.z = b.z - a.z;
	return c;
}

sVect produitVect3d(sVect a, sVect b) {
	sVect c;
	c.x = a.y * b.z - a.z * b.y;
	c.y = a.z * b.x - a.x * b.z;
	c.z = a.x * b.y - a.y * b.x;
	return c;
}

sPlanEqua planEqua(sParam *param, int iObj, int iFace) {
	int iPeakA = 0, iPeakB = 1, iPeakC = 2;
	sVect AC;
	sVect AB;
	AC = vect(param->poly[iObj].face[iFace].peak[iPeakA], param->poly[iObj].face[iFace].peak[iPeakC]);
	AB = vect(param->poly[iObj].face[iFace].peak[iPeakA], param->poly[iObj].face[iFace].peak[iPeakB]);
	sVect res = produitVect3d(AC, AB);
	float d = res.x*param->poly[iObj].face[iFace].peak[iPeakA].x + res.y*param->poly[iObj].face[iFace].peak[iPeakA].y + res.z*param->poly[iObj].face[iFace].peak[iPeakA].z;
	param->poly[iObj].face[iFace].planEqua.a = res.x;
	param->poly[iObj].face[iFace].planEqua.b = res.y;
	param->poly[iObj].face[iFace].planEqua.c = res.z;
	param->poly[iObj].face[iFace].planEqua.d = -d;
	return param->poly[iObj].face[iFace].planEqua;
}

void showStruct(sParam param) {
	printf("Name: %s\n", param.image.name);
	printf("Height: %f\n", param.image.height);
	printf("Width: %f\n", param.image.width);
	printf("Background-r: %d\n", param.image.background.r);
	printf("Background-g: %d\n", param.image.background.g);
	printf("Background-b: %d\n", param.image.background.b);
	printf("Light Factor: %f\n", param.light.lightFactor);
	printf("LightPosition:\n	x: %f\n	y: %f\n	z:%f\n", param.lightSource.x, param.lightSource.y, param.lightSource.z);
	printf("ViewerPosition:\n	x: %f\n	y: %f\n	z:%f\n", param.viewerPos.x, param.viewerPos.y, param.viewerPos.z);
	printf("Nb Objects: %d\n", param.nbPolyhedrons);
	for (int i = 0; i < param.nbPolyhedrons; i++) {
		printf("Object %d:\n", i + 1);
		printf("	Formula:\n");
		for (int j = 1; j <= param.poly[i].nbFaces; j++) {
			printf("	Plan Equation %d:\n", j);
			printf("		a%d: %f\n", j, param.poly[i].face[j - 1].planEqua.a);
			printf("		b%d: %f\n", j, param.poly[i].face[j - 1].planEqua.b);
			printf("		c%d: %f\n", j, param.poly[i].face[j - 1].planEqua.c);
			printf("		d%d: %f\n", j, param.poly[i].face[j - 1].planEqua.d);
			printf("		Color:\n");
			printf("			r: %d", param.poly[i].face[j - 1].color.r);
			printf("			g: %d", param.poly[i].face[j - 1].color.g);
			printf("			b: %d\n", param.poly[i].face[j - 1].color.b);
			printf("	Peaks(%d):\n", param.poly[i].face[j - 1].nbPeaks);
			for (int k = 0; k < param.poly[i].face[j - 1].nbPeaks; k++) {
				printf("		x%d: %f\n", j + 1, param.poly[i].face[j - 1].peak[k].x);
				printf("		y%d: %f\n", j + 1, param.poly[i].face[j - 1].peak[k].y);
				printf("		z%d: %f\n", j + 1, param.poly[i].face[j - 1].peak[k].z);
			}
		}
	}
	printf("Number of spheres : %d\n", param.nbSpheres);
	for (int i = 0; i < param.nbSpheres; i++) {
		printf("	Color:\n		r : %d\n		g : %d\n		b : %d\n", param.sphere[i].color.r, param.sphere[i].color.g, param.sphere[i].color.b);
		printf("	Center:\n		x : %f\n		y : %f\n		z : %f\n", param.sphere[i].center.x, param.sphere[i].center.y, param.sphere[i].center.z);
		printf("	Radius : %f\n", param.sphere[i].r);
	}
}

int loadFromFile(sParam *param) {
	FILE *f = fopen("data.txt", "r");
	if (f == NULL) {
		return 0;
	}
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
		else if (!strcmp(line, "LightPosition:")) {
			i++;
			fscanf(f, "%s", line);
			i++;
			fscanf(f, "%s", line);
			param->lightSource.x = atof(line);
			i++;
			fscanf(f, "%s", line);
			i++;
			fscanf(f, "%s", line);
			param->lightSource.y = atof(line);
			i++;
			fscanf(f, "%s", line);
			i++;
			fscanf(f, "%s", line);
			param->lightSource.z = atof(line);
		}
		else if (!strcmp(line, "ViewerPosition:")) {
			i++;
			fscanf(f, "%s", line);
			i++;
			fscanf(f, "%s", line);
			param->viewerPos.x = atof(line);
			i++;
			fscanf(f, "%s", line);
			i++;
			fscanf(f, "%s", line);
			param->viewerPos.y = atof(line);
			i++;
			fscanf(f, "%s", line);
			i++;
			fscanf(f, "%s", line);
			param->viewerPos.z = atof(line);
		}
		else if (!strcmp(line, "Polyhedron:")) {
			i++;
			fscanf(f, "%s", line);
			param->nbPolyhedrons = atoi(line);
			param->poly = (sPoly*)malloc(param->nbPolyhedrons * sizeof(sPoly));
			for (int j = 0; j < param->nbPolyhedrons; j++) {
				fscanf(f, "%s", line);
				i++;
				char buffer[2];
				sprintf(buffer, "%d", j + 1);
				char searched[sizeName];
				strcpy(searched, "Polyhedron");
				strcat(searched, buffer);
				strcat(searched, ":");
				if (strcmp(line, searched)) {
					return 0;
				}
				fscanf(f, "%s", line);
				i++;
				if (!strcmp(line, "NumberOfFaces:")) {
					fscanf(f, "%s", line);
					i++;
					param->poly[j].nbFaces = atoi(line);
					param->poly[j].face = (sFace*)malloc(param->poly[j].nbFaces * sizeof(sFace));
					for (int l = 0; l < param->poly[j].nbFaces; l++) {
						fscanf(f, "%s", line);
						i++;
						char buffer[sizeName];
						strcpy(buffer, "Face");
						char tmp[sizeName];
						sprintf(tmp, "%d", l + 1);
						strcat(buffer, tmp);
						strcat(buffer, ":");
						if (!strcmp(line, buffer)) {
							fscanf(f, "%s", line);
							i++;
							if (strcmp(line, "Color:")) {
								return 0;
							}
							fscanf(f, "%s", line);
							i++;
							sColor colorTmp;
							for (int k = 0; k < 3; k++) {
								if (!strcmp(line, "r:")) {
									fscanf(f, "%s", line);
									i++;
									colorTmp.r = atoi(line);
								}
								if (!strcmp(line, "g:")) {
									fscanf(f, "%s", line);
									i++;
									colorTmp.g = atoi(line);
								}
								if (!strcmp(line, "b:")) {
									fscanf(f, "%s", line);
									i++;
									colorTmp.b = atoi(line);
								}
								fscanf(f, "%s", line);
								i++;
								if (strcmp(line, "r:") && strcmp(line, "g:") && strcmp(line, "b:") && strcmp(line, "Numberofpeaks:")) {
									return 0;
								}
							}
							param->poly[j].face[l].color = colorTmp;
						}
						if (!strcmp(line, "Numberofpeaks:")) {
							fscanf(f, "%s", line);
							i++;
							int nbPeaks = atoi(line);
							param->poly[j].face[l].peak = (sPos*)malloc(nbPeaks * sizeof(sPos));
							param->poly[j].face[l].nbPeaks = nbPeaks;
							for (int k = 0; k < nbPeaks; k++) {
								fscanf(f, "%s", line);
								i++;
								fscanf(f, "%s", line);
								i++;
								param->poly[j].face[l].peak[k].x = atof(line);
								fscanf(f, "%s", line);
								i++;
								fscanf(f, "%s", line);
								i++;
								param->poly[j].face[l].peak[k].y = atof(line);
								fscanf(f, "%s", line);
								i++;
								fscanf(f, "%s", line);
								i++;
								param->poly[j].face[l].peak[k].z = atof(line);
							}
						}
					}
				}
			}
		}
		else if (!strcmp(line, "NumberOfSpheres:")) {
			i++;
			fscanf(f, "%s", line);
			param->nbSpheres = atoi(line);
			param->sphere = (sSphere*)malloc(param->nbSpheres * sizeof(sSphere));
			for (int j = 0; j < param->nbSpheres; j++) {
				i++;
				fscanf(f, "%s", line); 
				char buffer[2];
				sprintf(buffer, "%d", j + 1);
				char searched[sizeName];
				strcpy(searched, "Sphere");
				strcat(searched, buffer);
				strcat(searched, ":");
				if (strcmp(searched, line)) {
					return 0;
				}
				fscanf(f, "%s", line);
				i++;
				if (strcmp(line, "Color:")) {
					return 0;
				}
				fscanf(f, "%s", line);
				i++;
				for (int k = 0; k < 3; k++) {
					if (!strcmp(line, "r:")) {
						fscanf(f, "%s", line);
						i++;
						param->sphere[j].color.r = atoi(line);
					}
					if (!strcmp(line, "g:")) {
						fscanf(f, "%s", line);
						i++;
						param->sphere[j].color.g = atoi(line);
					}
					if (!strcmp(line, "b:")) {
						fscanf(f, "%s", line);
						i++;
						param->sphere[j].color.b = atoi(line);
					}
					fscanf(f, "%s", line);
					i++;
					if (strcmp(line, "r:") && strcmp(line, "g:") && strcmp(line, "b:") && strcmp(line, "Center:")) {
						return 0;
					}
				}
				fscanf(f, "%s", line);
				i++;
				fscanf(f, "%s", line);
				i++;
				param->sphere[j].center.x = atof(line);
				fscanf(f, "%s", line);
				i++;
				fscanf(f, "%s", line);
				i++;
				param->sphere[j].center.y= atof(line);
				fscanf(f, "%s", line);
				i++;
				fscanf(f, "%s", line);
				i++;
				param->sphere[j].center.z= atof(line);
				fscanf(f, "%s", line);
				i++;
				if (strcmp(line, "Radius:")) {
					return 0;
				}
				fscanf(f, "%s", line);
				i++;
				param->sphere[j].r = atof(line);
			}
		}
		else { 
			return 0; 
		}
	}
	for (int i = 0; i < param->nbPolyhedrons; i++) {
		for (int j = 0; j < param->poly[i].nbFaces; j++) {
			param->poly[i].face[j].planEqua = planEqua(param, i, j);
		}
	}
	return 1;
}