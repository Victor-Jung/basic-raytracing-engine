//fonction anciennement utilisee dans mainFunctions.c
void showTab(double *t) { //fonction de débog du tableau de t
	for (int i = 1; i <= t[0]; i++) {
		printf("%.15f ", t[i]);
	}
	printf("\n");
}

//fonction anciennement utilisee dans structure.c
void showStruct(sParam param) {//fonction de débug de la lecture en fichier
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
	printf("Number of ellipses : %d\n", param.nbEllipse);
	for (int i = 0; i < param.nbEllipse; i++) {
		printf("	Color:\n		r : %d\n		g : %d\n		b : %d\n", param.ellipse[i].color.r, param.ellipse[i].color.g, param.ellipse[i].color.b);
		printf("	Center:\n		x : %f\n		y : %f\n		z : %f\n", param.ellipse[i].a, param.ellipse[i].b, param.ellipse[i].c);
		printf("	Radius :\n 		a : %f\n 		b : %f\n		c : %f\n", param.ellipse[i].alpha, param.ellipse[i].beta, param.ellipse[i].gamma);
	}
}

//fonction anciennement utilisee dans mainFunctions.c
double distBetweenTwoPoints(sPos pos1, sPos pos2) { 
	double dist = sqrt(pow(pos2.x - pos1.x,2) + pow(pos2.y - pos1.y, 2) + pow(pos2.z - pos1.z, 2));
	return dist;
}

//fonction inutilisee dans mainFunctions_PEUL.c
int testTvalueFromParamEqua(sPos pos, sParamEqua paramEqua) { // test si un point appartient à l'equation paramétrique
	double t[3];
	int xTrue = 0, yTrue = 0, zTrue = 0; // bcp de conditions pour éviter de diviser par 0 :(
	if (paramEqua.x[0] != 0) {
		t[0] = (pos.x - paramEqua.x[1]) / paramEqua.x[0];
		xTrue = 1;
	}
	if (paramEqua.y[0] != 0) {
		t[1] = (pos.y - paramEqua.y[1]) / paramEqua.y[0];
		yTrue = 1;
	}
	if (paramEqua.z[0] != 0) {
		t[2] = (pos.z - paramEqua.z[1]) / paramEqua.z[0];
		zTrue = 1;
	}

	if (((t[0] != t[1]) && !zTrue) || (!zTrue && !yTrue && xTrue) || ((t[0] != t[2]) && !yTrue) || (!yTrue && !xTrue && zTrue) || ((t[2] != t[1]) && !xTrue) || (!xTrue && !zTrue && yTrue)) {
		return 0;
	}

	return 1;
}

//fonction inutilisee dans mainFunctions_PEUL.c
sPlanEqua makeTangentPlanFromEllipse(sEllipse ellipse, sPos collisionPoint) {
	sPlanEqua tangentPlan;
	sPos gradientVectorAtCollisionPoint;

	gradientVectorAtCollisionPoint.x = 2 * (collisionPoint.x - ellipse.a) / pow(ellipse.alpha, 2);
	gradientVectorAtCollisionPoint.y = 2 * (collisionPoint.y - ellipse.b) / pow(ellipse.beta, 2);
	gradientVectorAtCollisionPoint.z = 2 * (collisionPoint.z - ellipse.c) / pow(ellipse.gamma, 2);

	tangentPlan.a = gradientVectorAtCollisionPoint.x;
	tangentPlan.b = gradientVectorAtCollisionPoint.y;
	tangentPlan.c = gradientVectorAtCollisionPoint.z;

	tangentPlan.d = (-1)*(tangentPlan.a*collisionPoint.x + tangentPlan.b*collisionPoint.y + tangentPlan.c*collisionPoint.z);


	return tangentPlan;
}

//fonction anciennement utilisée dans main.c
void handmadeAnimation() {
	sParam *param = (sParam*)malloc(sizeof(sParam));
	//sParam param;
	int nbOfFrames = 2;


	if (!loadFromFile(param)) { //on charge les param�tres
		freeAll(&param);
		return 0;
	}

	for(int CPT = 1; CPT <= nbOfFrames; CPT++) {

		if (!createImage(param->lightSource, *param, CPT)) {
			return 0;
		}

		//on modifie qq param�tres pour comme d�placer la cam�ra pour cr�er le mouvement

		param->viewerPos.z -= 2*CPT; //translation du sol et de l'observateur
		for (int i = 0; i < param->poly[0].face[10].nbPeaks; i++) {
			param->poly[0].face[10].peak[i].z -= 2; //face 11 = sol
		}
		//biblio pour ffmpeg : libavformat et libavcodec ainsi que ffmpegSourceCode
	}
	
	freeAll(&param);
}