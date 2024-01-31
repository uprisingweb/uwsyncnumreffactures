Module de synchronisation des numéros de factures entre 2 instances Dolibarr

Pour configurer le module vous devez (sur les 2 dolibarr à synchroniser) : 
 1.  Installer le module en le copiant dans le répertoire htdocs/custom
 2.  Activer le module dans la liste des modules
 3.  Activer le module API Rest
 4.  Générer une clé API sur un de vos utilisateurs et la reporter la "page de configuration du module"(*) du 2nd dolibarr (utiliser/créer un utilisateur qui a les droits sur les factures)
 5.  Définir sur chaque dolibarr le SERVER NAME du 2nd Dolibarr (sans https:// ou / à la fin . (Ex: dolibarr.nomdedomaine.com) dans la "page de configuration du module"(*)
 6.  Dans la liste des modules > Factures et Avoir > configurer : il faut activer la numérotation synchrone et definir les mêmes masques de numérotation
 7.  Une fois la numérotation synchrone activée sur les 2 dolibarr, les informations API renseignées, vous pourrez voir sur la "page de configuration du module"(*) l'information "Prochain numéro de facture calculé".
      - Si elle est à 0 c'est que vous avez une erreur.
      - Si elle est différente c'est que les API sont mal configurées ou les masques de numérotation ne sont pas les mêmes.
      - Si elle est identique c'est que la configuration est bonne.


En cas de problème : 
- consulter : /api/index.php/explorer/ et saisr la clé API générée sur cette instance de dolibarr dans le champs DOLAPIKEY
  et Vérifier qu'existe 'uwsyncnumreffactures' > 'List Operations' > GET /uwsyncnumreffactures/nextnumreffacture (A verifier sur les 2 dolibarr)
- Vous pouvez aussi sur la "page de configuration du module"(*), activer les log en mettant FACTURE_SYNCHRONE_SYNCHRO_LOG à 1 et consulter les log dans custom/uwsyncnumreffactures/logs (le faire sur les 2 dolibarr pour bien voir les interactions de chaque côté)
- Recharger la "page de configuration du module"(*) suffit pour tester l'api et consulter les logs. En effet pour afficher le Prochain numéro de facture calculé ci-dessous les appels inter-dolibarr sont fait

(*) Dans les instructions ci-dessous, la "page configuration du module" correspond à la page accessible sur Dolibarr : Accueil > Configuration > Configuration Modules/Application > UwSyncNumRefFactures > Configurer (Engrenage)
