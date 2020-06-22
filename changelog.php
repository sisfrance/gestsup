<meta charset="UTF-8" />


#################################<br />
# @Name : GestSup Release Notes  <br />
# @Date : 17/06/2015             <br />
# @Patch : 3.0.11            	 <br />
#################################<br />
<br />

Update:<br />
- Ticket: les champs date de fin estimé, criticité, priorité, peuvent être obligatoire, si le droit est positionné. (./ticket.php, ./core/ticket.php)<br /> 
- Mails: Copie Multiple - Ajout de la détection du ; dans la liste des copies des paramètres $rparameters['mail_cc'], et donc envoi en copie multiple (./core/mail.php)<br /> 
- Meta-état: un nouvel état regroupant les état en attente de pec, en cours, en attente de retour, est activable pour les techniciens dans la partie administration.(./menu.php, .parameters.php ./:dashboard.php)<br />
- Export: Ajout du service du demandeur(./core/export.php)<br />
- Liste ticket: Ajout de la visualisation des lieux (tplaces) dans le dashboard et prise en compte totale SI le paramètre "gestion des lieux" est coché (./dashboard.php)<br /> 
- Liste ticket: Une préférence utilisateur de trie est possible pour les administrateurs et techniciens configurable dans le profile utilisateur (./admin/user.php ./dahboard.php)<br /> 
- Liste ticket: Une préférence utilisateur d'arriver sur un état données est configurable dans le profile utilisateur  (./admin/user.php ./login.php)<br /> 
- Liste ticket: La date création peut être remplacer par la date de résolution estimée, paramétrable dans la section administration.  (./dashboard.php ./admin/parameters.php)<br /> 
- Module de diponibilité: ajout de la gestion des tx cible (./plugins/availability/admin/parameters.php, ./admin/parameters.php ./plugins/availability/index.php ./plugins/availability/core.php ./plugins/availability/median.php)
<br /><br />
Bugfix:<br />
- Upload: certains caractères n'étaient pas remplacés (./core/upload.php) <br />
- Stat: certains critères globaux n'était pas pris en compte(./stats/* ) <br />
- Modèle de ticket: incohérence lorsque qu'un utilisateur avec pouvoir l'utilise (./ticket_template.php) <br />
- Export: les exports sont désormait au format csv pour gérer les grosses bases (./core/export.php) <br />
- Module disponibilité: les bornes de dates était basé sur la date de création du ticket, et non du début de l'indisponibilité (./plugin/aivailability/index.php) <br />
- Module disponibilité: Les catégories n'était pas affichées tant qu'un ticket n'avait pas été crée (./plugin/aivailability/index.php ./admin/parameters.php) <br />
- Correction faute (./login.php)<br />
- Calendrier le mercredi était déclalé (./planning.php)<br />
- Utilisateurs: les utilisateurs ne pouvaient plus modifier leurs profile.<br />
- Paramètres: Défaut saut de ligne sur une option (./admin/parameters.php).<br />
- Securité: Renforcement de la sécurité sur l'edition des tickets  (./index.php, index_auth.php, dashboard.php, /admin/user.php)<br />
- Liste des tickets: supression de la colorisation des tickets anciens pour gain de performance  (./dashboard.php, ./admin/parameters.php)<br />
- Liste des tickets: sur la vue tous les ticket lors qu'un filtre est appelé les états sont conservés  (./dashboard.php)<br />
- Connexion: La redirection vers un ticket depuis un lien mail fonctionne aussi avec les authentification LDAP  (./login.php)<br />

<br />
#################################<br />
# @Name : GestSup Release Notes  <br />
# @Date : 30/10/2014             <br />
# @Patch : 3.0.10            	 <br />
#################################<br />
<br />

Notice:
- Mail: Pour les utilisateurs du connecteur SMTP en SSL, si vous avez modifié votre nom d'hote en préfixant "ssl://" vous devez le supprimer.<br />
<br />
Update:<br />
- Procédure: Organisation par catégorie (./procedure.php)<br />
- Stat: une seul ligne de filtre (./stat_line.php stat.php ./stat/*)<br />
- Stat: affichage du nombre total de demande en cours. (./stat_line.php ./stats/lien_tickets.php)<br />
- mail: Ajout de 2 destinataire en plus. (./preview_mail.php ./core/mail.php)<br />
<br />
Bugfix:<br />
- Stat: Conserver le service de l'utilisateur dans le ticket, afin d'associé un ticket à un service et plus à un utilisateur. (./core/ticket.php ./stats.pie_services.php)</br />
- Stat: Export Excel modification de la limite de 7MB par fichier passage à 30MB. (./components/../class.writeexcel_olewriter.inc.php)</br />
- Mail: Pour les mails sécurisé SSL l'ajout de ssl:// en préfixe n'est plus nécessaire (./core/mail.php ./admin/parameters.php)<br />
- Connecteur IMAP: la page de test gère les accents.(./mail2ticket.php)<br />
- Modèle: la duplication de ticket intégre le type (./ticket_template.php)<br />
- Users: dans l'ajout modification des values des cases option pour changer le mot de passe. (./users.php)<br />
- Dashboard: Choix 'supprimer' bloqué par le droit. (./dashboard.php)<br />
- Dashboard: Changement de nom pour les ticket du jour dans le menu pour ne pas décaler sur deux lignes. (./index.php)<br />
- Dashboard: Problème de sécurité en modifiant le userid dans la barre d'URL (./index.php)<br /> 
- Liste: la touche retr oudu navigateur n'affiche plus de message d'erreur suite à un $_POST (./index.php) <br /> 
- Disponibilité: manque des variables non initialisés dans certains cas (./plugins/availability/index.php)<br /> 
- Ticket: Les pièces-jointes avec caractères spéciaux été mal renommées (./core/upload.php)<br />
- Ticket: Ajout de commentaires sur les flèches suivant et précédent (./ticket.php)<br />
- Ticket: Ajout de slash sur le titre lors de l'ajout d'un thread (./core/ticket.php)<br />
- Ticket: Les noms des pièces jointe peuvent aller jusqu'a 500 caractères (SQL)<br />
- Ticket: Ré-intialisation des champs lieu et criticité sur modification de la criticité (./ticket.php)<br />
- Mails: la troisieme personnne en copie du message ne recoit pas le mail (./preview_mail.php)<br />
- Moniteur: Correction des pluriels (./monitor.php)<br />
- Moniteur: Le son fonctionne tout le temps lors d'un nouveau ticket(./monitor.php)<br />

<br />
#################################<br />
# @Name : GestSup Release Notes  <br />
# @Date : 09/07/2014             <br />
# @Patch : 3.0.9            	 <br />
#################################<br />
<br />

Update:<br />
- Liste utilisateurs: la recherche par le numéro de téléphone est possible (./admin/user.php)<br />
- Gestion de la disponibilité:  (./admin/parameters.php ./menu.php ./ticket.php ./core/ticket.php ./stat_bar_stacked.php ./index.php)<br />
- Système: ajout informations installation openssl linux (./system.php)

<br />

Bugfix:<br />
- Tickets: Les images dans les champs description et résoluion ne s'affiche pas (SQL)<br />
- Tickets: Doublon dans la liste des types sur certaines actions (./ticket.php)<br />
- Tickets: Lors de la modification du l'état résolus, si la date de résolution est anti-daté, la date n'est pas prise en compte (./core/ticket.php)<br />
- Déconnexion: Supression de la redirection lors de la déconnexion (./index.php)<br />
- Mail: Sur la prévisualisation l'icone de la pièce jointe lorsqu'elle était en majuscule ne s'affichait pas(./preview_mail.php)<br />
- Liste des tickets: Le titre de la page est correcte lors de la selection d'une vue(./dashboard.php)<br />
- Liste des tickets: lorsque l'on selectionne la vue aujourdhui la page 2 ne fonctionne pas. (./dashboard.php) <br />
- Fiche utilisateur: Lors de l'ajout d'un utilisateur les champs service et fonction sont conservées (./admin/user.php)<br /> 
- Mails: Les messages automatiques n'était pas envoyés si l'état lors de l'ouverture était en attente de prise en charge. (./core/auto_mail.php)<br />

<br />
#################################<br />
# @Name : GestSup Release Notes  <br />
# @Date : 09/04/2014             <br />
# @Patch : 3.0.8            	 <br />
#################################<br />
<br />

Update:<br />
- Utilisateur: Lors de la création ou modification d'utlilisateur depuis un ticket le champ Société est accèssible (./ticket_useradd.php)<br />
- Utilisateur: Inscription des utilisateurs, nouveau paramètre (./login.php ./admin/parameters.php ./register.php)<br />
- Utilisateur: Sur la liste des utilisateurs la pagination, le trie, et la recherche est disponible (./admin/user.php ./index.php)<br />
- Moniteur: Affichage des ticket résolus du jour (./monitor.php)<br />
- Export Excel: Ajout de la colonne société (./core.export.php)<br />
- Ticket: Ajout du champs date de résolution (./ticket.php ./core/ticket.php)<br />
- Paramètres: Possibilité de changer le port SMTP sans utiliser sans séciurisé le protocole (./admin/parameters.php ./core/mail.php ./core/message.php)<br />

<br />

Bugfix:<br />
-Utilisateur: Lors de la création d'un nouvel utilisateur on peut selectionner la société. (./admin/user.php) <br />
-Utilisateur: Sur la fiche utilisateur le service était présent deux fois dans la liste. (./admin/user.php) <br />
-Utilisateur: Sur la liste des utilisateurs avec firefox les icones d'actions était sur deux lignes. (./admin/user.php) <br />
-Statistiques: Erreur de défintion de variable. (./stat.php) <br />
-Statistiques: Répartition par catégorie, vu toutes les catégories fonctionnel. (./stat/pie_cat.php) <br />
-Statistiques: Les noms long sur les camemberts ne sont plus coupés<br />
-Sécurité: Injection de code depuis les champs utilisateurs. (./admin/user.php) <br />
-Sécurité: Les utilisateurs pouvait modifier les informations d'autres utilisateurs. (./admin/user.php) <br />
-Sécurité: Les utilisateurs ne peuvent plus consulter le dossier de sauvegarde. (./backup/.htaccess) <br />
-Listes: Ré-organisation par ordre alphabétique. (./admin/list.php) <br />
-Listes: Les nouvelles entrées de la liste état sont supprimables. (./admin/list.php) <br />
-Mail: Variable non définit. (./core/mail.php) <br />
-Mail: Lorsque le paramètre envoi de mail automatique à l'utilisateur est activé, sur le ticket lors d'un ajout de catégorie un message était envoyé. (./core_ticket.php) <br />
-Liste tickets: Lors d'une recherche le nombre de ticket compté été faux. (./dashboard.php) <br />
-Ticket: Le droit de modification du demandeur sur l'édition d'un ticket ne fonctionnait pas. (./ticket.php) <br />
-Liste des tickets: Les utilisateurs et utilisateurs avec pouvoir ayant le droit d'afficher tous les tickets affiche la colonne Demandeur. (./dashboard.php) <br />
-Sociétés: Les codes postaux des sociétés qui commence par un 0 sont gérés. (SQL) <br />
-LDAP: La synchronisation des sociétés est fonctionnel . (SQL) <br />

<br />

#################################<br />
# @Name : GestSup Release Notes  <br />
# @Date : 14/03/2014             <br />
# @Patch : 3.0.7            	 <br />
#################################<br />
<br />

Update:<br />
- Statistiques: Ajout de la courbe d'évolution du nombre de ticket fermé (./stats_line.php, ./stats/*.php)<br />
- Statistiques: Ajout de nouveaux critères de selection (./stats.php, ./stats/*.php)<br />
- Statistiques: Ajout de camemberts pour les services et société si il y en as (./stat.php ./stats/pie_company.php ./stats/pie_services.php)<br />
- Moniteur: Joue un son si un nouveau ticket est crée par un utilisateur (./monitor.php)<br />
- Utilisateurs: Gestion des sociétés le paramètre "utilisateurs avancés" doit être activé (./admin/list.php ./admin/user.php ./ticket_useradd.php ./ticket.php)<br />
- Barre utilisateur: Distinction des demandes en attente de retour (./index.php ./menu.php) <br />
<br />

Bugfix:<br />
- Export excel: Plus de timeout sur les grosses bases de données, ajout du type de ticket (./core/export.php)<br />
- Ticket: changement d'état automatique à en attente de PEC lors de la création du ticket si l'on précise état résolu (./core/ticket.php)<br />
- Logo: si aucun logo n'est choisi alors c'est le logo par défault qui s'affiche. (./index.php ./login.php)<br />
- Sécurité: suppression de l'html dans les champs texte affichés (./admin/parameters.php ./core/ticket.php)<br />
- Sécurité: le chargements des fichiers php est désormais impossible (./admin/parameters.php ./core/upload.php)<br />
- Utilisateur: lors de l'insertion d'adresse avec apostrophes.(./admin/user.php)<br />
- Utilisateur: la li- Recherche: La recherche sur les tickets crées avec la version 2, fonctste des utilisateurs à rattacher à un technicien est vide.(./admin/user.php)<br />
- Statistiques: le menu utilisateur ne s'affiche pas lorsque l'on se trouve sur la page statistique (./stat_line.php)<br />
- Sauvegarde: lors du lancement de la sauvegarde manuel les anciennes sauvegardes sont exclues (./admin/backup.php)<br />
- Calendrier: numéro de jour incorrectes les mardis (./planning.php)<br />
- Droits: Certains nommage sont incorrecte. <br />
- Listes: Les nom avec apostrophe ne fonctionne pas. (./admin/list.php) <br />

<br />

#################################<br />
# @Name : GestSup Release Notes <br />
# @Date : 15/02/2014            <br />
# @Patch : 3.0.6               <br />
#################################<br />
<br />
Update:<br />
- Ticket: l'ordre d'affichage des autres tickets du demandeur est décroissant (./ticket.php)<br />
- Mails: Possibilité d'envoyer ou non la pièce jointe (./preview_mail.php ./core/mail.php)<br />
- Impression: Ajout de la date de résolution si elle existe (./ticket_print.php ./core/ticket.php)<br />
- Supervision: Ecran des supervision indiquant le nombre de tickets en attente d'attribution et du jour. (./monitor.php ./admin/parameters.php)<br />
- Statisques: Export excel des tickets. (./stats.php ./core/export.php ./components/php_writeexel/*)<br />

<br />
Bugfix:<br />
- Mails: redirection automatique vers le ticket lors de l'utilisation du lien du mail. (./login.php)<br />
- Mails: Les utilisateurs désactivés n'appairraissent plus dans les liste des destinataire en copie (./preview_mail.php)<br />
- LDAP: La synchronisation des noms et prénoms fonctionne. (./core/ldap.php)<br />
- Statistiques: Le nom des états n'était plus affichés(./stat.php)<br />
- Boutons: liens crée depuis les boutons en haut à gauche(./index.php)<br />
- Système: Valeurs du phpinfo non récupéré sous CentOS (./system.php ./install/index.php)<br />
- Ticket: La date de résolution n'était plus enregistrée (./core/ticket.php)<br />
- Recherche: Lenteurs de recherche sur les bases avec beaucoup d'utilisateurs (./searchengine.php)<br />
- Fiche utilisateur: Les boutons changement de mot passe s'affiche mal avec IE8 (./admin/user.php)<br />

<br />
#################################<br />
# @Name : GestSup Release Notes <br />
# @Date : 23/01/2014            <br />
# @Patch : 3.0.5               <br />
#################################<br />
<br />
Update:<br />
- Ticket: pouvoir anti-daté un ticket (./ticket.php)<br />
- Liste des tickets: lors du clic sur aujourd'hui les tickets du jour de l'ensemble des techniciens s'affichent (./index.php ./dashboard.php)<br />
- Statistiques: Le nombre de tickets sur camemberts sont affichés. (./stat.php ./stat_pie.php)<br />
<br />
Bugfix:<br />
- Mails: certains mails ne s'affichent pas correctement sur certains webmails (./core/mail.php)<br />
- Recherche: la recherche sur les utilisateurs possedant le même nom fonctionne (./searchengine.php)<br />
- Tickets Procédure: Les icones de la barre d'édition de texte, sont tous en français (./wysiswyg.php)<br />
- Système: Les valeurs étaient vide sour CentOS (./system.php ./install/index.php)<br />
- Paramètres connecteur: Le bouton de test ldap perd les paramètres du connecteur. (./admin/parameters.php ./core/ldap.php)<br />
- Impression ticket: des variables n'étaient pas initialisées. (./ticket_print.php)<br />
- Fichiers manquants: fichiers de police glyphicons introuvables. (./template/assets/font/gly* ./template/assets/css/uncompressed/bootstrap.css ./template/assets/css/bootstrap.min.css)<br />
- Mails: sur certains webmail le titre était affiché en petit (./core/mail.php)<br />
- LDAP: L'affichage de la ligne d'activation des utilsateurs n'est plus décalé (./core/ldap.php)<br />
- Mails: Le message envoyé à l'administrateur lors de la déclaration d'un ticket par l'utilisateur n'a plus de slash en trop(./core/ticket.php)<br />
- Tickets: les copiés collés depuis word avec firefox s'affiche mal (./core/ticket.php)<br />
<br />
#################################<br />
# @Name : GestSup Release Notes <br />
# @Date : 09/01/2014            <br />
# @Patch : 3.0.4               <br />
#################################<br />
<br />
Update:<br />
- Paramètres: de Gestion des types de ticket, demande, incident... (./admin/parameters.php ./admin/list.php ./ticket.php ./core/ticket.php ./stat.php)<br />
<br />
Bugfix:<br />
- Tickets: les titres ne sont plus coupés avec des points (./ticket.php)<br />
- Tickets: des retours a lignes sont envoyé sur le mail, en trop(./core/mail.php ./core/ticket.php)<br />
- Liste tickets: le changement de page sur un filtre par date fonctionne. (./dashboard.php)<br />
<br />
#################################<br />
# @Name : GestSup Release Notes <br />
# @Date : 07/01/2014            <br />
# @Patch : 3.0.3                <br />
#################################<br />
<br />
Update:<br />
- <br />
Bugfix:<br />
- Install: problème d'encodage avec le squelete en UTF-8 à l'installation(./install/index.php)<br />
- Changelog: problème d'affichage des caractères spéciaux du changelog.(./changelog.php ./admin/infos.php ./index.php) </br>
- Rattachement utilisateur: le transerfert automatique fonctionne. (./admin/user.php ./ticket.php) <br />
- Ticket: selection automatique d'un technicien lorsque le technicien n'est pas renseigné. <br />
- Ticket: la description est perdu sur modification de la catégorie.(./ticket.php ./thread.php) <br />
- Ticket: agrandissement des champs texte (./ticket.php ./thread.php ./core/ticket.php)<br />
- Ticket: message d'erreur lors de la validation de ticket sans demandeur(./core/ticket.php)<br />
- Liste tickets: le filtre par date fonctionne (./dashboard.php)<br />
- Liste des utilisateurs: l'utilisateur connecté ne peut plus se désactiver (./admin/user.php)<br />
<br />
<br />
#################################<br /> 
# @Name : GestSup Release Notes<br /> 
# @Date : 28/12/2013<br />           
# @Patch : 3.0.2<br />                
#################################<br />
<br />
Update:<br />
- Ticket: rattachement d'un utilisateur à un technicien (./admin/user.php, ./core/ticket.php)<br />
- Ticket: ajout du bouton sauvegarder et quitter dans la barre du ticket (./ticket.php)<br />
- Intégration du lien vers le site sur la page de login (./index.php)<br />
<br />
Bugfix:<br />
- profile: la modification du thème est prise en compte.<br />
- Liste tickets: les titres non lus et non attribué ne s'affichait pas. (./dashboard.php)<br />
- Liste tickets: lors de la selection multiple plus de redirection vers un ticket (./dashboard.php)<br />
- Liste tickets: le filtre sur l'état fonctionne.<br />
- Menu: correction procédures avec un s (./menu.php)<br />
- Mails: depuis certains clients de messagerie, erreure d'affichage des caractères spéciaux (./core/mail.php)<br />
- Ticket: perte titre sur changement de catégorie (./ticket.php)<br />
<br />
<br />
#################################<br /> 
# @Name : GestSup Release Notes #<br />
# @Date : 27/12/2013            #<br />
# @Patch : 3.0.1                #<br />
#################################<br />
<br />
Bugfix:<br />
- LDAP: les utilisateurs ayant un UAC de 512 sont gérés (/core/ldap.php)<br />
- Mail: Charset par defaut est UTF-8 (/core/mail.php , /core/message.php)<br />
- Affichage: désactivation automatique du mode de compatibilité du navigateur Internet explorer (./index.php)<br />
- Affichage: colonne fixe pour les actions dans la liste des utilisateur dans administration (./admin/user.php)<br />
- Statistique: sur la courbe des ticket il n'y plus qu'une seul fois le jour. (./stat.php)<br />
- Nouveau Ticket: un message d'erreur SQL s'affiche (./thread.php)<br />
<br />
<br />
#################################################################################<br />
# @Name : GestSup Release Notes                                                 #<br />
# @Date : 26/12/2013                                                            #<br />
# @Version : 3.0.0                                                              #<br />
#################################################################################<br />
<br />
<br />
Notice:<br />
- Merci de selectionner votre canal de mise à jour  dans administration, mise à jour.<br />
- Pour les utilisateurs de CentOS, vous devez modifier votre fichier de configuration apache et définir l'encodage à "AddDefaultCharset UTF8"<br />
<br />
Update:<br />
- Interface graphique<br />
- Admin / listes: la gestion des catégories est simplifié<br />
- Admin / droits: gestion de nouveaux droits<br />
- Mise à jour: Gestion de deux canaux stable et bêta<br />
- Mise à jour: l'installation automatique des patchs et intégré<br />
- L'encodage des fichiers passe en UTF-8<br />
<br />
Bugfix:<br />
- Liste tickets: l'ordre de trie défini dans les paramètres n'est pas pris en compte sur les états en cours...<br />
- Liste tickets: Le tri des tickets ne fonctionne pas sur la section en cours<br />
- Liste tickets: lors du passage sur une autre page le nom de la catégorie selectionnée n'était plus affichée.<br />
- ticket: les utilisateurs ne pouvaient pas indiquer le lieu dans la création de ticket.<br />
- ticket: sur les nouveaux tickets le technicien n'était pas conservé en cas de changement<br />
- Recherche: les tickets n'ayant pas de résolution son intégré dans les recherches<br />
- mails: le message invalid adress n'aparait plus quand le champ adresse en copie est vide<br />
- LDAP: la synchronisation des caractéres spéciaux est géré<br />
- LDAP: les utilisateurs issus des synchronisations ne peuvent plus se connecter avec un mot de passe vide<br />
- LDAP: ré-encodage UTF-8 des informations récupérées dans l'annuaire LDAP<br />
- LDAP AD: Gestion de la désactivation de l'utilisateur invité, et des comptes possèdant aucune exratpiion de mot de passe.<br />
