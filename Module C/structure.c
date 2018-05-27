#include "structure.h"

void freeAll(sParam *param) {
	free(param->poly);
	free(param->image.name);
	free(param->sphere);
	free(param->ellipse);
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

int loadFromFile(sParam *param) {
	FILE *f = fopen("data.txt", "r");
	if (f == NULL) {
		return 0;
	}
	char line[100];
	int sizeFile = nbLine(f); //on recupere le nombre de lignes du fichier
	//fscanf(f, "%s", line);
	for (int i = 0; i < sizeFile; i++) {
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
		else if (!strcmp(line, "Video:")) {
			i++;
			fscanf(f, "%s", line);
			param->video.isTrue = atoi(line);
			if (param->video.isTrue) {
				i++;
				fscanf(f, "%s", line);
				i++;
				fscanf(f, "%s", line);
				param->video.frames = atoi(line);
				i++;
				fscanf(f, "%s", line);
				i++;
				fscanf(f, "%s", line);
				param->video.movement.x = atof(line);
				i++;
				fscanf(f, "%s", line);
				i++;
				fscanf(f, "%s", line);
				param->video.movement.y = atof(line);
				i++;
				fscanf(f, "%s", line);
				i++;
				fscanf(f, "%s", line);
				param->video.movement.z = atof(line);
			}
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
		else if (!strcmp(line, "Shadows:")) {
			i++;
			fscanf(f, "%s", line);
			param->shadows = atoi(line);
		}
		else if (!strcmp(line, "Antialiasing:")) {
			i++;
			fscanf(f, "%s", line);
			param->antialiasing = atoi(line);
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
								if (strcmp(line, "r:") && strcmp(line, "g:") && strcmp(line, "b:") && strcmp(line, "isMirror:")) {
									return 0;
								}
							}
							param->poly[j].face[l].color = colorTmp;
						}
						if (!strcmp(line, "isMirror:")) {
							fscanf(f, "%s", line);
							i++;
							param->poly[j].face[l].isMirror = atoi(line);
							fscanf(f, "%s", line);
							i++;
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
		else if (!strcmp(line, "NumberOfEllipse:")) {
			i++;
			fscanf(f, "%s", line);
			param->nbEllipse = atoi(line);
			param->ellipse = (sEllipse*)malloc(param->nbEllipse * sizeof(sEllipse));
			for (int j = 0; j < param->nbEllipse; j++) {
				i++;
				fscanf(f, "%s", line);
				char buffer[2];
				sprintf(buffer, "%d", j + 1);
				char searched[sizeName];
				strcpy(searched, "Ellipse");
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
						param->ellipse[j].color.r = atoi(line);
					}
					if (!strcmp(line, "g:")) {
						fscanf(f, "%s", line);
						i++;
						param->ellipse[j].color.g = atoi(line);
					}
					if (!strcmp(line, "b:")) {
						fscanf(f, "%s", line);
						i++;
						param->ellipse[j].color.b = atoi(line);
					}
					fscanf(f, "%s", line);
					i++;
					if (strcmp(line, "r:") && strcmp(line, "g:") && strcmp(line, "b:") && strcmp(line, "A:")) {
						return 0;
					}
				}
				fscanf(f, "%s", line);
				i++;
				param->ellipse[j].a = atof(line);
				fscanf(f, "%s", line);
				i++;
				fscanf(f, "%s", line);
				i++;
				param->ellipse[j].b = atof(line);
				fscanf(f, "%s", line);
				i++;
				fscanf(f, "%s", line);
				i++;
				param->ellipse[j].c = atof(line);
				fscanf(f, "%s", line);
				i++;
				fscanf(f, "%s", line);
				i++;
				param->ellipse[j].alpha = atof(line);
				fscanf(f, "%s", line);
				i++;
				fscanf(f, "%s", line);
				i++;
				param->ellipse[j].beta = atof(line);
				fscanf(f, "%s", line);
				i++;
				fscanf(f, "%s", line);
				i++;
				param->ellipse[j].gamma = atof(line);
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
	param->nbSpheres = 0;
	param->sphere = (sSphere*)malloc(0);
	return 1;
}