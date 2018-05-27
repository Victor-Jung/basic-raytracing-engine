#define _CRT_SECURE_NO_WARNINGS
#include<stdio.h>
#include<stdlib.h>
#include<string.h>
#include "mainFunctions.h"
//#define FPF //FPF = (Frame Per Frame) -> utlise par defaut
//#define ANIMATION

int main() {

//#ifdef FPF
	sParam param;
	if (!loadFromFile(&param)) {
		//remove("data.txt");
		freeAll(&param);
		return 0;
	}
	//remove("data.txt");
	//showStruct(param); //aide au debug
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

	freeAll(&param);
	return 0;
/*#endif

#ifdef ANIMATION
	handmadeAnimation();
	return 0;
#endif
*/

}
