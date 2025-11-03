# Satellite

Para iniciar el servicio, ejecute lo siguiente:

```sh
# Con Podman
podman compose up
# Alternativamente, con Docker (experimental pero debería funcionar)
docker compose up
```

Acceda al puerto 8080 en su navegador para abrir el frontend.

No hace falta ejecutar ningún archivo SQL adicional. El programa hace una migración SQL en la primera llamada al backend.

La base de datos no persiste al reiniciar el servicio con Podman o Docker. Esto puede cambiarse agregando los volúmenes correspondientes en el archivo `docker-compose.yml`.

## Testing

Para iniciar el testeo, inicie el servicio y en otra terminal ejecute `sh test/run-all.sh` (requiere [podman](https://podman.io), un shell POSIX y [hurl](https://hurl.dev)).

Ejecutar pruebas solo debe realizarse durante el desarrollo porque **borra los datos actuales**.

