import subprocess
import os
import time
import shutil

def run_command(command, message):
    """Ejecuta un comando en la terminal y muestra un mensaje de estado."""
    print(f"\n[INFO] {message}...")
    try:
        subprocess.run(command, check=True, text=True, shell=True)
        print(f"[ÉXITO] {message} completado.")
    except subprocess.CalledProcessError as e:
        print(f"[ERROR] Falló al {message.lower()}. Código de error: {e.returncode}")
        print(f"[ERROR] Salida: {e.output}")
        exit(1)
    except FileNotFoundError:
        print(f"[ERROR] El comando '{command.split()[0]}' no se encontró. Asegúrate de que está instalado y en tu PATH.")
        exit(1)

def create_docker_files():
    """Crea los archivos docker-compose.yml y Dockerfile con el contenido correcto."""
    docker_compose_content = """version: '3.8'

services:
  app:
    build:
      context: .
      dockerfile: Dockerfile
    image: laravel-pos-app
    container_name: laravel-pos-app
    restart: unless-stopped
    volumes:
      - .:/var/www/html
    ports:
      - "8000:80"
    networks:
      - pos-network
    depends_on:
      - db
    # Comando para iniciar el servidor de Laravel
    command: php artisan serve --host=0.0.0.0 --port=80

  db:
    image: mysql:8.0
    container_name: laravel-pos-db
    restart: unless-stopped
    ports:
      - "3306:3306"
    environment:
      MYSQL_DATABASE: laravel_pos
      MYSQL_ROOT_PASSWORD: rootpassword
      MYSQL_PASSWORD: password
      MYSQL_USER: laraveluser
    volumes:
      - pos-db-data:/var/lib/mysql
    networks:
      - pos-network

# Red para la comunicación entre contenedores
networks:
  pos-network:
    driver: bridge

# Volúmenes para datos persistentes
volumes:
  pos-db-data:
    driver: local
"""

    dockerfile_content = """# Usa una imagen oficial de PHP como base
FROM php:8.2-fpm-alpine

# Instala extensiones de PHP y dependencias necesarias
RUN apk add --no-cache \\
    git \\
    curl \\
    mysql-client \\
    zip \\
    unzip \\
    nodejs \\
    npm

RUN docker-php-ext-install pdo pdo_mysql

# Configura el directorio de trabajo
WORKDIR /var/www/html

# Instala Composer globalmente
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Expone el puerto 80 para el servidor web
EXPOSE 80
"""

    print("\n[INFO] Creando archivos de configuración de Docker...")
    try:
        with open('docker-compose.yml', 'w') as f:
            f.write(docker_compose_content)
        with open('Dockerfile', 'w') as f:
            f.write(dockerfile_content)
        print("[ÉXITO] Archivos de Docker creados correctamente.")
    except IOError as e:
        print(f"[ERROR] Falló al crear los archivos de Docker. Error: {e}")
        exit(1)

def main():
    print("--- Configuración inicial del proyecto Laravel y Docker ---")

    # 0. Crear archivos de Docker
    create_docker_files()

    # 1. Crear .env a partir de .env.example
    if not os.path.exists('.env'):
        try:
            shutil.copy('.env.example', '.env')
            print("[ÉXITO] Creando el archivo .env completado.")
        except FileNotFoundError:
            print("[ERROR] El archivo .env.example no se encontró. Asegúrate de que el archivo existe.")
            exit(1)
    else:
        print("[INFO] El archivo .env ya existe. Saltando este paso.")

    # 2. Levantar contenedores Docker
    run_command('docker-compose up -d', 'Levantando los contenedores Docker')

    # Dar tiempo para que el servicio de la base de datos inicie
    print("[INFO] Esperando 10 segundos para que la base de datos se inicie completamente...")
    time.sleep(10)

    # 3. Instalar dependencias de Composer
    run_command('docker-compose exec app composer install', 'Instalando las dependencias de Composer')

    # 4. Generar APP_KEY
    run_command('docker-compose exec app php artisan key:generate', 'Generando la clave de la aplicación')

    # 5. Ejecutar migraciones y seeders
    run_command('docker-compose exec app php artisan migrate --seed', 'Ejecutando las migraciones y seeders')

    print("\n--- ¡Configuración completada! ---")
    print("Puedes acceder a la aplicación en http://localhost:8000")
    print("El usuario de prueba es: admin@example.com / password")

if __name__ == "__main__":
    main()
