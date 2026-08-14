# Notes de changements

Résumé des modifications apportées au projet pour répondre aux tâches
techniques (WordPress Bedrock + ACF FREE).

---

## Contexte technique

- WordPress installé via **Bedrock** (Composer), WordPress 6.9.1.
- Plugins : **Advanced Custom Fields (FREE)** 6.8.7, `stoutlogic/acf-builder`.
- Thème : **twentytwentyfive**.
- Toutes les modifications sont dans le thème : `web/app/themes/twentytwentyfive/`.

> Contrainte importante : la version **FREE** d'ACF ne gère pas les blocs
> ACF (sidebar du bloc) ni `acf_register_block_type()`. Ces fonctionnalités
> sont réservées à ACF PRO. Le bloc a donc été implémenté en **bloc Gutenberg
> natif** (rendu dynamique serveur), avec les champs ACF rattachés au CPT.

---

## Fichiers modifiés / créés

### 1. `web/app/themes/twentytwentyfive/functions.php`
- Ajout du chargement des fichiers personnalisés en fin de fichier :
  - `inc/acf/acf_project.php`
  - `inc/post_type/new_post_type.php`
  - `inc/block/*/fields.php` (tous les dossiers de blocs)
  - `inc/block/block.php`

### 2. `inc/post_type/new_post_type.php`
- Enregistrement du Custom Post Type **`project`** (Projets) :
  - `public`, `has_archive`, `show_in_rest`, `supports` (title, editor,
    thumbnail, excerpt), slug `projets`.
- Correction apportée : le nom de fonction était `regester_post_type()`
  (faute de frappe) → remplacé par `register_post_type()`.

### 3. `inc/acf/acf_project.php`
- Groupe de champs ACF lié au CPT `project` (via `setLocation('post_type', '==', 'project')`) :
  - `client_name` : Nom du client
  - `project_category` : Catégorie (Web / Branding / Mobile)
  - `project_gallery` : Galerie photos
  - `project_url` : Lien externe
  - `project_summary` : Description courte

### 4. `inc/block/block.php`
- Enregistrement du bloc : `register_block_type(__DIR__ . '/testimonial')`.
- Était enveloppé dans `if (function_exists('acf_register_block_type'))`
  qui est **toujours faux en ACF FREE** → le bloc n'était jamais enregistré.
  → Appel direct, sans dépendance ACF PRO.

### 5. `inc/block/testimonial/block.json`
- Bloc **`acf/testimonial`** ("Témoignage"), catégorie `text`, icône
  `block-default`.
- Changements clés :
  - Suppression de la clé `acf` (`renderTemplate`, `mode`) : réservée à ACF PRO.
  - Ajout de `"render": "template.php"` : rendu **dynamique** côté serveur
    (syntaxe native WordPress ≥ 6.1).
  - `"category"` : `custom-blocks` → `text` (catégorie `custom-blocks` non
    enregistrée → bloc invisible dans l'éditeur).
  - `"icon"` : `dashicons-block-default` → `block-default` (WordPress préfixe
    déjà `dashicons-`).
  - Ajout de `attributes` : déclaration de la configuration du bloc
    (`author_name`, `quote`, `author_photo`).

### 6. `inc/block/testimonial/fields.php`
- Groupe de champs ACF `testimonial_block` :
  - `author_name` : Nom
  - `quote` : Citation
  - `author_photo` : Photo (retour tableau)
- Localisation : `setLocation('post_type', '==', 'project')`
  (la localisation `block` est réservée à ACF PRO).

### 7. `inc/block/testimonial/template.php`
- Rendu dynamique du bloc (frontend + éditeur) :
  - Lecture de l'ID du post via `$block->context['postId'] ?? get_the_ID()`.
  - Source unique des champs : lecture des champs définis dans le groupe ACF
    `group_testimonial_block` via `acf_get_field_group()` + `acf_get_fields()`.
    → le template utilise **uniquement les champs définis dans la
    configuration du bloc** (aucune liste codée en dur).
  - Rendu conditionnel : photo, citation (avec placeholder dans l'éditeur),
    nom de l'auteur.

---

## Explications des choix techniques

### Pourquoi un bloc Gutenberg natif plutôt qu'ACF Block ?
`acf_register_block_type()` et les champs dans la sidebar du bloc sont des
fonctionnalités **ACF PRO**. La version gratuite ne les fournit pas. Un bloc
enregistré via `register_block_type()` + `block.json` avec `render` est un
bloc dynamique natif WordPress qui rend le contenu côté serveur, sans
dépendre d'ACF.

### Pourquoi les champs sont-ils rattachés au post_type `project` ?
En ACF FREE, `setLocation('block', ...)` n'existe pas. La seule possibilité
est de rattacher les champs à un type de contenu. Les champs du bloc
s'éditent donc dans l'éditeur du projet, et le bloc les affiche sur le post
courant (le projet).

### Pourquoi `$block->context['postId']` ?
En rendu natif, `$block` est un objet `WP_Block` (pas un tableau ACF) et ne
supporte pas `$block['post_id']`. L'ID du post est disponible via
`$block->context['postId']`, avec repli sur `get_the_ID()`.

### Pourquoi `$is_preview` est recalculé ?
`$is_preview` est une variable fournie par ACF PRO. En rendu natif elle
n'existe pas. On la définit via `defined('REST_REQUEST') && REST_REQUEST`
(affichage d'un placeholder dans l'éditeur).

---

## Statut par rapport aux tâches

| Tâche | Statut |
|---|---|
| 1. Installation Bedrock + .env | OK |
| 2. CPT + enregistrement des champs | OK |
| 3. Champs ACF | OK |
| 4. Bloc Gutenberg (rendu dynamique) | OK |
| 5. Configuration du bloc (champs ACF + utilisation exclusive) | OK |

### Point d'attention
Pour passer en "vraie" config ACF du bloc (champs éditables dans la sidebar
du bloc), il faudrait **ACF PRO** :
- `fields.php` : `setLocation('block', '==', 'acf/testimonial')`
- `block.json` : clé `acf` avec `renderTemplate` + enregistrement de la
  catégorie `custom-blocks`
- `template.php` : lecture via `$block['data']`
