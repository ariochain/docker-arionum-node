FROM mariadb:latest

COPY healthcheck/healthcheck /usr/local/bin/healthcheck
RUN chmod 755 /usr/local/bin/healthcheck

HEALTHCHECK --interval=25s --timeout=3s --start-period=120s \
    CMD healthcheck || exit 1

CMD ["mysqld"]

EXPOSE 3306
