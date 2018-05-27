#include "bmp.h"
#define sizeName 30

typedef struct sFormula_ sFormula;
struct sFormula_ {
	int nbX;
	double *x;
	int nbY;
	double *y;
	int nbZ;
	double *z;
};

typedef struct sParametricEquation_ sParamEqua;
struct sParametricEquation_ {
	double x[2];
	double y[2];
	double z[2];
};

typedef struct sPosition_ sPos;
typedef struct sPosition_ sVect;
struct sPosition_ {
	double x;
	double y;
	double z;
};


typedef struct sPosAndFace_ sPosFace;
struct sPosAndFace_ {
	sPos *position;
	int iPoly;
	int iFace;
};

typedef struct sPosAndSphere_ sPosSphere;
struct sPosAndSphere_ {
	sPos *position;
	int iSphere;
};

typedef struct sPosAndEllipse_ sPosEllipse;
struct sPosAndEllipse_ {
	sPos *position;
	int iEllipse;
};

typedef struct sPlanEquation_ sPlanEqua;
struct sPlanEquation_ {
	double a;
	double b;
	double c;
	double d;
};

typedef struct sFace_ sFace;
struct sFace_ {
	sColor color;
	int nbPeaks;
	sPos *peak;
	sPlanEqua planEqua;
	bool isMirror;
};

typedef struct sPolyhedron_ sPoly;
struct sPolyhedron_ {
	sFormula formula;
	int nbFaces;
	sFace *face;
};

//equation d'une sph�re : (x - x0)� + (y - y0)� + (z - z0)� = r� avec x,y,z inconnues et x0,y0,z0(position du centre de la sphere) et r(rayon) connus

typedef struct sSphere_ sSphere;
struct sSphere_ {
	sPos center;
	double r;
	sColor color;
};


//equation d'une ellipse (x-a)�/alpha�  +  (y-b)�/beta�   +  (z-c)�/gamma�  =  1    alpha, beta et gamma donnent les longueurs des rayons dans les 3 directions,  (a,b,c) sont les coordonn�es du centre de l'ellipse
typedef struct sEllipse_ sEllipse;
struct sEllipse_ {
	double a;
	double b;
	double c;

	double alpha;
	double beta;
	double gamma;

	sColor color;
};

typedef struct sImage_ sImage;
struct sImage_ {
	char *name;
	double width;
	double height;
	sColor background;
};

typedef struct sLight_ sLight;
struct sLight_ {
	float lightFactor;
	sParamEqua paramEqua;
};


typedef struct sVideo_ sVideo;
struct sVideo_ {
	bool isTrue;
	int frames;
	sPos movement;
};

typedef struct sParam_ sParam;
struct sParam_ {
	sImage image;
	bool shadows;
	bool antialiasing;
	sVideo video;

	sPos viewerPos;

	int nbPolyhedrons;
	sPoly *poly;

	int nbSpheres;
	sSphere *sphere;

	int nbEllipse;
	sEllipse *ellipse;

	sPos lightSource;
	sLight light;
};


int nbLine(FILE *f);

int loadFromFile(sParam *param);

void freeAll(sParam *param);