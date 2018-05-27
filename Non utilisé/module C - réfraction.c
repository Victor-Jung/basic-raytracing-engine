sPos findNormalisedVector(sPlanEqua planEqua) {//est utilise dans le module C
	sPos n;
	n.x = planEqua.a;
	n.y = planEqua.b;
	n.z = planEqua.c;

	return n;
}

double* calcAngleWithSnellDescartes(double *teta, sPos orientationVectorIncidentRay, sPos normalisedVector, double refractiveIndexA, double refractiveIndexB) {
	double scalarProduct = 0;
	teta = (double*)malloc(2 * sizeof(double));
	//calcule de teta 1
	//produit scalaire n . -u
	scalarProduct = (-1)*normalisedVector.x*orientationVectorIncidentRay.x + (-1)*normalisedVector.y*orientationVectorIncidentRay.y + (-1)*normalisedVector.z*orientationVectorIncidentRay.z;
	//formule calcule d'angle à partir de la formule du produit scalaire avec les normes et l'angle
	teta[0] = acos(scalarProduct / sqrt((pow(normalisedVector.x, 2) + pow(normalisedVector.y, 2) + pow(normalisedVector.z, 2))*(pow(orientationVectorIncidentRay.x, 2) + pow(orientationVectorIncidentRay.y, 2) + pow(orientationVectorIncidentRay.z, 2))));
	//calcule de teta 2
	//Formule Snell-Descartes
	teta[1] = asin((refractiveIndexA / refractiveIndexB)*sin(teta[0]));

	return teta;
}

int isTotallyReflected(double refractiveIndexA, double refractiveIndexB, double tetaA) {
	double test = 0;
	test = 1 - pow(refractiveIndexA / refractiveIndexB, 2)*pow(1 - cos(tetaA), 2);
	if (test < 0) {
		return 1;
	}
	return 0;
}

void* isRefractedRay(sParamEqua incidentRay, sFace face, double refractiveIndexA, double refractiveIndexB) {
	sPos pI;
	sPos normalisedVector; //vecteur normal "n"au plan (pointant vers l'exterieur)
	sPos orientationVectorIncidentRay; //vecteur directeur du rayon incident
	sPos orientationVectorRefractedRay;//vecteur directeur du rayon réfracté
	double *teta = NULL; //angles (incident et réfracté)
	sPlanEqua planEqua;
	sParamEqua refractedRay;

	planEqua.a = face.planEqua.a;
	planEqua.b = face.planEqua.b;
	planEqua.c = face.planEqua.c;
	planEqua.d = face.planEqua.d;

	//calcul des coordonnées de I
	double t = (-1)*(incidentRay.x[1] * planEqua.a + incidentRay.y[1] * planEqua.b + incidentRay.z[1] * planEqua.c + planEqua.d) / (incidentRay.x[0] * planEqua.a + incidentRay.y[0] * planEqua.b + incidentRay.z[0] * planEqua.c);
	pI.x = incidentRay.x[0] * t + incidentRay.x[1];
	pI.y = incidentRay.y[0] * t + incidentRay.y[1];
	pI.z = incidentRay.z[0] * t + incidentRay.z[1];

	//determination du vecteur normal au plan
	normalisedVector = findNormalisedVector(planEqua);
	orientationVectorIncidentRay.x = incidentRay.x[0];
	orientationVectorIncidentRay.y = incidentRay.y[0];
	orientationVectorIncidentRay.z = incidentRay.z[0];

	//calcule des angles incident et réfracté
	teta = calcAngleWithSnellDescartes(teta, orientationVectorIncidentRay, normalisedVector, refractiveIndexA, refractiveIndexB);

	//test de la réflexion complète
	if (isTotallyReflected) {
		return false;
	}

	//determination du vecteur directeur du rayon réfracté
	if (((-1)*normalisedVector.x*orientationVectorIncidentRay.x + (-1)*normalisedVector.y*orientationVectorIncidentRay.y + (-1)*normalisedVector.z*orientationVectorIncidentRay.z) >= 0) {
		orientationVectorRefractedRay.x = (refractiveIndexA / refractiveIndexB) * orientationVectorIncidentRay.x + ((refractiveIndexA / refractiveIndexB)*cos(teta[0]) - cos(teta[1]))*normalisedVector.x;
		orientationVectorRefractedRay.y = (refractiveIndexA / refractiveIndexB) * orientationVectorIncidentRay.y + ((refractiveIndexA / refractiveIndexB)*cos(teta[0]) - cos(teta[1]))*normalisedVector.y;
		orientationVectorRefractedRay.z = (refractiveIndexA / refractiveIndexB) * orientationVectorIncidentRay.z + ((refractiveIndexA / refractiveIndexB)*cos(teta[0]) - cos(teta[1]))*normalisedVector.z;
	}
	else {
		orientationVectorRefractedRay.x = (refractiveIndexA / refractiveIndexB) * orientationVectorIncidentRay.x + ((refractiveIndexA / refractiveIndexB)*cos(teta[0]) + cos(teta[1]))*normalisedVector.x;
		orientationVectorRefractedRay.y = (refractiveIndexA / refractiveIndexB) * orientationVectorIncidentRay.y + ((refractiveIndexA / refractiveIndexB)*cos(teta[0]) + cos(teta[1]))*normalisedVector.y;
		orientationVectorRefractedRay.z = (refractiveIndexA / refractiveIndexB) * orientationVectorIncidentRay.z + ((refractiveIndexA / refractiveIndexB)*cos(teta[0]) + cos(teta[1]))*normalisedVector.z;
	}

	//équation paramétrique du rayon réfracté
	refractedRay.x[0] = orientationVectorIncidentRay.x;
	refractedRay.x[1] = pI.x;
	refractedRay.x[0] = orientationVectorIncidentRay.y;
	refractedRay.x[1] = pI.y;
	refractedRay.x[0] = orientationVectorIncidentRay.z;
	refractedRay.x[1] = pI.z;
	free(teta);
	return &refractedRay;
}
