FROM alpine:latest
RUN apk --no-cache add php84 php84-pdo_mysql dumb-init
VOLUME ["/app"]
EXPOSE 3000/tcp
WORKDIR /app
ENTRYPOINT ["/usr/bin/dumb-init", "--"]
CMD ["/usr/bin/php84", "-S", "0.0.0.0:3000"]
