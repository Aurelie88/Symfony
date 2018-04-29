Aurélie
Cuny
aurelie.cuny18@gmail.com


Global : 
   * création de la base de données avec doctrine en ligne de commande
   * création des entities (avec des contraintes de validation) et form correspondant
   * creations des controleurs pour chaque routes
   * creation du services uploads de fichier pour les photo de profil de l'employee
   * création des templates de pages d'erreur dans twigBundle
   * service knp-paginator pour la pagination
   * >>>>>service swiftmailer / "configuration du server mail" (pas vraiment configuré etant en local) 
on peut décommenter la partie dans la projectDeleteAction pour avoir apercu du mail envoyer au propriétaire du site 
   * réalisation de fixture pour peupler la BDD (les employees ont des image de profil par defaut) 
   * ecriture de requete particuliere dans les Repositories
   * action recherche 

Page d'acceuil :
   * recupartion de valeur pour les afficher dans le tableau de bords
	/!\ attention la page d'acceuil appele des requetes, et affiche les resultats il faut passer les fixtures avant car il y a un probleme si les resultats de requetes sont vides 

Métier : 
   * liste des metiers avec une pagination 10 par page
   * Possibilité de créer un métier a partir d'un formulaire
   * Posibilité de le modifier a partir d'une meme formulaire pré remplis
   * Suppression du métier uniquement si aucun employee lui est lié
   * gestion des exception : 
	+ impossible de supprimer un projet qui est lié a un employe
	+ impossible de trouver le metier demander (pour les routes aved des id non presente en BDD) 

Employee:
   * liste des employees avec une pagination de 10 par page 
   * possibilité d'ajout d'un employée en le liant a une entity métier grace un formulaire (avec ajout de photo) cette photo n'est pas obligatoire dans ce cas on utilise l'image par defaut present dans les asset (/web/uploads/images/default.jpg)
   * possibilité de modifier un employe a l'aide d'un formaulre prérempli
   * Archivage des employer possible depuis la liste + la feuille d'heure
   * un employe est barré dans la liste des employee si il est archivé l'action d'archivage est irreversible (depuis l'application)
   * un lien vers la feuille d'heure dans la liste
   * gestion des Exception :
	+ imposssible de trouver l'employee demandé
	+ impossible de archiver un employee deja archiver
	

Feuille d'heure par employé : 
   * Détail sur l'employee selectionné
   * Possibité de modifier les information
   * liste des jours de travail avec pagination 10 par page
   * formulaire d'ajout des heures de travail formulaire avec requete pour recuperé uniquement les projet non livré.
   * un employée archiver n'a pas acces a ce formulaire
   * posibilité de supprimer le temps (si le projet est pas livré)
   * gestion des exception :
	+ impossible d'ajouter un temps de travail sur un projet déja livrer
	+ impossible d'ajouter un temps de travail pour un employée archivé
	+ impossible de supprimer un temps de travail si  le projet est deja livrer
	+ impossible de supprimer un temps de travail si l'employée est archivé

Projet :
   * liste des projets avec pagination 10 par page
   * creation de projet a partir de forulaire
   * modification a pertir d'un formulaire preremplie
   * action : livrer un projet boolean qui permet d'empecher la suppression et l'ajout de temps de travail..
   *>> lors de la suppression : recuperer tout les données et les envoyés pa emails
   * gestion des exception : 
	+ impossible de modifier un projet deja livrer
   	+ impossible de trouver le projet en bdd


Feuille d'heure par projet:
   * Détail sur le projet 
	+ requete pour calculer le cout total du projet 
   * possibilité de modifier les informations si il est pas encore livré
   * action pour livrer le projet passe le boolean a true
