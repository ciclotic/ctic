*NOTA Esta documentación está en castellano por motivos de productividad dada la comunidad que la desarrolla. En cuanto salga una versión beta toda documentación va a estar también en Inglés.

# Requisitos

`"php": "^7.1.3"`

Se puede utilizar cualquier base de datos que se especifique en esta url: https://www.doctrine-project.org/projects/doctrine-dbal/en/2.9/reference/platforms.html Aún así para determinados motores se deberá adaptar el driver.

# Instalación
Clonar el repositorio principal:

`git clone https://github.com/ciclotic/ctic.git suite`

Clonar todos los módulos:

`git clone https://github.com/ciclotic/ctic_warehouse.git almacenes`

`git clone https://github.com/ciclotic/ctic_customer.git clientes`

`git clone https://github.com/ciclotic/ctic_setting.git config`

`git clone https://github.com/ciclotic/ctic_account.git cuentas`

`git clone https://github.com/ciclotic/ctic_device.git dispositivos`

`git clone https://github.com/ciclotic/ctic_document.git documentos`

`git clone https://github.com/ciclotic/ctic_hr.git grh`

`git clone https://github.com/ciclotic/ctic_product.git productos`

Instalar dependencias:

`composer install`

`composer update`

Hacer una copia de `/config-sample.yml` al mismo directorio con el siguiente nombre `config.yml`.

Modificar los datos db, smtp y todos los hosts por los que se vayan a utilizar.

Instalar dependencias del módulo de cuentas:

`cd cuentas`

`composer install`

`composer update`

Hacer una copia de `/cuentas/config-sample.yml` al mismo directorio con el siguiente nombre `config.yml`.

Modificar los datos db y todos los hosts por los que vayas a utilizar.

Crear la estructura y los datos iniciales de la base de datos:

`php cli-config.php orm:generate-proxies`

`php cli-update-schema.php`

`php cli-fixtures.php`

`cd ../`

`php cli-config.php orm:generate-proxies`

`php cli-update-schema.php`

`php cli-fixtures.php`

Eso es todo. Apartir de aquí solo queda configurar los hosts en el servidor web.

# Virtual Hosts config to apache

```
<VirtualHost *:80>
    ServerName cuentas.olimpoapp.local
    ServerAlias www.cuentas.olimpoapp.local

    DocumentRoot /Users/{user}/proyectos/ciclotic/suite/cuentas
    <Directory /Users/{user}/proyectos/ciclotic/suite/cuentas>
        AllowOverride All
	    Options Indexes MultiViews FollowSymLinks
	    Require all granted
    </Directory>

    ErrorLog /private/var/log/apache2/cuentas.olimpoapp.local-error.log
    CustomLog /private/var/log/apache2/cuentas.olimpoapp.local-access_log combined
</VirtualHost>
```
```
<VirtualHost *:80>
    ServerName grh.olimpoapp.local
    ServerAlias www.grh.olimpoapp.local

    DocumentRoot /Users/{user}/proyectos/ciclotic/suite/grh
    <Directory /Users/{user}/proyectos/ciclotic/suite/grh>
        AllowOverride All
	    Options Indexes MultiViews FollowSymLinks
	    Require all granted

    </Directory>

    ErrorLog /private/var/log/apache2/grh.olimpoapp.local-error.log
    CustomLog /private/var/log/apache2/grh.olimpoapp.local-access_log combined
</VirtualHost>
```
```
<VirtualHost *:80>
    ServerName config.olimpoapp.local
    ServerAlias www.config.olimpoapp.local

    DocumentRoot /Users/{user}/proyectos/ciclotic/suite/config
    <Directory /Users/{user}/proyectos/ciclotic/suite/config>
        AllowOverride All
        Options Indexes MultiViews FollowSymLinks
        Require all granted
    </Directory>

    ErrorLog /private/var/log/apache2/config.olimpoapp.local-error.log
    CustomLog /private/var/log/apache2/config.olimpoapp.local-access_log combi$
</VirtualHost>
```
```
<VirtualHost *:80>
    ServerName clientes.olimpoapp.local
    ServerAlias www.clientes.olimpoapp.local

    DocumentRoot /Users/{user}/proyectos/ciclotic/suite/clientes
    <Directory /Users/{user}/proyectos/ciclotic/suite/clientes>
        AllowOverride All
        Options Indexes MultiViews FollowSymLinks
        Require all granted
    </Directory>

    ErrorLog /private/var/log/apache2/clientes.olimpoapp.local-error.log
    CustomLog /private/var/log/apache2/clientes.olimpoapp.local-access_log combi$
</VirtualHost>
```
```
<VirtualHost *:80>
    ServerName dispositivos.olimpoapp.local
    ServerAlias www.dispositivos.olimpoapp.local

    DocumentRoot /Users/{user}/proyectos/ciclotic/suite/dispositivos
    <Directory /Users/{user}/proyectos/ciclotic/suite/dispositivos>
        AllowOverride All
        Options Indexes MultiViews FollowSymLinks
        Require all granted
    </Directory>

    ErrorLog /private/var/log/apache2/dispositivos.olimpoapp.local-error.log
    CustomLog /private/var/log/apache2/dispositivos.olimpoapp.local-access_log combi$
</VirtualHost>
```
```
<VirtualHost *:80>
    ServerName almacenes.olimpoapp.local
    ServerAlias www.almacenes.olimpoapp.local

    DocumentRoot /Users/{user}/proyectos/ciclotic/suite/almacenes
    <Directory /Users/{user}/proyectos/ciclotic/suite/almacenes>
        AllowOverride All
        Options Indexes MultiViews FollowSymLinks
        Require all granted
    </Directory>

    ErrorLog /private/var/log/apache2/almacenes.olimpoapp.local-error.log
    CustomLog /private/var/log/apache2/almacenes.olimpoapp.local-access_log combi$
</VirtualHost>
```
```
<VirtualHost *:80>
    ServerName productos.olimpoapp.local
    ServerAlias www.productos.olimpoapp.local

    DocumentRoot /Users/{user}/proyectos/ciclotic/suite/productos
    <Directory /Users/{user}/proyectos/ciclotic/suite/productos>
        AllowOverride All
        Options Indexes MultiViews FollowSymLinks
        Require all granted
    </Directory>

    ErrorLog /private/var/log/apache2/productos.olimpoapp.local-error.log
    CustomLog /private/var/log/apache2/productos.olimpoapp.local-access_log combi$
</VirtualHost>
```
En el host de la api se debe settear el Authorization en apache.
```
<VirtualHost *:80>
    ServerName api.olimpoapp.local
    ServerAlias www.api.olimpoapp.local

    DocumentRoot /Users/{user}/proyectos/ciclotic/suite/api/public
    <Directory /Users/{user}/proyectos/ciclotic/suite/api/public>
        AllowOverride All
        Options Indexes MultiViews FollowSymLinks
        Require all granted

	    SetEnvIf Authorization "(.*)" HTTP_AUTHORIZATION=$1
    </Directory>

    ErrorLog /private/var/log/apache2/api.olimpoapp.local-error.log
    CustomLog /private/var/log/apache2/api.olimpoapp.local-access_log combi$
</VirtualHost>
```
```
<VirtualHost *:80>
    ServerName documentos.olimpoapp.local
    ServerAlias www.documentos.olimpoapp.local

    DocumentRoot /Users/{user}/proyectos/ciclotic/suite/documentos
    <Directory /Users/{user}/proyectos/ciclotic/suite/documentos>
        AllowOverride All
        Options Indexes MultiViews FollowSymLinks
        Require all granted
    </Directory>

    ErrorLog /private/var/log/apache2/documentos.olimpoapp.local-error.log
    CustomLog /private/var/log/apache2/documentos.olimpoapp.local-access_log combi$
</VirtualHost>
```