#define _CRT_SECURE_NO_WARNINGS
#include<stdio.h>
#include<stdlib.h>
#include<string.h>
#include "mainFunctions.h"
#define FPF //FPF = (Frame Per Frame)
//#define ANIMATION

int main() {

#ifdef FPF

	sParam param;
	if (!loadFromFile(&param)) {
		//remove("data.txt");
		freeAll(&param);
		return 0;
	}
	//remove("data.txt");
	showStruct(param);
	if (!(param.video.isTrue)) {
		if (createImage(param.lightSource, param, 0)) {
			freeAll(&param);
			return 1;
		}
	}
	else {
		for (int i = 1; i <= param.video.frames; i++) {
			createImage(param.lightSource, param, i);
			param.viewerPos.x += param.video.movement.x;
			param.viewerPos.y += param.video.movement.y;
			param.viewerPos.z += param.video.movement.z;
		}
	}
	return 0;

#endif

#ifdef ANIMATION

	sParam *param = (sParam*)malloc(sizeof(sParam));
	//sParam param;
	int nbOfFrames = 2;


	if (!loadFromFile(param)) { //on charge les paramètres
		freeAll(&param);
		return 0;
	}

	for(int CPT = 1; CPT <= nbOfFrames; CPT++) {

		if (!createImage(param->lightSource, *param, CPT)) {
			return 0;
		}

		//on modifie qq paramètres pour comme déplacer la caméra pour créer le mouvement

		param->viewerPos.z -= 2*CPT; //translation du sol et de l'observateur
		for (int i = 0; i < param->poly[0].face[10].nbPeaks; i++) {
			param->poly[0].face[10].peak[i].z -= 2; //face 11 = sol
		}
		//biblio pour ffmpeg : libavformat et libavcodec ainsi que ffmpegSourceCode
	}
#endif
}

