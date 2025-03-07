**Voici les étapes détaillées pour déployer mon projet Symfony sur Hostinger :**

1. **Préparer le projet Symfony CandiMate**

   - Installez les dépendances : j’ai exécuté `composer install --no-dev --optimize-autoloader` pour installer les dépendances sans les paquets de développement.

2. **Configurer la base de données**

   - Créez une base de données : Dans le hPanel, dans la section "Bases de données MySQL", j’ai créé une nouvelle base de données.
   - Dans phpMyAdmin, j’ai copié l’information suivante `serverVersion=10.11.10-MariaDB` pour l’ajouter plus tard dans le fichier `.env`.
   - J’ai importé le fichier `db_candimate.sql`.

3. **Mettre à jour le fichier `.env`**

   - Modifiez le fichier `.env` de votre projet Symfony pour inclure les informations de connexion à la base de données :

   ```
   DATABASE_URL="mysql://username:password@host/database_name?serverVersion=10.11.10-MariaDB&charset=utf8mb4"
   ```

   Exemple de ce que vous pourriez avoir dans le fichier `.env` :

   ```
   APP_ENV=prod
   APP_DEBUG=false
   APP_SECRET="943a1812345...."
   DATABASE_URL="mysql://username:password@host/database_name?serverVersion=10.11.10-MariaDB&charset=utf8mb4"
   GEMINI_API_KEY="AIzaSy..."
   MAILER_DSN="smtp://..."
   ```

4. **Configurer le serveur web**

   4.1 **Fichier `.htaccess`, Activer la réécriture d'URL**

   - J’ai créé un fichier `.htaccess` dans le répertoire `public` pour activer la réécriture d'URL :

   ```
   # Enable URL rewriting
   RewriteEngine On

   # Rediriger toutes les requêtes HTTP vers HTTPS (facultatif : décommentez si vous souhaitez utiliser HTTPS)
   # RewriteCond %{HTTPS} !=on
   # RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

   # Rediriger toutes les requêtes vers le contrôleur frontal de Symfony (index.php) si le fichier ou le répertoire demandé n'existe pas
   RewriteCond %{REQUEST_FILENAME} !-f
   RewriteCond %{REQUEST_FILENAME} !-d
   RewriteRule ^(.*)$ index.php [QSA,L]

   # Pages d'erreur personnalisées pour Symfony (facultatif)
   ErrorDocument 404 /index.php
   ErrorDocument 500 /index.php

   # Paramètres PHP pour Symfony (ajustez selon les besoins)
   php_value memory_limit 256M
   php_value upload_max_filesize 50M
   php_value post_max_size 50M
   php_value max_execution_time 300
   php_value mysql.connect_timeout 60
   php_value mysqli.reconnect 1

   # Entêtes de sécurité (facultatif)
   <IfModule mod_headers.c>
       Header set X-Content-Type-Options "nosniff"
       Header set X-Frame-Options "SAMEORIGIN"
       Header set X-XSS-Protection "1; mode=block"
   </IfModule>

   # Mise en cache des ressources statiques (facultatif, améliore la vitesse de chargement)
   <IfModule mod_expires.c>
       ExpiresActive On
       ExpiresByType image/jpg "access plus 1 month"
       ExpiresByType image/jpeg "access plus 1 month"
       ExpiresByType image/gif "access plus 1 month"
       ExpiresByType image/png "access plus 1 month"
       ExpiresByType text/css "access plus 1 week"
       ExpiresByType application/javascript "access plus 1 week"
       ExpiresByType text/javascript "access plus 1 week"
   </IfModule>
   ```

   4.2 **Redirection vers le répertoire public**

   - J’ai créé un fichier `.htaccess` à la racine pour rediriger tout le trafic vers le répertoire `public` :

   ```
   # Redirect all traffic to the public directory
   RewriteEngine On
   RewriteRule ^(.*)$ /public/$1 [L]
   php_value display_errors 1
   php_value display_startup_errors 1
   ```

   4.4 **Explication du rôle du fichier `.htaccess`**

   - Ce fichier sert à rediriger le site vers `index.php` du dossier `public`, qui effectuera ensuite le routage des requêtes dans Symfony.
   - À l’intérieur du dossier `public`, ce fichier `.htaccess` gère la réécriture des URL, la gestion des paramètres PHP, la configuration des pages d’erreur personnalisées et la mise en cache des ressources.

5. **Télécharger les fichiers de CandiMate**

   - Dans le hPanel, dans la section « Gestionnaire de fichiers », j’ai téléchargé le fichier ZIP du projet CandiMate dans le répertoire `public_html`.
   - J’ai utilisé l'option "Extraire" pour décompresser tous les fichiers dans le répertoire `public_html`.

6. **Configurer les permissions**

   - **Permissions des répertoires** : Assurez-vous que les répertoires `var` et `public` ont les bonnes permissions pour que le serveur web puisse y accéder. Vous pouvez utiliser le Gestionnaire de fichiers ou exécuter des commandes via SSH.

   - **Accès SSH** : J’ai copié la ligne de commande suivante pour me connecter en SSH :

   ```
   ssh -p 6512345678
   ```

7. **Sur mon terminal** :

   - Je me suis connecté en SSH à l’hébergeur en utilisant la connexion SSH fournie par Hostinger, puis j’ai entré le mot de passe de la base de données que j’ai défini.
   - Ensuite, j’ai lancé la commande suivante pour modifier les permissions du dossier `public_html` :

   ```
   chmod -R 755 public_html
   ```

   - **Installer Composer** :

   ```
   php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
   php -r "if (hash_file('sha384', 'composer-setup.php') === 'dac665fdc30fdd8ec78b38b9800061b4150413ff2e3b6f88543c636f7cd84f6db9189d43a81e5503cda447da73c7e5b6') { echo 'Installer verified'.PHP_EOL; } else { echo 'Installer corrupt'.PHP_EOL; unlink('composer-setup.php'); exit(1); }"
   php composer-setup.php
   php -r "unlink('composer-setup.php');"
   ```

   - Pour utiliser Composer :

   ```
   php composer.phar
   ```

   - ajout de ce code pour afficher l'erreur au niveau de PHP qui n'était pas visible auparavant avec l'incompatibilité des versions php php_value display_errors 1 :

   ```
   php_value display_startup_errors 1
   ```

   - installation de toutes les dépendances via git bash (connexion via ssh)

   ```
    install --no-dev --optimize-autoloader
    php bin/console cache:clear
   ```

8. **Vérifier le déploiement**
   - J’ai accédé à mon domaine pour vérifier que mon projet Symfony est correctement déployé.
