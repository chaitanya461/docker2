FROM mysql:8.0

# Set default database and root password
ENV MYSQL_DATABASE=mydatabase
ENV MYSQL_ROOT_PASSWORD=rootpassword

# Copy initialization SQL
COPY ./db-init /docker-entrypoint-initdb.d/

EXPOSE 3306
