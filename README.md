# php mysql docker boilerplate

## configuration
copier le fichier .env.sample vers un fichier .env et valuer les variables présentes dans ce fichier puis lancer la commande :

```shell
docker-compose --env-file .env up
```

## xdebug et PhpStorm

Ouvrir les "settings" de PhpStorm (File > settings | ctrl-alt-s windows | cmd-, macos) et sélectionner PHP.

### configuration du CLI interpreter

![image](https://github.com/matthieuaudemard/php-mysql-boilerplate/assets/4210719/174d64ca-a632-4467-a551-c6d14cee4a67)
* cliquer sur les bouton ... pour ouvrir une nouvelle fenêtre puis sur le + pour ajouter une nouvelle configuration

<img width="571" alt="image" src="https://github.com/matthieuaudemard/php-mysql-boilerplate/assets/4210719/17126206-bffb-4e30-919f-495467dcc16e">

* choisissez docker-compose et remplissez le formulaire comme sur la capture ci-dessus

### configuration du server

<img width="1320" alt="image" src="https://github.com/matthieuaudemard/php-mysql-boilerplate/assets/4210719/3be5be93-7009-44ff-9b27-20f79d4f8e53">

* ajouter une nouvelle configuration en cliquant sur +
 * le champ name doit correspondre à la valeur de la variable que vous avez fourni dans le fichier .env

* activer l'écoute 
<img width="66" alt="image" src="https://github.com/matthieuaudemard/php-mysql-boilerplate/assets/4210719/870498a0-e35f-4246-b7be-adb39df18ce3">
