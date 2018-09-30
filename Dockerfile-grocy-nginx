FROM alpine:latest
MAINTAINER Talmai Oliveira <to@talm.ai>

RUN	apk update && \
	apk upgrade && \
	apk add --update openssl nginx && \
	mkdir -p /etc/nginx/certificates && \
	mkdir -p /var/run/nginx && \
	mkdir -p /usr/share/nginx/html && \
	openssl req \
		-x509 \
		-newkey rsa:2048 \
		-keyout /etc/nginx/certificates/key.pem \
		-out /etc/nginx/certificates/cert.pem \
		-days 365 \
		-nodes \
		-subj /CN=localhost && \
	rm -rf /var/cache/apk/*

COPY docker_nginx/nginx.conf /etc/nginx/nginx.conf
COPY docker_nginx/common.conf /etc/nginx/common.conf
COPY docker_nginx/conf.d/default.conf /etc/nginx/conf.d/default.conf
COPY docker_nginx/conf.d/ssl.conf /etc/nginx/conf.d/ssl.conf

# Expose volumes
VOLUME ["/etc/nginx/conf.d", "/var/log/nginx"]

# Expose ports
EXPOSE 80 443

# Entry point
ENTRYPOINT ["/usr/sbin/nginx", "-g", "daemon off;"]