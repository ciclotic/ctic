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