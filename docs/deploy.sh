#!/bin/bash
PROJECT_NAME="cobranzas"

# ============================================
# Script de Deploy Automático para Laravel
# ============================================

GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

SCRIPT_DIR=$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)
PROJECT_DIR="$SCRIPT_DIR"
SRC_DIR="$PROJECT_DIR/src"

echo -e "${BLUE}=========================================="
echo "  🚀 Deploy Laravel: $PROJECT_NAME"
echo "==========================================${NC}"
echo ""

cd "$SRC_DIR"

echo -e "${YELLOW}[1/7] ⬇️  Descargando cambios desde Git...${NC}"
HAS_STASH=false
if ! git diff --quiet || ! git diff --cached --quiet; then
    git stash --quiet && HAS_STASH=true
fi

BRANCH=$(git rev-parse --abbrev-ref HEAD)
if git pull origin "$BRANCH" 2>&1; then
    echo -e "${GREEN}✓ Cambios descargados desde rama '$BRANCH'${NC}"
else
    echo -e "${RED}✗ Error al descargar cambios${NC}"
    $HAS_STASH && git stash pop --quiet 2>/dev/null || true
    exit 1
fi

if $HAS_STASH; then
    git stash pop --quiet 2>/dev/null || true
fi

LAST_COMMIT=$(git log -1 --pretty=format:'%h - %s (%ar) por %an')
echo -e "${BLUE}    📝 Último commit: $LAST_COMMIT${NC}"

echo ""
echo -e "${YELLOW}[1.5/7] 🐳 Reconstruyendo imagen Docker (si cambió el Dockerfile)...${NC}"
cd "$PROJECT_DIR"
docker compose build php 2>&1 | tail -10
docker compose up -d --no-deps --no-recreate php
echo -e "${GREEN}✓ Imagen actualizada${NC}"
cd "$SRC_DIR"

echo -e "${YELLOW}[2/7] 📦 Composer install...${NC}"
docker exec -w /var/www/html ${PROJECT_NAME}_php composer install --no-dev --optimize-autoloader --no-interaction 2>&1 | tail -5
echo -e "${GREEN}✓ Dependencias actualizadas${NC}"

echo ""
echo -e "${YELLOW}[3/7] 🗄️  Migraciones...${NC}"
docker exec -w /var/www/html ${PROJECT_NAME}_php php artisan migrate --force 2>&1
echo -e "${GREEN}✓ Migraciones ejecutadas${NC}"

echo ""
echo -e "${YELLOW}[4/7] 📦 Compilando assets...${NC}"
if [ -f "$SRC_DIR/package.json" ]; then
    docker exec -w /var/www/html ${PROJECT_NAME}_php npm install 2>&1 | tail -3
    if docker exec -w /var/www/html ${PROJECT_NAME}_php npm run build 2>&1; then
        echo -e "${GREEN}✓ Assets compilados${NC}"
    else
        echo -e "${RED}✗ Error al compilar assets. Revisa que Node.js esté instalado en el contenedor.${NC}"
        exit 1
    fi
else
    echo -e "${BLUE}⏭️  Sin package.json, saltando${NC}"
fi

echo ""
echo -e "${YELLOW}[5/7] 🔐 Ajustando permisos...${NC}"
docker exec ${PROJECT_NAME}_php chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
docker exec ${PROJECT_NAME}_php chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache
docker exec ${PROJECT_NAME}_php chmod 666 /var/www/html/.env 2>/dev/null || true
echo -e "${GREEN}✓ Permisos ajustados${NC}"

echo ""
echo -e "${YELLOW}[5.5/7] 📦 Publicando assets (Livewire, etc.)...${NC}"
docker exec -w /var/www/html ${PROJECT_NAME}_php php artisan vendor:publish --force --tag=livewire:assets 2>/dev/null || true
docker exec -w /var/www/html ${PROJECT_NAME}_php php artisan livewire:publish --assets 2>/dev/null || true
echo -e "${GREEN}✓ Assets publicados${NC}"

echo ""
echo -e "${YELLOW}[6/7] ⚡ Limpiando y recacheando...${NC}"
docker exec -w /var/www/html ${PROJECT_NAME}_php php artisan config:cache
docker exec -w /var/www/html ${PROJECT_NAME}_php php artisan route:cache
docker exec -w /var/www/html ${PROJECT_NAME}_php php artisan view:cache
docker exec -w /var/www/html ${PROJECT_NAME}_php php artisan event:cache 2>/dev/null || true
echo -e "${GREEN}✓ Cache reconstruida${NC}"

echo ""
echo -e "${YELLOW}[6.5/7] 🔄 Limpiando OPcache...${NC}"
docker exec -w /var/www/html ${PROJECT_NAME}_php php -r "if (function_exists('opcache_reset')) { opcache_reset(); echo 'OPcache limpiado'; } else { echo 'OPcache no activo'; }" 2>/dev/null || true
echo -e "${GREEN}✓ OPcache limpiado${NC}"

echo -e "${YELLOW}[7/7] 🔄 Reiniciando servicios...${NC}"
cd "$PROJECT_DIR"
docker compose restart php nginx
sleep 5
echo -e "${GREEN}✓ Servicios reiniciados${NC}"

echo ""
echo -e "${GREEN}=========================================="
echo "  ✅ Deploy completado exitosamente"
echo "==========================================${NC}"
echo ""
echo -e "${BLUE}📅 Fecha: $(date '+%Y-%m-%d %H:%M:%S')${NC}"
echo -e "${BLUE}🔀 Rama: $BRANCH${NC}"
echo -e "${BLUE}📝 Commit: $LAST_COMMIT${NC}"
echo ""
