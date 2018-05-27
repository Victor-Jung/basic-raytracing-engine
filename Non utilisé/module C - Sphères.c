sPlanEqua makeTangentPlanFromSphere(sPos collisionPoint, sPos centerOfSphere) {
	sPos radiusVector;
	sPlanEqua tangentPlan;

	radiusVector.x = collisionPoint.x - centerOfSphere.x;
	radiusVector.y = collisionPoint.y - centerOfSphere.y;
	radiusVector.z = collisionPoint.z - centerOfSphere.z;

	tangentPlan.a = radiusVector.x;
	tangentPlan.b = radiusVector.y;
	tangentPlan.c = radiusVector.z;

	tangentPlan.d = (tangentPlan.a*collisionPoint.x + tangentPlan.b*collisionPoint.y + tangentPlan.c*collisionPoint.z) * (-1);

	return tangentPlan;
}

void* doesRayCollideWithAnySphere(sParam param, sParamEqua paramEqua) {// le but est de renvoyer 1 si l'eq paramétrique touche une sphère avec t > 0 et t < 1
	double alpha, beta, gamma, delta;
	double t = 0, t1 = 0, t2 = 0;
	for (int iSphere = 0; iSphere < param.nbSpheres; iSphere++) {
		alpha = pow(paramEqua.x[0], 2) + pow(paramEqua.y[0], 2) + pow(paramEqua.z[0], 2);
		beta = (2 * paramEqua.x[0] * paramEqua.x[1]) - (2 * paramEqua.x[0] * param.sphere[iSphere].center.x) + (2 * paramEqua.y[0] * paramEqua.y[1]) - (2 * paramEqua.y[0] * param.sphere[iSphere].center.y) + (2 * paramEqua.z[0] * paramEqua.z[1]) - (2 * paramEqua.z[0] * param.sphere[iSphere].center.z);
		gamma = (pow(paramEqua.x[1], 2) - 2 * paramEqua.x[1] * param.sphere[iSphere].center.x + pow(param.sphere[iSphere].center.x, 2)) + (pow(paramEqua.y[1], 2) - 2 * paramEqua.y[1] * param.sphere[iSphere].center.y + pow(param.sphere[iSphere].center.y, 2)) + (pow(paramEqua.z[1], 2) - 2 * paramEqua.z[1] * param.sphere[iSphere].center.z + pow(param.sphere[iSphere].center.z, 2)) - pow(param.sphere[iSphere].r, 2);
		delta = pow(beta, 2) - (4 * alpha*gamma);
		
		if (delta > 0.01) {
			sPosSphere* intersectionPoint = NULL;
			intersectionPoint = (sPosSphere*)malloc(sizeof(sPosSphere));
			intersectionPoint->position = (sPos*)malloc(sizeof(sPos));
			t1 = (-beta - sqrt(delta)) / (2 * alpha);
			t2 = (-beta + sqrt(delta)) / (2 * alpha);
			if (t1 > t2) {
				t = t1;
			}
			else if (t2 >= t1) {
				t = t2;
			}
			else {
				return false;
			}
			if (t > 0.01 && t <= 1) {
				intersectionPoint->position->x = param.light.paramEqua.x[0] * t + param.light.paramEqua.x[1];
				intersectionPoint->position->y = param.light.paramEqua.y[0] * t + param.light.paramEqua.y[1];
				intersectionPoint->position->z = param.light.paramEqua.z[0] * t + param.light.paramEqua.z[1];
				intersectionPoint->iSphere = iSphere;
				return intersectionPoint;
			}
		}
		else if (delta > -0.01) {
			sPosSphere* intersectionPoint = NULL;
			intersectionPoint = (sPosSphere*)malloc(sizeof(sPosSphere));
			intersectionPoint->position = (sPos*)malloc(sizeof(sPos));
			t = -beta / (2 * alpha);
			if (t > 0.01 && t <= 1) {
				intersectionPoint->position->x = param.light.paramEqua.x[0] * t + param.light.paramEqua.x[1];
				intersectionPoint->position->y = param.light.paramEqua.y[0] * t + param.light.paramEqua.y[1];
				intersectionPoint->position->z = param.light.paramEqua.z[0] * t + param.light.paramEqua.z[1];
				intersectionPoint->iSphere = iSphere;
				return intersectionPoint;
			}
		}
	}
	return false;
}

void* doesCollideSphere(sParam param) {
	for (int iSphere = 0; iSphere < param.nbSpheres; iSphere++) {
		double alpha, beta, gamma, delta;
		double t = 0;
		//equation de la sphere devient (A²+B²+C²)t² + 2(A(a-x0) + B(b-y0) + C(c-z0))t + (x0-2a)x0 + (y0-2b)y0 + (z0-2c)z0 + a² + b² + c² - r² = 0
		//								  alpha					beta								gamma
		alpha = pow(param.light.paramEqua.x[0], 2) + pow(param.light.paramEqua.y[0], 2) + pow(param.light.paramEqua.z[0], 2);
		beta = (2 * param.light.paramEqua.x[0] * param.light.paramEqua.x[1]) - (2 * param.light.paramEqua.x[0] * param.sphere[iSphere].center.x) + (2 * param.light.paramEqua.y[0] * param.light.paramEqua.y[1]) - (2 * param.light.paramEqua.y[0] * param.sphere[iSphere].center.y) + (2 * param.light.paramEqua.z[0] * param.light.paramEqua.z[1]) - (2 * param.light.paramEqua.z[0] * param.sphere[iSphere].center.z);
		gamma = (pow(param.light.paramEqua.x[1], 2) - 2 * param.light.paramEqua.x[1] * param.sphere[iSphere].center.x + pow(param.sphere[iSphere].center.x, 2)) + (pow(param.light.paramEqua.y[1], 2) - 2 * param.light.paramEqua.y[1] * param.sphere[iSphere].center.y + pow(param.sphere[iSphere].center.y, 2)) + (pow(param.light.paramEqua.z[1], 2) - 2 * param.light.paramEqua.z[1] * param.sphere[iSphere].center.z + pow(param.sphere[iSphere].center.z, 2)) - pow(param.sphere[iSphere].r, 2);
		//résolution de polynôme de second degré
		delta = pow(beta, 2) - (4*alpha*gamma);
		if (delta > 0.01) {
			double t1 = 0, t2 = 0;
			sPosSphere* intersectionPoint = NULL;
			intersectionPoint = (sPosSphere*)malloc(sizeof(sPosSphere));
			intersectionPoint->position = (sPos*)malloc(sizeof(sPos));
			t1 = (-beta - sqrt(delta)) / (2 * alpha);
			t2 = (-beta + sqrt(delta)) / (2 * alpha);
			if (t1 >= t2 && t2 > 0) {
				t = t2;
			}
			else if (t1 > 0) {
				t = t1;
			}
			else if (t2 > 0) {
				t = t2;
			}
			else {
				return false;
			}
			intersectionPoint->position->x = param.light.paramEqua.x[0] * t + param.light.paramEqua.x[1];
			intersectionPoint->position->y = param.light.paramEqua.y[0] * t + param.light.paramEqua.y[1];
			intersectionPoint->position->z = param.light.paramEqua.z[0] * t + param.light.paramEqua.z[1];
			intersectionPoint->iSphere = iSphere;
			return intersectionPoint;
		}
		else if (delta > -0.01) {
			sPosSphere* intersectionPoint = NULL;
			intersectionPoint = (sPosSphere*)malloc(sizeof(sPosSphere));
			intersectionPoint->position = (sPos*)malloc(sizeof(sPos));
			t = -beta / (2 * alpha);
			intersectionPoint->position->x = param.light.paramEqua.x[0] * t + param.light.paramEqua.x[1];
			intersectionPoint->position->y = param.light.paramEqua.y[0] * t + param.light.paramEqua.y[1];
			intersectionPoint->position->z = param.light.paramEqua.z[0] * t + param.light.paramEqua.z[1];
			intersectionPoint->iSphere = iSphere;
			return intersectionPoint;
		}
	}
	return false;
}
