# ============================================================
# Dockerfile para MariaDB con inicialización de BD
# ============================================================

FROM mariadb:10.11

# Copiar script de inicialización
COPY database.sql /docker-entrypoint-initdb.d/01-init.sql
