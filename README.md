# Satellite

Para iniciar el servicio, ejecute lo siguiente:

```sh
# Con Podman
podman compose up
# Con Docker (experimental pero debería funcionar)
docker compose up
```

Acceda al puerto 8080 en su navegador para abrir el frontend.

Cada vez que el servicio se ejecute (o solo la primera vez si se utiliza la persistencia) debe tambien ejecutarse el archivo `config/mariadb.sql`. Esto arma la base de datos. Es posible que este proceso no sea necesario en el futuro.

La base de datos no persiste al reiniciar el servicio con Podman o Docker. Esto puede cambiarse agregando los volúmenes correspondientes en el archivo `docker-compose.yml`.
