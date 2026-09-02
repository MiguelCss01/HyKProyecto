# Plataforma Mayorista HyK - Configuración del Proyecto

Aca les dejo el paso a paso para que puedan instalar y levantar el proyecto de Laravel en sus compus sin problemas. Lean todo porque si saltean algo no les va a andar la base de datos.

## Requisitos Previos

1. PHP y MySQL: Tienen que instalar XAMPP o Laragon. La version de PHP tiene que ser 8.2 o superior.
2. Composer: Es el gestor de paquetes de PHP. Bajen el instalador .exe desde getcomposer.org y denle a siguiente a todo.
3. Git: Para bajar el repo.
4. MySQL Workbench: Para la base de datos.

## Paso Importante: Configurar el php.ini
Laravel necesita un driver para conectarse a MySQL que a veces viene apagado.
1. Busquen el archivo php.ini en su compu (en XAMPP suele estar en C:\xampp\php\php.ini).
2. Abranlo con el bloc de notas o VS Code.
3. Aprieten Ctrl + F y busquen: pdo_mysql
4. Si ven que la linea dice ";extension=pdo_mysql" (con punto y coma al principio), borrenle el punto y coma para que quede exactamente asi:
   extension=pdo_mysql
5. Guarden el archivo.

## Instalacion Paso a Paso

Abran la terminal en la carpeta donde quieran guardar el proyecto y sigan estos pasos:

1. Clonar el repositorio
```bash
git clone https://github.com/MiguelCss01/HyKProyecto.git
```
Despues entren a la carpeta:
```bash
cd HyKProyecto
```

2. Instalar dependencias
```bash
composer install
```

3. Configurar variables de entorno (.env)
Adentro de la carpeta del proyecto hay un archivo que se llama ".env.example". Hagan una copia de ese archivo, peguenla ahi mismo y cambienle el nombre para que se llame solamente ".env".

4. Generar la clave de la app
Corran esto en la terminal:
```bash
php artisan key:generate
```

5. Crear la Base de Datos
Abran MySQL Workbench y creen un nuevo schema (base de datos) vacio. Ponganle de nombre "hyk_db" (o el que quieran).

6. Conectar Laravel a la Base de Datos
Abran su archivo .env nuevo en VS Code, busquen la parte que dice DB_CONNECTION y dejenla asi:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=hyk_db       <--- Pongan el nombre de la BD que crearon
DB_USERNAME=root
DB_PASSWORD=             <--- Si no tienen clave en MySQL, dejenlo vacio
```

7. Correr las Migraciones (Tablas)
Para que se creen todas las tablas automaticamente en su base de datos, corran:
```bash
php artisan migrate
```

8. Levantar el servidor
Para ver la pagina, corran:
```bash
php artisan serve
```
Y entren en su navegador a: http://localhost:8000

Cualquier duda avisen. Si bajan cambios de github despues, acuerdense de tirar "php artisan migrate" por si alguien modifico las tablas.
