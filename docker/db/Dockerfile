# ============================================================
# Dockerfile para MariaDB con inicialización de BD
# ============================================================

FROM mariadb:10.11

# Copiar script de inicialización
COPY docker/db/database.sql /docker-entrypoint-initdb.d/01-init.sql
