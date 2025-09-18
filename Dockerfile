FROM alpine:latest
RUN apk add php
USER 1000:1000
VOLUME ["/app"]
EXPOSE 8080/tcp
WORKDIR /app
CMD ["/usr/bin/php","-S","0.0.0.0:8080"]
