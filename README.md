# INSTALACION DEL PROYECTO DE PRUEBA LFP
_Estas instrucciones te permitir谩n obtener una copia del proyecto en funcionamiento en tu m谩quina local para prop贸sitos de desarrollo y pruebas._
### Pre-requisitos 馃搵
```
* Prueba desarrollada para ser ejecutada bajo SO Linux o Mac
* Asegurar que el puerto 80 esta libre 
* Asegurar que el puerto 3306 esta libre
* Instalar docker y docker-compose | https://www.digitalocean.com/community/tutorials/how-to-install-and-use-docker-compose-on-ubuntu-20-04-es
* Hacer al usuario actual permisos de uso en docker y docker-compose (para no tener que ejecutar los comandos de docker-compose con 'sudo')
* Editar el archivo /etc/hosts y agregar la siguietne linea
    127.0.0.1       lfp.com
* Instalar git
```
### Instalaci贸n 馃敡
_Clonar repositorio_
```
$ git clone https://github.com/Lotykun/lfp.git
```
_Acceder a carpeta y levantar contenedores_
```
$ cd lfp/
lfp (main)$ docker-compose up -d --build
```
_Importar datos a la base de datos_
```
lfp (main)$ docker cp dump.sql mysql:/dump.sql
lfp (main)$ docker exec mysql /bin/bash -c 'mysql -uroot -proot < /dump.sql'
```
_Agregar Archivo .env.local y editarlo con las configuraciones necesarias_

Especial atencion los parametros ENABLE_NOTIFICATION para activar las notificaciones MAILER_DSN para la configuracion del correo saliente 
```
lfp (main)$ cp symfony/env-template symfony/.env.local
lfp (main)$ nano symfony/.env.local
```
_Instalar dependencias symfony_
```
lfp (main)$ docker exec -i -t php /bin/bash
/var/www/symfony# composer install
/var/www/symfony# exit
```

### Comprobaci贸n y Tests 馃敡
_Acceder a un navegador y ejecutar la siguiente url_

Deberia devolver un listado de los jugadores totales que hay
```
http://lfp.com/api/players
```
_Ejecuci贸n de Tests PHPUnit_

En la ruta tests/ se encuentran los tests a ejecutar, cada nombre de funci贸n, especifica que tipo de test se realiza
```
lfp (main)$ docker exec -i -t php /bin/bash
/var/www/symfony# bin/phpunit
```
_Ejecucion Requests Colecci贸n Postman_

Importar la coleccion Postman del archivo
```
lfp (main)$ lfp.postman_collection.json
```