# Documentation on official PHP Docker images
# https://hub.docker.com/_/mysql
FROM mysql:8

LABEL maintainer="Z. Patrick Lewis <zpatricklewis@gmail.com>"

# Fix for missing mysql-files folder
# https://support.plesk.com/hc/en-us/articles/115000066805-Unable-to-log-in-to-Plesk-on-Debian-Ubuntu-based-distributions-mysql-connect-No-such-file-or-directory-var-run-mysqld-mysqld-sock-Error-code-2002-
# https://bugs.launchpad.net/ubuntu/+source/mysql-5.5/+bug/1637280
# log_bin_trust_function_creators=1
# https://dev.mysql.com/doc/refman/8.0/en/replication-options-binary-log.html#sysvar_log_bin_trust_function_creators
RUN mkdir -p /var/lib/mysql-files \
    && mkdir -p /usr/local/mysql/mysql-files \
    && chown -R mysql:mysql /var/lib/mysql-files/  /usr/local/mysql/mysql-files/ \
    && chmod 700 /var/lib/mysql-files/ /usr/local/mysql/mysql-files/

# Copy the custom configuration for mysql server
COPY ./mysql/my.cnf /etc/mysql/my.cnf

USER mysql

EXPOSE 3306
