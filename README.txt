Aur�lie
Cuny
aurelie.cuny18@gmail.com


Global : 
   * cr�ation de la base de donn�es avec doctrine en ligne de commande
   * cr�ation des entity (avec des contraintes de validation) et form correspondant
   * cr�ation des templates de pages d'erreur dans twigBundle
   * service knp-paginator pour la pagination
   * >>>>>service swiftmailer / configuration du server mail
   * r�alisation de fixture pour peupler la BDD
   * ecriture de requete particuliere dans les Repositories

Page d'acceuil :
   * recupartion de valeur pour les afficher dans le tableau de bords

M�tier : 
   * liste des metiers avec une pagination 10 par page
   * Possibilit� de cr�er un m�tier a partir d'un formulaire
   * Posibilit� de le modifier a partir d'une meme formulaire pr� remplis
   * Suppression du m�tier uniquement si aucun employee lui est li�
   * gestion des exception : 
	+ impossible de supprimer un projet qui est li� a un employe
	+ impossible de trouver le metier demander (pour les routes aved des id non presente en BDD) 

Employee:
   * liste des employees avec une pagination de 10 par page 
   * possibilit� d'ajout d'un employ�e en le liant a une entity m�tier grace un formulaire
   * possibilit� de modifier un employe a l'aide d'un formaulre prerempli
   * Archivage des employer possible depuis la liste + la feuille d'heure
   * un employe est barr� dans la liste des employee si il est archiv� l'action d'archivage est irreversible (depuis l'application)
   * un lien vers la feuille d'heure dans la liste
   * gestion des Exception :
	+ imposssible de trouver l'employee demand�
	+ impossible de archiver un employee deja archiver
	

Feuille d'heure par employ� : 
   * D�tail sur l'employee selectionn�
   * Possibit� de modifier les information
   * liste des jours de travail avec pagination 10 par page
   * formulaire d'ajout des heures de travail formulaire avec requete pour recuper� uniquement les projet non livr�.
   * un employ�e archiver n'a pas acces a ce formulaire
   * posibilit� de supprimer le temps (si le projet est pas livr�)
   * gestion des exception :
	+ impossible d'ajouter un temps de travail sur un projet d�ja livrer
	+ impossible d'ajouter un temps de travail pour un employ�e archiv�
	+ impossible de supprimer un temps de travail si  le projet est deja livrer
	+ impossible de supprimer un temps de travail si l'employ�e est archiv�
Projet :
   * liste des projets avec pagination 10 par page
   * creation de projet a partir de forulaire
   * modification a pertir d'un formulaire preremplie
   * action : livrer un projet boolean qui permet d'empecher la suppression et l'ajout de temps de travail..
   *>> lors de la suppression : recuperer tout les donn�es et les envoy�s pa emails
   * gestion des exception : 
	+ impossible de modifier un projet deja livrer
   *


Feuille d'heure par projet:
   * D�tail sur le projet 
	+ requete pour calculer le cout total du projet 
   * possibilit� de modifier les informations si il est pas encore livr�
   * action pour livrer le projet passe le boolean a true
