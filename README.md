# Satellite

Para iniciar el servicio, ejecute lo siguiente:

```sh
# Con Podman
podman compose up --build
# Con Docker (experimental pero debería funcionar)
docker compose up --build
```

Acceda al puerto 8080 en su navegador para abrir el frontend.

No hace falta ejecutar ningún archivo SQL adicional. El programa hace una migración SQL en la primera llamada al backend.

La base de datos no persiste al reiniciar el servicio con Podman o Docker. Esto puede cambiarse agregando los volúmenes correspondientes en el archivo `docker-compose.yml`.
