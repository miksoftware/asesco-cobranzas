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
echo -e "${YELLOW}[2/5] 📦 Composer install...${NC}"
docker exec -w /var/www/html ${PROJECT_NAME}_php composer install --no-dev --optimize-autoloader --no-interaction 2>&1 | tail -5
echo -e "${GREEN}✓ Dependencias actualizadas${NC}"

echo ""
echo -e "${YELLOW}[3/5] 🗄️  Migraciones y permisos...${NC}"
docker exec -w /var/www/html ${PROJECT_NAME}_php php artisan migrate --force 2>&1
docker exec -w /var/www/html ${PROJECT_NAME}_php php artisan db:seed --class=RolesAndPermissionsSeeder --force 2>&1
echo -e "${GREEN}✓ Migraciones y permisos ejecutados${NC}"

echo ""
echo -e "${YELLOW}[4/5] 🔐 Ajustando permisos de archivos...${NC}"
docker exec ${PROJECT_NAME}_php chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
docker exec ${PROJECT_NAME}_php chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache
echo -e "${GREEN}✓ Permisos ajustados${NC}"

echo ""
echo -e "${YELLOW}[5/5] ⚡ Limpiando cache y reiniciando...${NC}"
docker exec -w /var/www/html ${PROJECT_NAME}_php php artisan config:cache
docker exec -w /var/www/html ${PROJECT_NAME}_php php artisan route:cache
docker exec -w /var/www/html ${PROJECT_NAME}_php php artisan view:cache
docker exec -w /var/www/html ${PROJECT_NAME}_php php artisan event:cache 2>/dev/null || true
docker exec -w /var/www/html ${PROJECT_NAME}_php php -r "opcache_reset();" 2>/dev/null || true
cd "$PROJECT_DIR"
docker compose restart php nginx
sleep 3
echo -e "${GREEN}✓ Cache limpia y servicios reiniciados${NC}"

echo ""
echo -e "${GREEN}=========================================="
echo "  ✅ Deploy completado exitosamente"
echo "==========================================${NC}"
echo ""
echo -e "${BLUE}📅 Fecha: $(date '+%Y-%m-%d %H:%M:%S')${NC}"
echo -e "${BLUE}🔀 Rama: $BRANCH${NC}"
echo -e "${BLUE}📝 Commit: $LAST_COMMIT${NC}"
echo ""
