#!/usr/bin/env bash
#
# installer.bash — Aplica el parche "elimina duplicación de nombres/apellidos"
# sobre una copia local del proyecto CtrlViewModelDb (Laravel).
#
# Qué hace:
#   1. Verifica que el destino contenga la carpeta 'app' (proyecto Laravel,
#      completo o exportación parcial).
#   2. Hace backup de cada archivo que va a sobreescribir en
#      backup_nombres_apellidos_<timestamp>/ (misma estructura de carpetas).
#   3. Copia los 24 archivos modificados/nuevos: modelos (User, Estudiante,
#      Profesor, Secretaria con accessors nombres/apellidos), controladores,
#      seeders, factories, 2 vistas, y las 4 migraciones que renombran
#      'apellido'->'lastname' en users y eliminan 'nombres'/'apellidos' de
#      estudiantes/profesors/secretarias.
#   4. Pregunta (o vía flag) si quieres correr 'php artisan migrate'.
#
# IMPORTANTE: este parche elimina columnas de la base de datos. Haz un
# backup de tu BD antes de correr 'php artisan migrate' (ej: mysqldump).
# No uses 'migrate:fresh' salvo que quieras perder tus datos actuales.
#
# Uso:
#   ./installer.bash [ruta_al_proyecto] [opciones]
#
# Opciones:
#   --dry-run       Solo lista qué archivos se instalarían/sobreescribirían, sin tocar nada.
#   --no-backup     No crea backup de los archivos existentes (no recomendado).
#   --migrate       Corre 'php artisan migrate' automáticamente al terminar.
#   -y, --yes       No pregunta confirmación antes de aplicar los cambios.
#   -h, --help      Muestra esta ayuda.
#
# Ejemplos:
#   ./installer.bash .
#   ./installer.bash /c/xampp/htdocs/www/Remanente-LMS-Re --migrate
#   ./installer.bash . --dry-run
#
set -euo pipefail

SCRIPT_PATH="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)/$(basename "${BASH_SOURCE[0]}")"
EXPECTED_SHA256="e8890fcc4bdd2fb0e7a4d3f8271fcacabaa6d700661fc872ce0b8ef5025727f2"

TARGET_DIR="."
DRY_RUN=0
NO_BACKUP=0
DO_MIGRATE=0
ASSUME_YES=0

print_help() {
    sed -n '2,32p' "$SCRIPT_PATH" | sed 's/^# \{0,1\}//'
}

# ---------- Parseo de argumentos ----------
POSITIONAL=()
for arg in "$@"; do
    case "$arg" in
        --dry-run)   DRY_RUN=1 ;;
        --no-backup) NO_BACKUP=1 ;;
        --migrate)   DO_MIGRATE=1 ;;
        -y|--yes)    ASSUME_YES=1 ;;
        -h|--help)   print_help; exit 0 ;;
        *)           POSITIONAL+=("$arg") ;;
    esac
done
if [ "${#POSITIONAL[@]}" -ge 1 ]; then
    TARGET_DIR="${POSITIONAL[0]}"
fi

if [ ! -d "$TARGET_DIR" ]; then
    echo "❌ La ruta '$TARGET_DIR' no existe." >&2
    exit 1
fi
TARGET_DIR="$(cd "$TARGET_DIR" && pwd)"

echo "== Instalador: elimina duplicación de nombres/apellidos =="
echo "Proyecto destino: $TARGET_DIR"

# ---------- Verificación de proyecto Laravel ----------
if [ ! -d "$TARGET_DIR/app" ]; then
    echo "❌ '$TARGET_DIR' no parece ser la raíz del proyecto (no se encontró la carpeta 'app')." >&2
    echo "   Ejecuta este script pasando la carpeta CtrlViewModelDb/ como argumento." >&2
    exit 1
fi
if [ ! -f "$TARGET_DIR/artisan" ] || [ ! -f "$TARGET_DIR/composer.json" ]; then
    echo "⚠️  No se encontró 'artisan' y/o 'composer.json' en '$TARGET_DIR'."
    echo "   Parece una exportación parcial del proyecto (sin vendor/artisan)."
    echo "   Se instalarán igual los archivos de app/, database/ y resources/;"
    echo "   deberás correr 'php artisan migrate' desde el proyecto Laravel real."
fi

# ---------- Extraer el paquete embebido ----------
WORKDIR="$(mktemp -d)"
trap 'rm -rf "$WORKDIR"' EXIT

ARCHIVE_LINE=$(awk '/^__ARCHIVE_BELOW__$/{print NR + 1; exit}' "$SCRIPT_PATH")
if [ -z "$ARCHIVE_LINE" ]; then
    echo "❌ No se encontró el paquete embebido dentro del script." >&2
    exit 1
fi

tail -n "+$ARCHIVE_LINE" "$SCRIPT_PATH" | base64 -d > "$WORKDIR/patch.tar.gz"

if command -v sha256sum >/dev/null 2>&1; then
    ACTUAL_SHA256="$(sha256sum "$WORKDIR/patch.tar.gz" | awk '{print $1}')"
elif command -v shasum >/dev/null 2>&1; then
    ACTUAL_SHA256="$(shasum -a 256 "$WORKDIR/patch.tar.gz" | awk '{print $1}')"
else
    ACTUAL_SHA256=""
fi

if [ -n "$ACTUAL_SHA256" ] && [ "$ACTUAL_SHA256" != "$EXPECTED_SHA256" ]; then
    echo "❌ El paquete embebido está corrupto (checksum no coincide)." >&2
    echo "   Esperado: $EXPECTED_SHA256" >&2
    echo "   Obtenido: $ACTUAL_SHA256" >&2
    exit 1
fi

mkdir -p "$WORKDIR/patch"
tar xzf "$WORKDIR/patch.tar.gz" -C "$WORKDIR/patch"

# ---------- Listar archivos a instalar ----------
mapfile -t PATCH_FILES < <(cd "$WORKDIR/patch" && find . -type f | sed 's|^\./||' | sort)

echo
echo "Archivos que se van a instalar (${#PATCH_FILES[@]}):"
for f in "${PATCH_FILES[@]}"; do
    if [ -f "$TARGET_DIR/$f" ]; then
        echo "  [reemplaza] $f"
    else
        echo "  [nuevo]     $f"
    fi
done
echo

if [ "$DRY_RUN" -eq 1 ]; then
    echo "🔎 --dry-run: no se modificó nada."
    exit 0
fi

if [ "$ASSUME_YES" -ne 1 ]; then
    read -r -p "¿Continuar y aplicar estos cambios? [y/N] " REPLY
    case "$REPLY" in
        [yY]|[yY][eE][sS]) ;;
        *) echo "Cancelado."; exit 0 ;;
    esac
fi

# ---------- Backup ----------
if [ "$NO_BACKUP" -ne 1 ]; then
    BACKUP_DIR="$TARGET_DIR/backup_nombres_apellidos_$(date +%Y%m%d_%H%M%S)"
    mkdir -p "$BACKUP_DIR"
    echo "🗂  Backup de archivos existentes en: $BACKUP_DIR"
    for f in "${PATCH_FILES[@]}"; do
        if [ -f "$TARGET_DIR/$f" ]; then
            mkdir -p "$BACKUP_DIR/$(dirname "$f")"
            cp "$TARGET_DIR/$f" "$BACKUP_DIR/$f"
        fi
    done
else
    echo "⚠️  --no-backup: no se respaldó ningún archivo existente."
fi

# ---------- Copiar archivos nuevos ----------
echo "📦 Instalando archivos..."
for f in "${PATCH_FILES[@]}"; do
    mkdir -p "$TARGET_DIR/$(dirname "$f")"
    cp "$WORKDIR/patch/$f" "$TARGET_DIR/$f"
    echo "  ✔ $f"
done

echo
echo "✅ Cambios aplicados con éxito."
echo "   nombres/apellidos ya no se duplican en estudiantes/profesors/secretarias:"
echo "   viven solo en 'users' (name/lastname) y se leen vía accessors."
echo
echo "Pasos pendientes:"
echo "  1. Haz backup de tu base de datos antes de migrar:"
echo "       mysqldump -u root TU_BD > backup_antes_de_migrar.sql"
echo "  2. Corre las migraciones (NO uses --fresh si quieres conservar tus datos):"
echo "       cd '$TARGET_DIR' && php artisan migrate"
echo "  3. Limpia cachés:"
echo "       php artisan config:clear && php artisan view:clear && php artisan cache:clear"
echo "  4. Revisa 'backup_nombres_apellidos_*' si necesitas comparar/revertir."
echo

if [ "$DO_MIGRATE" -eq 1 ]; then
    if command -v php >/dev/null 2>&1; then
        echo "▶ Ejecutando: php artisan migrate"
        (cd "$TARGET_DIR" && php artisan migrate)
    else
        echo "⚠️  No se encontró 'php' en el PATH; corre la migración manualmente." >&2
    fi
fi

exit 0
__ARCHIVE_BELOW__
H4sIAAAAAAAAA+w9yXIbuZI+K8L/gOlQBMkemuIui2qrm6botjq0eEjJc7AUDKgKospdLNC1SNbz
6Dq3ucxx/uGd+jZX/8l8yWQCtW8ktdCym4iONlUFJJbcEwlUZePZo5cqlM1WS/wLJf6v+F1r1Zut
WrNZazbg+eZmrfmMtB5/aM+eOZZNTUKemZzbefVmvf9OS2VDpTY9pxZ7PEJYGP+1WrNdXeF/GSWE
/4k2NqmtccN6YFJYDP91wH+j3Wis8L+Mko7/erXeHlVfjupbo9pLQE11ZLIJv2Ijg0/OTWaN6JTp
uqZya3Rh8sloavILZnHTGgEsnVWml9NQH4jgdrOZhf9ardaI4r9RrdZB/leXsQB/c/z/8ivgam3N
sRjZ03VnohnUZqe7Lk2cHvg0Efzczqw9VC7ZhJ6+1h02NTXDTtQcOtMpN+3TN1ShKrPcBttrayaz
HdMgBrsmik4ti7DPNjNUi/i9ki9rBMrUOdc1hVw4hiKeOtNiqUOuuKaK17ISFgm60xEEWSz4FFoo
B42L/kjJuqhXCgHAIp++2FFNPu1xmIZR/FBweQAAFXw2KJyVtv2Wt+7v27XUIav82ljGoC0bKo2L
/nhLL3bohc3MYkFTC6HhprUJJha08uGkzfR2e+1bk/Kq3KHMJf9rs+Q/s2xH1ahhszQNMFP+N2px
+d+ARyv5v4Tyd5H/IQr9njTAfYcd1wFlUqtW76YIYk1X2uCHKHPJ/yrKf4NOmC/3RzYfAZ/a4qFm
jIDPM4z/Z7Plf70d8//qm/XG5kr+L6N8W/m/+/pbqoiNDTJkxLEo6e4f9wfkuPt6v08qlQrpve0e
/t4nzCBX7B9EZb5YlGzgKoJSGBIM+pPDyAE1Nbr7mvwC0rLSqtSJwYnFcUaU6JRYsCr0s2aRQf+w
e9AnvaP9k4PDchjQDYl2gqPYpya9YjpCrcHrT47GTAZqREEpzTbUc6pXfBi7rzsdIGqbTZhhFwvh
uQk29Wbn8TLxGJm87w7g3aBYb7VK5PBkf79wZz027yD8vv3R5AxipVYevMwl/xuz7H+LKcCeSPp3
sv9rmwn7v7aK/yyl/F3s/xCFfk/2/32HvYoBrUpuCcl/izEVNPPD7wMuvv/XqNXqq/2fZZQU/A99
mTMUT5IO3YJlhv6v15qbMfxvVpur+N9SitD/z9eer6ERbk2pwkigziVBbONrVOTd6fT0gKtMt057
jmnx7cTjd+6GRfLNW24CRaU06fsRruS7gBKT707Ai3CfppkiPTBZuC7nYJ3+u2ZfcscWTftX4JFY
OU3ltMWspS0SZwjfOJF/Pl/78jxVzZuOr+VlBbee6+m98MsH0Pv93qB/3B3sdQk5exEuoTbr6DqR
VwTn3unAoGDcxQ/PI1q8gIgskFc7pBCMu1COVfLcLlmxR22TGslaYIlouqwSGCK/iacVhU/S64+u
mKldaEwdUVu0Nfh1sRSvOoWFveamKmqcK+bNFLzEWr0h/yuE64NhFfwhlgDMEcvSxsaAR22kQklg
LVhi4torG74tQ26ocMkZGTvUVKlB6Cfn6z9J8UoDukBfuyD8U7C2qGlq57QUwAvWM3PxFUUuF/g0
9Wqj3Wq2EotkM51dcIPLiuDobFY3X9Zb7UTFC6Zc0pFBFW2iAcm6Der1jVoVHLRaNdFA1UymKEB5
sqZCdZ2RFuGEWTZL1MZpjjSJAHdVNTVr2WEpXyxaZOvb52vw37cWc6uSUVL0vyfEH0j7z9T/zVq7
HtP/7RaYiyv9v4Ti+v852n8tXfdnqv4szZ+j+HP0fqrav7vWn6X011yVH+WBmMJfSw9FhNR9ItC8
iNw8iyq7hL7PUvfemAvlPF2/z641K14lpOi9nJOwmp9fyy+o5MPKJk/Fe4MKxx0eRrv74LzVy1rn
qNputtryv1Z8ddLVamSid8Tvcb/be9sf5KP3d1D6MHM+B4ZrTx7FC+KkUW/U4X/N5aHkD0fXuBxm
PlreU11l87Bd/ZGRUq8tFylBr0tDygE1bc14UKw0VlhJDOwuSu127XblCjypkmL/B4bZUuJ/9Wq9
3k7E/8AlWNn/SygPZv/nmPP5NnvM8o5T3/1t74UiF2eZDT+EhhYL0fnljhorAJyvr6huUoVjoC5T
YwUJg0/KuguGVQgr9ge24deDbmDtg1XNwoAfrcPpbLYS2nDMDGa6KvMg/jKhUbdataRCjcXkerpO
tgiXAbnYYLhhU8XmIzZhJvSsaFS2abeaqaD5OSzDFUXgYMSIqjojGqyJTTHkB2JNYaZCCTAhmfKr
hE+wkLInA6aLvhAscJwOmFJQElikSC0HQ5QqJ5gCFXoDqGSfNZisEUKS++4VEYKk07m+ZCbbM8SW
dJl8qJ2VXuyMmV0sbWO3R+c2YkFCFD3v7ZJaGspf7EjIRdyutmEtL4tuXxJSD2jAxIQjYrpTYdZd
bcw3FPBjKZx0DZsbWmJpY06ZqX2k+nke316YLsTK2K38hPk3ZfEX4rd6o9V8uZX0nRfgt62Xm8AX
YMLOwXCbj8dwImcqKZCfJmfV0zkrBNVlr/q3Zq/u4Pf+wd7gKJ+t+sPe0evugPx+crzXHwz6wxz+
ooDhiWbyCgMeO6dPK7yVoR3vw16bK+4SSziDJRrzskTjW7PE7xTs8xmx3b2P9KOWwwRjAeNvQPqb
f2faf3h9UCM3314n/OGAK7BLr3Ds8xhb5MjiZtIyC7HDR4CoIsAxr21tbf7IfNFobW69bLebidj4
AoyBqQqt9stG/XtlDH8R7+aBpPDFw/MEVBBYpRLHVNWEZajjyRHZ+11jDOo1eNP5rDOEPsSS5DAN
jOMJM4pXcX7OaLbamy+3sM97cEa9hgT8MqF2UjijVoX1MB+HNQ41Y+zcU2csRFQKNSfMyCeqXZ63
wyIh/JAUdS9Z61LUy0ekqMT4ngJF6dQxE90tQlACwA9JT7kO3Zv56GnrEekpMb6nQE9g4ZnJLd5F
CEpC+CEpqn1/CbVVfUSKSozvKVDURCZU352gJvEs6h+GnnLd7vkk1NY8bvdd6Skxvsehp+TO4cnw
pDvYOxom9w3PIs28IinxAudi3qAjkVj/UGUxvlCaGGBTAAhNIV47RI02VE8lxnib+akt1BI9qTCl
OZaDyaDxTcCHmLcz95Sx5pOZsuuCzp6o2Cd1eUDshb2u5QxdZZZiatMQ34g2KkPfVXWAob7+ZZAp
NSnhnn+rKYJliK1NEXolbzUtm6ou53bT68VZwitRHG8FSA7Oka5yhJZWQvk/EiUae/AToHc4/9nE
+19W5z8fv6TiH1n0jWTQBzj+Mev8R6NWi+O/3aytzn8upeSd/3zjEUTaCdBjRicLn8rs6/yTwww7
AO3+ukk28u6KGNoeRPcWnNM/mG3ZoDMmp29AcTimO0CZPxaiXT91zP0786Cmyi40Q8OfmERGTZPe
JA5sujdUZJ62XLcvNevFzgX9E23DC8207EN4lzz5GHEVoq3wVXqjwHqJtnAMDRYULSWLXrA+1spo
fP9jmY06WDziqiO3Uvxg4zUfSRni3gcju3F0PXEEMqgJtj2Hkd2MFK66JnhaCxMs+sk5mOA2/5NJ
iwbootMxwTvnk2KtmpyJyS80nY2mlxwcgim1LzOBK45pAlGObCApz8aP1Tvb9k8zppOQY3jriySE
Nx9pShYNuSgU1yMVgzs9BN2RdWrbpnbugF1eihwYzqTBPBwnZ3sWOtV5W5o1rWvNvnzHwHI0qI4c
X8RDpXhNCFnHX+dU+RMcZ+wme9baBSn+C/FYtdO5pBaC8h4US1nzjKzTh8h51NvwOd9odQBfjILD
3kJeRXL1fFykYaEsRIp0+kq4qCnLL1AQMLX0D/HvSuG0YIkBxM/f+s1mncONVJ66uBC0KprYpsNS
ap+lTvP6khlFzRp5WCz6WATODv2OMQi/NpiKk7AKwZsQ7awO+H63JdX+8w6TPJANOOv+r1ajGc//
b1RX+f9LKXe2/+5j6T1f2/j5Z5AeP5PfPBPtdDEgv5ymHD3eQZAbz2NHeGcZg3IoWH4mu2gIMmJf
MjJB2CC7wTakjm4L5cYqXk2/xW+u8hFq4xd5dVaZTLTPTN3xKm08tNWZkzYfJMkHl8d3ROqCiMXS
BKCwvVxEItjwDNSSDA8xnbghLD9hgaq8ElMRkWBu1EYFC8xghw6abwmDEwZQALJjikZhFlRNaY6W
Zlq7rsXGX//XBKxgVAvTYwNVajFK+NTNnJBnCXD2GPwCU0+xKd5KyXIuxIgFIoXZO9TwrlCLuE/l
6mDFVDPxO9GJFaCg6SN/A2qx+A9+/6Xa3Fx9/2UpReJfCtLHIoPF8V/brLVX+F9GieA/uHfjIcJ+
fpll/7Vb7Rj+m636Kv63lJJi/wWWlW/3zWmcvaVWZjgv2U704VYcTqmtsdN3zJxolgV6+/TYpJot
QOI2WjjCF1BpcEssgvItOgRIvIZlEhmVMMRMboMRwFSyLo4Ywo9X5ENBbkOJ2EkZrIGp6v915l8r
BmbAIVOYJcwhYQAIw8O7FnUjdGkqKVIFaqIFViIU6rJ/KFSYZjALHxq0mjq4m2aRP4ZHh6SID7hh
iZjeR/D1i+vB/WalElogKtSdcpmPClYqhUlWEhMDrhYrgxNLv652OzOUZaGh9iUWVjlnOjfG1jEv
umkSiIvSNrnFWRw4yiWMhhIHTFANQ2EW9ZcsZF+nWq2HR+HDnmi9wnIQvIyWkiK8UZ0pjA+Ix8C9
Sx+Wewq0VMFL3HXGDHHOUcRqqMXBoAyM1CwLfMzsQzmirhfu8cJDmVE77PVXGdyZFTwD8F1vogt3
4Fnhj2xRRuS/5zI9qPSf4/6vZiMu/+uN6kr+L6M8Afkfd9hzpHqeKBe3V4O8Czm+24T4vBlUvNDc
EDbKRt9vLQcO4DeW9974lyPtce0xCP8lRRRdUuuAGjfFHtbxRf6tjBuApKdkIgR/vh5JE3IZ2iSh
TFZK5IGVSAYRuCdWJI36CHB/SIXO3S6zMCkpxc1mQnQCAV7Ku//874OOFHm6JGBSwW9iC87iI3En
ffYoXWg4zvuN0r2S8A7j9CrGRhomzyHXOfGaMTwydMP9CNrx0W73/df/6hKwdg0GNKaNMZqGQTaT
6wFZ+lc8VUgPT6lxwQ4+GAuJFUSRvOmBYVtSnFYI+1hxP0Tjg5pS6+tf0BpJOpLoWSaWA6s01izb
5LGIIXagoFQyr2gIkilCjCafTPFeFp3A0qLkp9C5FCNlolBduwBGkymVZR9nwKM+IFVTMHvMgiEA
II4wVXbOoNOxo5lSdiry1LvCJ/BSQxGpocjGMfhw5OqZ8PjwpP/+aOh3tiHpeRbnWgqfsh7HPL33
2hgjgsV1WDrzJpu7xGuxj2YyUEZFN+sw9GWG9U+J7cz1Ty92zPjNXem7sK6WSFC+t9Ae40OdL/6v
bMH91mvnS1i/UXZnUzpGHluoo3fQJrWP7yQM+rctEfsf9d4D2/5YZtj/m41GK/7978bq/t/llNn2
P/BxzJQHkWmbYIZbp13Hvjw9gCV8j4knNyL/aLFNwTyX4Q13DFV8/Ed25JplBP8AKKBlhM2fbHjI
baGE5IeL5F+hml4e2Rtuwpub0+NrLseQDjiZdgaDfifTi95hdlGs3pCCIHVEre5UO8aEJe9zA/OF
uWDFg1w23xuKDY5ok6kuvq4GjlIUAyB6u/vk4Oigf3h8RHb75OTQO4EgNSXZO9zr7XUHZNgfDvfA
C9nvk3f93T38+sD77v7eLrzq75Pe0WDQPwon1mUOBvfj/u1kj/SHx13ocdjtHe+97+4exb03b5Kh
R+E1Cj2O+nnuw9iqe28iGPYe5mA13yUUeTzlUKJe2cu/K4cS5M7S4FxqqgqWFELxK5YT2WvlvAy4
cloiXWpnCoxPunzpyV8FDGDaGkwgtXnYZ4wmyzmmDk0ETncP9g73j/uznMcZ3ki6H+kaPSFv8sD1
JtM7C4zXeTo8MlgxcoIp2qnf50ngZCYMIddam7e70B2srl/hRRQW6zfwc+Zc16g/43c257oG8eV5
Jxr5EEZ6lycR392z2KWjjFY9yC6by4QD8q/+VyDBD//6P4amcKI7Y1DLKjdUJnIa0N2Qjr8H6yb4
ZKQa5Ch0QsfWykFI6Sa8a3B45MHw4gXgCQGyp/BOwf+Da4ROnE69kIB7BeHV139S4kd5Kp53kR8W
6LmTDTnvmCqJiSJyseNrDa8mxZ++uEuOy3JLvL+8hbr9Kf8Lc1QFVajbbKRN6DiHX4DViy7ohAyI
Eu2wPyDv9nrHJ4NMaeB36oFC6LldCwbZcKsXZmaj+h3g6aaZxIouF85Ld5Q/i1Kql17soNoEQVtE
QRuZ4aAPczskg6P9zAmCFgGHmc7PmH3ZIEcgzIrIzOgoN/oSCEw3nhHLCQU/FtN7tStuxz9kg8X9
AI1mBCfJUhJNvaNg8TTj1L6OQRtB9ckUZhZfgWgelo0mkoEfzA0nILmC4+tfGAwRc/J9fAJWKV6W
8vUvo4NN5LDk3Zc4Q3iAAVGTn4uDa0cge7hNyRQljElVSnZekUalWgoA9nVfAwB6GEiBCTUVcUnf
BEScCwp6sODVXrBKbGbgAcjDlQpM7XGYBiuexi/YBf0u3oCwOOdeaChEB9GbgFxyCJ6F4gsi/zpU
veTR0AXVrcAcEkDlUr1Kv88mgVGMghQLbrBFBMjcQWMKcw6xeTSTqCKOTRSjX3GSwxcDyxm5zM9i
IZQhsoBQ2Oep/vWfiubmfMWQ93//+d9EHDC2ebRH2SFID/mPS02vXr0KEVEpkNhO2mguHJdkvEAW
VNdM/At/hodgxHrXjJHIB0wfRhksNg+UNC+9EZ2V8pco4BGCXw9VtCnVOySVEcJEYfIJU4H9XpEI
mYZCfZ2OV+mdC4cHeljQcywnP0JgiXz3EBkFL6Jk4clBf3By2N9dAt43LpH4T4CyZeb/tGuNWiL/
p7GK/yylPIH930Xzf0LXsOfuFKel/2TuGv8UyCPrp7SKwlUYCX8FdMA1Oy98003ikDrP2ib2wYF5
wlyxv6GCD6Tr4BrdyFHiTetobopPW4s76bApNtHd7dM77ja7fS+aa+U1zQrOKAo2cO8VKZPI/r0f
pAmuDCmnXwpSjl/9kdz9z9vTntcoj25uh+MdIRqeMhPvXwDGE1t8J9LUfYSNbx8O3l4YbHzDUHAb
1SNLQUHpO+E+bfuQcNsS+pNmNlhbqjYGpz6i291BlwmzlUoJ7PkxFcuJufmGyn1QGkxBFyQpeFDc
dyjvpcBnNheGN5onVxpMcAOxCg4ejMr8sTbo07pYtjOY5vn5jl5pxlgjG8DzhwYjxmTKoKNOc1rP
1BL3oQOpzt9t128zR6f3ioHGkBOKhKKMwj9H4d35+b3ntDHlBCpEhC4US3UzETCRB8S/15AUHYMK
hSJ4DkwkJr8jIWinNGtZE3GPhRApyb1vvDP5GLiWz1zbXAc16nKinoJermbTsYDpuumYrfDQo/C9
yMTuP/CC4ugg72D9FcAI/SiOOAX0nRGu9msHVO1ePbuneqe0k3EEkdIQhBAiXJRwxD1wKTkO8cwI
QdOJ1Ai0zbKhSEjRyMLIDSy4TbajLW4zT4Wv2+DW6jgxd2jgUxp2MVwfnEdmMaFuXkVnEMaUVwmj
hwGMFCoQ/e2QKvmVmLh1WSyGOthwK5RA19Wq1TKpl0iHVGPI/xlDDMnidvYfpNvr9YdHA/fv1LpC
D87QhCkx6SzijkWjpSINAtK+LYIR6ZXv/d0V6f+/te1HPAR4h/NfzVp9df5rGSWE/540qXX8BuCD
9rEY/sX9X5t4/muF/8cvGfjvgtnHJprCH4ISFsd/q9Venf9cSpmJf+FGBq/uEhjOj//WAee1KP7r
8GN1/mcpJSP+iwRxGiKIU58g/JhwarXg93bixhD53dDE4354zzIWNBbwB+wTBkOTb70b4t5QHJzM
Rpxda/f17DrvxW0UNjdDYecYI/ix5+BR5v1yoxEm09umo9i+oS03CUWaCbdkjIsZYwzh6RgSnGgW
l7vOhojN4ZdQ5VcdRaxOBFciTr1rj080VdXZNRXuEzU6ImWi4m3RGir7jG4MN/SbYsH9c3thODJ6
GwBy/wZfCdPN2V1AMlWzA4DiLz8wfBd4gEXAyk0A0n8wK54jViXFH/JuCUcq63RkIDjpH8sLvC6p
1TVuxP2/HwqWA/js4uCE3+/9QIEilsxPTBLbt3G/Ov6tGqrrER/2FgjGYqG+/eNj2aBiNcNfrFHB
+9cMpFPvEzg5XYVzCmZ1lkwoSEDOhqEgfymx0aT44Ffa/7f3bb2RXMmZfl5g/0M23XYVx8XipZst
u7rZEkVSEoW+uUnJHlAEnaxKktlTlVmTmUU1JfWT4QX2aYB9XOyL4CcBqwdDL8Y8Lv/J/AH/hY2I
c79lZpFsSpphYjTNyjz3S5yIOBFfJF93O5413yPTkRiK4OYKncXGhUBrucupT3S/YH94Fsa5oBbQ
Ukk5BoNJ/LukK7KBpIwT13MAJIc5Ks2ZwSamTYtk9B2DdRzwptpwbDoUtMjiJII1l+Yju2Bmd+Yk
1uGcZdo0G6z0Vp20DrJ0U+FCmYyRjFgO1EThjc53tLv96U/SzJP4u/ikSoqjvDiCSuPxwChbx2v0
bE05TQgtlI5rgAcRBM+PE4hq2J2iyItSKy+YcjebzqpwQd1OPk2y53hbRxdVREVpm7N31moXf5qI
4dYC02NMzbIomyXnOdNlerYLTBrZvuBmL3JUA/n2ziJv7UGnyuOS2Rav4dVbWo1FcKm3aUW6sjQ7
yXW8ce5fxixgNOilPiUeiiVUzuj6qXPYvCnP8q+7wkBK00U3E2q1Fjzky1wHrGQoIoYicHBYIT4i
hm99VkvafJFGt0Bz1irHa1oz9FeU8avjWDvPWQOWno7zeGSb5nXy4zcJarBdwnCcwmDlp0V8AqW5
GzcfznDwPRmn+ThFA3n3yzAeA5eDN5I7507eQ3Nc9ePPDSzhHhFIdTaFB+SG7LR2Ex/YO0xDKy9s
FWymNznFybWV2aLF94xWeBXS8TGwht2HKw9gyb7I2X0JvzDNGU+WRPxKzVZOz31a4fJ2DqsWZxUy
TE3b4le0ptoMFecR5x4qxlM653ovahi+a53yviM7dF7OfcDOddR7+Y0/s5M7dE4bp3KIldz+eDCA
EysrY1ovGkCzuL2SK4avFbstQPBX/+s///Cnf/33aHNYQWfTb+JCHMJ6QrEl+ZL02WBr06UWGXvp
M8bW1plKzl76kttLTeXRvvgy6ktOZeJvfRmcpaRy6Z/CWcWqsvPBe8f03KH0MCNrfEZeckrH8TDH
/hmR9JBOf5qel8UW47jcJgJnlE75DuHFdg49XTnQB/wI6rB6JGo94oUcmkV4+/XA6lcZEZ7m5Y8n
wF6VZnLaTGr8UhCqRl1F+48YEidl9JzXwfHh5zGNQU/geWIpyDqOEjRz95y++JyAhBUPz6IuGrUd
jRJ0geu642G0rEfm1ovoCHofh9PbzmBbRZwdfxZ8tLnUehIA5KYM3knFl4FMhzafgM8789U7z1Q/
5FP9sTySL3+MrSmWI6pG0TjBow8/jA4OafDYe+/wkQ16WYL8z1MdYGhcj3pCPGKsjbq0tcHYNKOs
OrInhxZkjNmYEwyRm7/zbTCZDxj3vDCzsVeHOAAeWHyr1qlT57ShRnSaNLKQF2VDbf6V4FWGiCc4
0kIe5NN6tSW2zpfYlmTR2lAQxdAd4UZuoB0O+9dtIhP3E5YQOC8/obAawEmEtyy2tIV7BS84vLDV
fhJtwK3D/g5mUvXQFmKpD9hBBis/+tu/jaxP7F//N7b2wk1sNb7NdE88vJm0lq2W16x/mVvfsXbv
2uWfWrkb9x4+3p0knnf+T57XLbbII75FXuXjyx9JwGmzRaQ0VLdD0DB4nOKdBNp+ieIjEmDJiii8
qWT5jZtJqodQN0RW4/5+UOmy2ODWMzumdp5dlNpIqkzcSuJX49Fi9LDVMY4L8UhkY2tK/Gp3iqCR
cZKlgs9VmdWHUP6WZ7z80yv16tJMVyrJekK5FgvhgrRrWXT5A6nhmi1Wa40ab/qeQ7vf0PC0au46
1IUgrjrdh5+rIblaHOekFneJyvx9awNMOReH2t2HV8o0tBG0FKojrpWYxkWVxuOyX8Xl78q+NtRH
8Xjs3kNonVW6C2P2qBems6etvnSmx3T3bBaVYVxQWO4wDRcwH0lGBvbM0VaChElHgFi65PZtLXW9
6sYcLG10Op6bEdsClx8Ljg7WMNLlU+eJ8WTYFIfK0q2O3aKa9dO0vvAObei1vvYucdI4UDgeeTll
tn7p6Zs8zbquDb9AzuPet52NjsfSv6/A9ZqKLb35TeNttxqqPVA2czPh7jWBZpZ96RbvD20RIyKG
FtqCFWpXWCZ0R+jRIJhNxWPH6pKbB+ekiL/uLmy9fLG1ud9lzUcjCWh6BO1nL1SsDLNQ1ocFexlq
S5E3RJk4h1OywlRqruQJOOM7c4c0zlIk2EOXF6Ok+Pii2zipLlm87+xBqcrXlzlxQhWcJRhj6/6Q
AmndH4Ycqx+7O0buy6sXr+z+W+mXtZ1cT7d7PlrV89CcZuU0t1JoUOXDJlHG+Ox2ahEtSvQXGhfI
8C3QPZ4YSR1IUlD0HsicjPEc5+7s6mXpPKrwB7G8zNtxL7xCa7rnu5m0l6e4p6y9mdzhraZzjhoB
x5zGP13xZrLKT0/HyV4VV7Oyez8dES3b3mFIWAihZE2ivGTk97kn0KeXxScYwRFzO1tALebonvnG
k7SMz23+3zOyxgzZ17vagCkoDbluFOvZbzE2PMT0Frsy1Y7d3ZHnjDSYHYf/k2ck7nk1ZEaZ/o6H
fWjF6d/YERB6JvGoBiiJERJVcp9cp/jFXotxEqrKegJgVGZeHXq5KMbt0J82Y1RyT1ma6fA1oPqi
itJUqwEe6c4P5Jf+NNr/asA2V7UCrrf/fbC2vvKBZf+79ujBXfy/W3l+gfa/nsh+17UMbmPz+yw/
1ax9vcu+zuZXd9R/nqPNk27vk84DD8UsUFsYHhqlmoJks7v3n5XsxyoU9pJ1MrBogpKbnda3ESR5
sQHB7QoCZ0dJmJhe4moEUwrRE1Mr6I2m1iB+xi3LpFLZQ5YMNSmEUcE8gm2rtC6cQYM8THGSXSX+
8awcxgVC5/ucmsU+9aohWVaUXdW9LL3yqImVB7RWTTk7FhWxjGGFOSQ1jOTYqiJI2/R3+O/C33zL
C3n3Nwsemzk1EHnxT3o5OiTu1cuy12OgLJ8C3XX/bjl1SgcGk8enDRnZ3/th7NTNIs+32LYigZIR
qia4kLVK2Su3So+aRpA3A08HbQ9csjaNT+nk666uewXnf0T/9z2yBPPreJSsKhvCt71XYe5RrZjH
oGOab3xWRNqjN/Gdtid5MUHYhBRFVXXiXv6UNR24/DbJq2jRVbXopfPiJdLyyx8yXgNeCcO5lMGo
Xf4xs628NBnTuczA9bCdJ2VWfYbSeoPu9xYPUzm7GvEIHZJG7b8JaGRYlIwWykS8m2RLjSnrHJNo
bQ3aOA108RjsiliooTZoLXilYreQURoBYnVFOOEexUYhe+7L7zMGCbz5+eY/M7AUTVynRmtxYDZk
wYPB0Ag6MveMy0Atar6vNYeqvCvOoHjTuOWlp5jc84lPkap3MjH3P5+k55c/AHfNIam4ekmPuiNn
Q82Qf+9DF9SEv8qZjkohlPhIQij2tgGkBiTIiESGUXqScpaMYe8ZpTFAUgm/xeBk2XhHXVRLCrw1
WHlo2UEYX2MGDIcw1eVi/9oLTqG2COzxvpyI943fYq4wd02BXDYYoDKyu6DtTBGanM3x7rasYBAt
RH19HJaeVvnnqO9b9OqZQ9HXktJedNqR8ynBrRVzHTKt3ckkG8ANlx0zBwtNyTTlZt4OA21b9VxO
Xs0M++0rgS0AX2Y9JlYgs1z5lJ+uHxKEKWTzhnc1eGIz6CxLdah+VsWFvcBQwj1OgI/Z1yy0vXax
q/3otRbcSqAZ4r6cipsklPwvf0CtRtTdSxXg/5HjSVIrWSsL4V0MmeWTHA88c2bwe+qbHg3NMvvm
r72mxX7O24PkhmVm+dfOFbnMIpwBtQenmF+fhXJpGI5arlBFbSyb1/rRS07YUYXCFotm3cyILIY/
UzDdRBXKxGdsT2uQeLRqFk3S0yKmbdvj2P4l/AfF57BpT9FZIM11NOkjHYgYn/usObsj+wKfXtsn
JT5equjKGTUZzVh0mglWcNmy7BwBnwOvOaPyoB99mWYMcSwe69BwQ8LzG2HwuJLQQ6l3bJRhK9mA
ZOJhd5NifJaepuWLvNqZTKsLj+MGPso2TI0q2obRr8Axg4897gaCXotdKZ4Dnl3sOF6vg1tXs2vr
7MgOjM0hd0Wko6DK14f+cq5iz4vDA9zFJDUPVHyarll9glqNH6t2uburHYkYo4M869gv2K18E0vD
n9iyH4LlVuFC+Grn7TCZ0rl537VgIuOMfDz+mO4vra4Rv0DmTN0FcgFC8q8f1JxDSIjVeA7NpggZ
gRHSr0i545BjMkW1DKJOQ6nvamRYY9QmMGjncSt+4vlsXKUwlC34CnYmMs81OR+IPYG+UeiROkyP
kZmdixMx+YjrsBHNXESQCSrt7GTc/R2s44Hrvm7Kq8zNo4llujG+RENjdEm/tQAlJ2ol14bNzqH1
gB1MPmJV+s6Aj1HrNslL7aRdlqE7iV+KJ2Tbw09Y4fIKO1y0572cj6YY5k1unYpq1OY+CzVbf3Mg
yeRfty/A6XdLx4BZ/WjnPMUbRY67LMeMzlFViEdu4tY5c13jOKNh39f4bC2uOfIsS9CrGh9ug0od
Cp7euOz6ETucY6LSLofetQcg0J4G/jxlHECdubh10uNjWoju2kEqjOyWQCWy81Gsy2lJUyKnWsd1
medn71lrNcZD1dmYbT4eHx+PL6Z4UNencZ7s0GMxWmKd4Rfc5hhxv304uPYzN/spHhZsZYtVuxEd
HHp4LvFclWX11XVwiLXVp8dH41Lp0VnVFrk9vGzL1a0aYK6bFmtGZrXWTsusdVMQ8OrBp0Ew4ORA
n4OQ+9AVfIQstruB8fYdUjfDijOsYussWwQmtaOdQ6V2jaFvQWUPefMceiuUnUbsHJP95jqe1OWj
r8eYS1vXeVR9hMZxP/XZNmqz2XTSW86zUFxdgCytYGc2BILJQxitZ9YgZTnFqeIOH65EprVd0wxY
oaD0ey3DjlU1ypKXXftWBpVgx5jy2MbqZUq1iWsxzn54i7uZ60sbgMQNlyBIjlDpsS7WXmy2Rivx
Ly+PxGZqE2vkM+us/jUuVa31F/HOFfjpMB9ds5QDRcyj5zMHsHNvo6PPsErl5bqJ4RH9ndPda8dQ
+F2QiHL5vTwUWEAZLuzVDPfcq8Pv+x9io53hCyHVhOBMnO/omG/OqZPErqMd9+vHL7qOoq1JyaZs
8WMbZK21v8kv6LCqX6+7FkFoRQxA1KDw6CmaYGkBLrSuEqqXipthaS20PCH7DK7YQMdSlbgvOXZp
dkjJvGaHgmywFDr18B95gfx69dehYmFSY4/V1WAUHZ9TFmNV+CH1I5M0sTA1WucUtiB09AYpk9d5
vsmlyViWwrOpeTvqnOaXaF+s8eWjxA1yG+I3VbYWFgx1jkfaLb2X8bONmMJ+qZaJcNA51VyiP7/x
U8hMr1k31tYD9TdXNVq9cbtYZu6rVYH9MzPOZQ7UyujoKpy2YSNkIf6Zi6DBVhA3GRcP2P7SPLUa
NhnLBvtLrf/b9mTTQJVVonDgKT4ddTHd/JaiV5giaS7kMewKCDzeQY4nx2mMQwyL+DpSj8W7JQGA
Y2bU0BPRlHvCBbmnYiqHebtfIn90HZnpKnKS7I79vQbTUDcwsW/UYz98oPcC3UznZ7yDJ/aOFiLe
0RydYeD5kY2wEjrLf81ejgH/P0Wrrhf6g54G/7/VR+vrZvyntZVHqw/v/P9u42np/2d4/V3dnw/D
yV7Xl+/monx8FpeipN/Gb4qYYlTvIwEsVTnyleYc6Nsdv6h4ILURMgzOvCkiSOuS5okJ0rrQ1lFB
Wpd43bggzXY2Jv5b/CZ+67l2rLEiVwKKYeGCsZwnOTBgBYa6dUrj0gy7LYVSuPkBYXSggc9iHy1w
z3B/f/5y90V0gRJ3PL78MdZCj2vlSTt2Gc+ctkik9gOtRjQNPAVuKkJekwI6RxdOWeTShZ/QKN8f
Fp2M4GWde//4jAci75uFyWClOi9N7+r0DD5hhX2/knTpK8jvDdMLenXWO3EGrw3V6A8GyTiHnuMN
Hw0ALOh4NNqiIex2mAWSab2v2oZgMejoguQEBaHzfHwO9OQ4r8iRuFsAIUsKQq0GEYlDmnn67ZEK
9AGQWGisNSHpYBF1olSjc1OI3gHCej+o6bcdg1iEHOxgmRIxpQgLaUXhjZHVowjiDdKNSyTbhJDB
gPcB37JvMXCRv6kKMifUAtdtRjUhIEnNG70mGeHiMmzGgtKUWMb1EWi07V2bbji05LJJ/HawuiqC
4Ohmea73MsPbzhuqqGB7nuSZLQCyijwQ+cDp22mTCQjnlGNtfUW0jTZxjz65MP6o8HdB/D21ZRVM
aX6UTJLilHSbjc000CXbmyPSAv3T//23//rPPwCbMkmT7BsyreFw9yT7OCaLAvJnI0LuTcadOeiQ
eseHNI8Bv6WnsJlArgkkrjRsNrI9Dj16fwGf9TUcKvQdGTUn0oJIQTc5qoDhogO9KbqAwa3RHNmN
TeIzifSez7b2MOrGRZEexzC2GSmx+bmbOcXZB3oPSP7lj3SyQjaY6QpOYqn5HRJCK5zdAnvROghn
GUEEG3v3QO7Lw15kf1Kb0fORL3h34Kx04gg8ZCCUfFDT0eOo5kGrSEIZo/7wIkxHg/pa82PIc87D
5bC6NYR47ZtrbhvUvwmwbKOqxZp+SNTcYKv5eDBrXX1wfGFnVA/OKFY6p+YM/VmAU5u3ngRSbQ0r
2tZLhWYpAN4y96T2wMLzXGS0VuPOZ42jHvcoroBwnXVDrn34hBFvxUjcE2jXijvh7AmzbTMuZOzi
LTMj/9TVPlDP6wSYnyoluJYcRzEen+bRCZzNHgNDTUfGKICt2ZPLYNFCzdtOTtIMarmPJvsTXCdV
PiG2GLbF5PL7t/ASHRmZOopMYWsHzzAz0yn7Xsp7Amv78qfoGMh8Lzolj0XOAB2jFw/UQ5b/jRZq
1o21R3wMst4GFN/l/3Sx+PYSQeGgpUAT1B7F9qFHv7pEawDmoyGS1mma2I9bG3v8FQEc1JqtfaU5
j3C7MmiGMVzYSnHzqFODQdTp+4zMGpW3PEzNAdfhMsQ9zaTNqL1PgRMCUWyuZJpnrRxa9j1oJ0qa
eO0KK6lxjNIMvfIKjgz83kfnJRCcImXYxInVgNoBapDwKbactgJ1mlR3tyNiajFB03BiB/kMhiMu
GQygZR1grt0GEcCODGYIUPWXKmSO2LpffhmlruNcwH5sRrpDZ5K8lLdtKTpap6Q4uJAwkggp6TtQ
eSP2UKom2IVR6NbL8OhAYXGTzk7WGOk+qtpxEXOXtBHbynrtqv5micw2/vPedsm/jI50Wjmi6x7D
trG2qkE5GZSByrQUxodm0M6g6WH9UmLD95Vy2zfG5sAKlWZsgV+gLOoVQntIzLSl6NqjXUMqdT3W
pJzUXhq1W8jZbvLEZ0NJxwvleF9ya83NKTKvKjIaO1d1eNkuD1kmdWK9iLqlQ6vYcqn/Cl123Vhn
kj0n3duGK8F6E0tl3YZPovVmoVabjnj4xrWpMuUBaEFSHUkZ2BMqQdYgxeANv5gMAnHAnFsW4QEL
9sm/y6wzTEvNJQ1LcWsLuapAv8DKlrZHIm0nyQaPJUMsbCkwmouG0b5G+dBcyDvewyQgPZUX2dAV
xyg8Fzu99tJsWMCZKYMHlh6d5dXZ9JZRfe9Z4NmK9tdcjt8WhDYPVeISAGe/IyB6SQUTfrb+wpPw
+ujZzcNYC6l9xQEU5rz6Cb1Tw+wR6dmxiCUQG+edJsvOCyYvx1YvU5ZmwcnveJjBK6x0S/x0geA3
nqIBiDZMDVjwPf8sEAnwQNFJYCI2qRafi2GlYZ+Qd3CT1dkkZeacXn9+wd+apRuLStXkJUg+TDo9
sjILApSOuou19krG1VJbdcIIjqvjPC4CV2oNcWlwGouJQjLQ+kRXltWsRYgacwzllGF4UO/MmRxM
OUMVOmxjuq0n+2QW1xubdJyM8+y03M+fx9kFLSnyNtSBYG2sQGGX28q4z0StTAwUPGlKIHR8Npie
UYTdIlWYDJkhCzQRh+kzHPvdMhVhsROCBognU9tTW2xJd6rDqPP0tTYANaUIB6Gmz8FA1PSV4fM7
waToSjWoD6XPcgDP0xIFWz8GofloNrLMHpMrCJhmYOjEosLnnbdTCh3PsYOzB17sWzmLBGyUAtsO
nLZOpxbdnc2c7gOb2gl2UKbZErcXDG25UNZJuiTseesMfFtigcLwTC9/KtGtdVkuL9rMrQzqOdV9
BaXkVIgvlNOfNxWt8627MUIV4Eg1gqBv3/DK/hnXLVtPU7lSbmb9ygCDc6/ZLZHzbsne4pK9O9Wu
e6r9gje3ivZ5I3ubzc08G7vcpyx3O/pWdvQtrjtN/uCrAgWFcoaaNHZxpHWYp9iQ0ZcqY1U4na3d
wPKsFRUFaAXD+61ZFNQ0k3JoAoWNQYW6csfIz936DeTETt7gr+RsaE4dG3ZzT5LRZp8y2KMg9YzJ
xbhdkBfIsWVkuNvdfxm7W2w4uV5GV9zlP9v+DbECvIAXefViNh6jBb5a374oy83bmC6UxwwgLMur
GOW6CSxQQ5klxnNDDJ68E5Zjg3tiHFcwF+ZVNpb5ihdJi41nWHoan596OtCaYzB2dntC08P7TNUk
g/C8Hy+zUPw31Kwu31Ad6OX1wfp6wP+L/W3Ef1t9+MH66l9F6zdUf+3zF+7/VTv/eK/y3v3/Vh+t
ffDIiv+38vDRyp3/3208reP/4YIwvADniv33sjiNs7ScePwFrRh/789bcG8aV2ny1StypiuBMot6
0B68pbcgnkhPlpaizVM8J8jCLY7Glz9mSdwywGBDor0qL+LTRHM2NDfhdd0MdU6jxmWOe0JdyzuQ
lWH7Bc7pDcgKMfwAD2xHwMM5S3N8APV7Z90R0DeozA3QGU5kIWNYa8g2Yy3471lcMlcDpB1+Iw5o
jry/dhHANKbex3jbkVLeRbCak6ZaiFuHlZMo22U9ChX7Yod9CaqMeAm7WbfDA8MdsO7Sjek0KYhw
4BTZodpYH67faUNsoNZDX1khgYkwi/0wwm/cztH8NOCfBFvb3MuW2Bn69tKYQ+mIyKfHvmkOe5eh
2V2wHrEFH4fW9A2GppE+P6b70tr6StAmr2Xy+czgXLR53aFIqy3NBn8vSwB6CfLhxGPtxmaE8mYg
3KAHAIOZ96cU0PIp8PenSSHAFulbPbj8fbSpK6wlbAtFz+EojdHQesx8cxNprbk4oKBQEVueHFMr
hxLxtMJ3y2q99u2NQ0djlXJjW2pIy82jGeFefwuFMNpZI/dyWLtpxU2Ch3gEwt5RS5ORAG5HZWd+
kZt9tEtcejpKT0669nB4QNCc4hqApgWw5QOmImHmAtKxHy0WYu7OM8vyKEePkFJEaGBdKnXz3Trd
ibKMzJKvieybUm+N9SO8mN/0cU7LRzdtkw2j+O7vhcdaS82QMbe1E6QKpIyaiZ5VTnjg4/OUKa0Y
D1IqAz4Rimo7ge3QFVX13AU4H3qkxabR2U5U6Yhsci0kMmGjtvF0recYqHV6Xrcc8uhQ5vqOSw4z
j9p4qsyjLHrIBkW9q9elbs3II4oFwSNbEi2q52mSXf5QYLSuMuG7hVwk87Gyh+hEMWwgWZ4caCB/
cVbBVEw4ugZR5DgqZ6qnepgwLXafLKx7DmURJEJWoZsMu8OBX4ixoEWhiku8uKNaTD7ECpXxGzKk
LSM8UBYJ77BKxpc/oEk49yadpoh3j/GpNHfR3xPuwnk8vPwx5zgOMMSxLBQmjFaHgm5ASyAWcSfh
Q7vwKQyu0MiqsHoLUmstS3sm6Y/hkHABVDsBxqLCIelgVNSS7NryJeQ2Mh5UC0QbcnCdlbI8Gt4S
hx+mk5t+03xYkw0DMIynsPzjqDu8/GEEr6PLP2bpMO7JsripOkXxopsC7A1GG0K7rArtEKthH0b2
FQtaolFSSHhMAenQ8EuWN7n8CerB5Tcik7lIM6QsI4wfJnpcLvYwuBGLHIbnLfZ+xHymHM1/kZ7H
2DnJboXoApLqqIY4IH77IKIN7vBmXpLH4x+ESB4nL0agA5OicTvs15yt1s5yPMg5trGnlfIQZxiA
LnHWC0a3b9hRaVaqaC7MF/aeIsonkPpM30ROb7Rol9xF3ZUfNJ8LInTB+OoqtqHuy1sPOaYPaQdE
T4wJLOkptOnyJ92jWycZPeQEyOe0IpIm6cCAbdzLH4DgEIEKbtxOcBJxvMMDrl9fR999FwUTatt9
MTg52oVVIIUqJrgczbHbQcKANJ54dlh5wg8T9ihF5EXEEk4hegZJoN0fIi5IWvuIgkCByKyJodNn
rNEDIwx0rhOCmoG3t1dYjCPPQzJ4/7bOh8+PfmgYwVvivWsT/+sQlj3ucCwiLtuQSdHTRTLWk2t4
tAnCmxS3K/RKUfJmhF7h+4XxLpjVvuuZZsrBqgG/Sjl4LyjqdkGCwt2aoT+DkqHuJOC/EAkYF38L
8RaTzSHbYvI2Ln2wNJ/FEYNcLZPL/4glk8gAbhFlAo0k0Jj+mHmyxyAGJCrqY2T7BZ6kMM2jbqfe
KRAbeBVp2jd8Uq623rcQj68mEyNVv0GBuMcp+s3KxcJNK480NxvD/U0Ixabv1txicRvnuCv7xjW5
xIUXQVtHuObBbPSBa3CB+zXD7v5intr7/8/yyS3g/z5ce/SBc///YO3u/v82nlu6/6cge57XyGee
MgyaL0rU3EWw/QXqKEpgmC6PKHtrAGJmxOX5UINM/BkLdjuPeYISAmtNF/AtcEVkD0YwmF+9ystK
f3ODNg7XRES2WiVMEEw68F5NEPAOHQXXhCCM4DSZMLigo3GcjdLs9GgKPzrcQ36TsPZoncgiIlZE
FGsYNJPLH9gPVmqO8IRaaQbfVms/gHL6EYIzFOfMqpDbNCA2T0sAYd8o5FU8PuJnoLqpZ/EbTdsG
SqlUKJhYLcOaLJqPtI2EJ7M4mZSPIeTRtGuhSmTAaGA82Z81iYcizCrt7bqERCNmhUJzYFSjLouA
MNq0LDKkiv7D4CcFLMSLBwGOIyEF64NFlI1oMlZoXT5XeiOxWKL48o8UjSFFhRPyiaRfRthdDHQ8
K4C9Wcar+3KZltpyli8lGQz6BTBZueuYaepsaux77484lA5b12ew8ImLPDnZFmbdDrQebPACDRIo
r6vjJB4dimQ8d8f9DoKjGOgNVGqWiVXBfbR+F5MkBFgHfRKm5G06iUsZkTeYkqbheVruGMt8xU4m
zHtfwXThDYE/1STlq9eq0UyGs+C/3fGbWBtjgpbpjz2JRBHbnjl7Jb+JaQtU7ylYTqhegS+h0Xle
fc5s2Pl70YxA7V4Hj4CS3IwmY9nCmESRbKS1va1+a8RQvdQxaORL3WVavBMkSytfxnEz9rbC8tpW
SGbknspLJXCtSivHJFvCqoeZfotBxF9yK/DStCUvSzOXtg4sp5YyK4vPkokxFjAMU6NEoKUhksJB
TI2sBoX1Z2JJ7Iw+arQ1xh6Qis0JfuRdxQGy9OdDdrw7T9VSFyPquvur/X6yd+IN7S//pvLuveBO
u/0d1sD70QVP+O4mqOMmHSDTxJzF5WZ2QbvkQNcX98Q53DOu5A89+kGb5+IKGzHafQO/0nRSYfEP
SDnNnfO2k3IoHPRwmo/SDC/k9GDKcnSI/ph9cUG4nfYytyyuda33YNJ1yztKn5RpauaTdFznfmZM
X3jEUIn/WWzcm7rWsszpimMpeQ7Z34ddrezees/TkC/YXBNpl9E0rXb6ltbHwzoqWh/IQQTVYMbB
KlZ3TfhuUVZNfJUSzWYFYB0sCM3SZ9mYVbM46BqLcRKdX/4YRxg5hNz5BBA8GeKMkySjKCcxqSvh
j64egmRRv28KSFdNIUlka0MBSVqGYRSDq8r7TSjw8Mwb7NEecj1KqEanvQFDw92zF+r1O6iXeMUu
Otpv/WQ9yyeJg86rTjn3UBNHlG+0WkSE13UQ2pnbJZCmYTzG47RwThpPkIjGwwcfCp0MwmpGtlVE
FsqoiL/JM7z6lXo6UW9KRirn6Xk+iFajCWJEVQXe411ED/AnvhglYyK/TkU752lFwSgYHBewQFUO
R9lYGK+RQM3pSRfNOuCwGyakV0Fo9RG7flnEFgwp+i5iEdsRhJhxxwYLIQirZHb8PM+YKgA1wdVL
kIsvXFYPuk2MK88Wj0aUrew+QLY3G6lsVkYRsegaR657gvDz4+Ok+hoojo9c96ID1tUeb7vHVgmG
fPW//vMPf/rXf4+YgRctaMLuZnhJf/of/yvCeBcuCPiN8iU0TvKcEM7INn3hm8LqwBrvgAblh61m
162zUupfLEnCZEea3KOdtt0IG8CWh4cVUJSvp+w4vBgqnpPPP0wP+DBJAckYJNa/2iGqV274B0gd
LkZfDFVBoANeXsKsKiSVOUwcdP8TtFquEgJWmgCJOidcHSQnn8zG4y1OvKIu3Vf2IiIEPaAjIyTp
Y7TPYzrvZISQYKW1I++L8jZE85aeTuJpV60G2vz8o3cEhamYf5UgB8RwvFlEDMeqRyZUF64iLbyZ
jX0xVSk99dRI75CSUFYYjlBGoMahXDScRj42wKFK9GGnbIEhotTKCFwrX9P51uSl/DqWuJ1dBTdq
KMSEGneLUSGQwgUdRgOyCq9Jwk8MYyQZYARrJ9r/NBWhG3RDOTnFjIgxDDwrUCNJEjhDeWFqwex0
TOZAhbYBBL1zBJuQKsOxBxR7zpRAfAEwxJ9tgn0gr8ECGJg8jeQ7WBwLUWTLeBYeY0bgVMr4jTCN
qKu7jxjY6ysr82gbfHdmrUPBHZgRloJGj5ptx/zQ9a573lmeNZXGu9UaZV6/zcSYJ2QnxGqnyOdV
muUfwQrPketBmK8JipIZ5rqwTITQkcq+ttVMqvi5YZmE6S+of/oL3hntKLHlV2+VfIoWvWJJMNj1
n/7Pv0X7s4ivOh4R/DytD2vN/tC9c7bJ8Bo4vBLJBJnp8Kt6DBMpRFjGUvCYFNPi8ifSt/ENpJw3
hrPL7+nU1O8mmbaGse7o3MISxQoPC0RsZibP5GwBtFI0Ol2ELlEkRyQF8qCLhaE+vq8ouobpzmVO
jkTE5Fr+QykrRikuPWY75YqYAa2yqlzz6DAX6YfRivliME9Id3w0b25Kw/k2rWrNMtWbX/bNUm15
E7MLVTulORqONl4XpDr1wekt9tNgPANZfJJU5ymqJkjya0Iqq9WYIb541V1voWbwXxlwwxauhdSd
9k2gIm/XSFohmCEFWarUET7BpZ7l/70zyny9m8PcqLr02R/4uWFNj09cj/zpxFXxKPQph+eDcw6Z
1wOUzXznnHHuPQEL1+O810+qWjKbnM+S8TkcF0RbSZchudhumZyiKQEwCSfpGN1V8inzg1nsEeXl
Qd14WceX35fpECjWGxjmnDSWFBKX8W7CvJprXlGnMkrhMECxJ4MjJyFLa1kYuplnXGysCuAsKKh0
jB6h8OnVZ69I+ZN8Q/wS1PTp65dfvDraevlia3O/uwhvZUl7//hsAIQ/poVH7kxdzLC9u7e/+2Jr
P/q76OXr7Z3X0ce/hT/3dl5tvt7cf/l6Eb0lyxQOhLepahVU9vwCQ0ZfoHqXCoThiNJTxG+HBsGn
tEqW8Sw9xT53J+gkQMfHKDlB8wq9g89gyM9hcFdXYTzHDNu9yCdTVPoS2gsaNM3gAGo8cdQG28oz
tlI3T4vZFIe0++FXWzDwM+DT7/Np3GB+oO6pIzRIGi1XKmtnP2kKVoztKEmgVLmabxpUy35FOrm9
Sm9XpHm+wKhkyMfOgyofKEU5xgxH18soPo7f5FKFqClh/H3id4lS53kkdWb+L32T/Nt99CmWjYrK
mqLFa61kkaumYKULDhSrHbqsUMVILHIbSfRfll7K6FaDbjpp7K/QOfONIl2du92MQD+s62Anv32r
5ar2w2XLderXh7Gi5NJtca3Bslmr3hOQ3Urhj8xus3P4lY2WYO/c61u2v93bWPZeIDWGbsX4WWho
RR0GAmSbKTIhvhExNVFYa1z6j3QWE4DMMyiVHyzTalgXca6H1WKj7oqXXqe9MvQvMn293sXWt8hs
TXoWI4Qe7y/nbs0ZXXrKfOyIwcdrZvgXrfjyEVBiWBA+jYer2bCnpUKnnFWQ6+0PIOHN8DrHuQfy
SzS2KZ97fjimEzWxHU3DC2WJUnuWtWLeLJMOwVhp75wAc47JhxbLUHvdlq16ide3URYjpsBYcD4j
ONdLHnO3BKkdGAc4mkb8WGI3vuw2ilFsnVVA2Acx8CFugMyfiZl5cbS9u7kHQ3rQefbFi5096s0q
rJ/nm6/3+U+MHPV8d+f11stn/A0aNHz+xc6X/OdD+PklJBD51+H33ubHm9sv6ecj+Ln98vnui0/Z
7w8OH/tGQhgasQ6PrfNchQziDOhERDVCaOrmLssFGjAclNO+6/Fm5qtH2g/XS3K7HjlOyYUe7shL
PnFTA/U8QfLIp3kRRw/OkpPBQM3dgfiKYm58iBrWf/iH1vu3fgJ0NjxhPDp3XL/8nkfdqXKTTZKl
va8ZCxqcBWjM7qg02dQmlUMbxGHPFBtU2tEY0Jkr2tMC/aLRLsW7JD/Twrb7xWfRhHrxWapahPQs
s7U1+tGOsdtohmnIEABx/nl2WOCEDJqXusv4SsdfLVKipifR5C0N41BfdV4vDGkV9f43lsok4n9B
x4/x71ZqG9p5RgWuNViYF5nnTt2qBIuWYOsTLei0QS+JoAo3DCKsWSQ6WNbsjflGsW4K7PBqhHep
DbKz5dtb2bXn8bwQJddm1gyTYKVs429ulrW7c/y9uSfg//uK7GUFOs41QcAb8L8/WFlbt/x/V9c/
eHDn/3sbT0v/38e6528Ld9o5vGav6/YqnFSDS7bWX7XIK6BmyYhd+XH4CUjISiNpTVqVmIZsZgwH
v6C1TYaJMvQJt0qcTBnqgQKkGsDZdPkDopx0kxLxaqIsGSZlWsWZujCFHFVKWUEgmM4qNDlOUSOe
H4/T0xhtJd1YUrJ/Woxg1iZgZLAlZTd43xk4HHQtzYEGCHRgxbQIjNshulxAP9g1fgWTQ75ROOuF
AEZC/7geeukeJ9x84IUMCG61xlT/3Fp7NpXFi3MUSoi522zQvoCMcxs0HDY2JVh9NpscMwvVpgZs
MQg2t3oOycahPK7UiraDsC3R39xmnKJk2zApZq2l9D5qqvfTyx+ocHib8+C4vPDncTmcjdOMvn2C
jr/875dAijqHTivZNbLCsPS0NzhKBIbforWfuFCZnkWDwH/DKj9KCDInGyLMZ/uxC60bcsuzFg6v
ibxzVGVOi/JjMuU1hrdVW3D14MV3q9a8NGo5rFMuXs/JS34WMHxc1uTAW+LSmBsQO4JWQloNljmo
2wjiJKHTQl30LToZVJvYb4RRJUNxE6zJ6AzI5tylhWflL17lxTa7bPV1yHCrYL5a/aE4yTs9Y6JZ
pXgRQ43ikgP9DS95dewt/9EMDNgaAP8vaSqFq65rCii6h/jz5Zf0dkjWaWIaRF+MroKE/sK+Sh4g
C3OB18dAnTMDC6rL4X/xwhltI2EDF+McjRzo9jgjQF/LRysF0Z4SVPkkziwmi7lonULpo9i8qiZc
4Twaz06F5wwvcJRnQJTwbhu5L8b4PNZ8DZYFn7us2FruZobwDzPCJ8kMty9hebHBBliZ0ZK2S0Ou
06dCXb3LbJrJrZZR3Fo6aywty4R7TmtVEuKr8Um35A0BzAmwPF6M7SrjourJQv1JHYwxB742zY6I
Re2aDENoyxj4JmEsYM4X8Z7DL5+Nss438JT8jTe1wfzx9PKdN4fJHvEs6qU3T+hwFl3xfD6sMQj3
HK28JPNDbRkSNVlAtIYAk8WPmlnWOOPQHM+J9tx2Nm6yF4awGOqHAd9z3bU6f3+9/CbP6Xy7sfV7
vVFudyL6Q3Nr+IP/73su5AtOw7aGvudYoLuAhLb1udFYXRWwl+qAkXBGcFPnMVnzcf/0LhqlzTKC
u0byBUmLRTSiTsg1AK8FZYH8YCd8fcQZmqZVwqOLcrjUroQR70UegOxFdleYqTvtaTK6/LEAsQfK
zSu06kq+6buHtghOwFtlgv332ImutBaI+MtMw1T/eTP4OBMU6tvheHaROGc2IiRZXZa8y2LjfWaI
+QxaeCv29eDQOUObvQVl/r/b8NnDWPvT9fUX3700fxjKNRy22JduNpnA3nh11K3JHfAqI6AMhgP9
b9OiGlz5q7RJFTfPrOi55pgVPdvcsyIMxHgn62gQ8/IluwJFijgdIujlEXPv4NYvwghB3pnRVpal
hUKnEA3FKxwm9oB8o+7zlqVd3rIGNNO8kf2SVHAbT3WzeSO8BZCWJEYfMV1EU/reuKSD2gtFYmGX
sHQ8ygF++BbfvAuY1skGcfeqfGzxwu8apleVUDfDX2TckoQdDqNEQ0Fkh4IqByl1JoSUIsKIC7NM
aZBDJ5ZrDsxEKvzLlNkU3UbZDRfIcIZYAnCiUTCWkWpo+ARoXBoBgVbX54cXipSOaxeJqzKnhcJN
QInrSU788DUMdZrsdEtCaYeUQptxGOYK8UEuPs0coDjHv9lgOnmjUGUS8M/EJasLcuqnENmu1Ro+
pAe8IbTaoc9N1IvlarTocFQOfJqF4qHdrLNS3ENezTtfFy1m+f4xunazTh5o6sVD9DdWXoro0Xsg
QyZ0zHplOUQcJsyJlRVIWsxD74wIHaseYMEbz4b0wpqG2E0CVcWzccU4W/LssFO9cxqshfGJNjY2
6Cj0tpOD4fMYThR4CbVoLJiTBOtgZwdTuMCAnsdAcNDAHZJWulZGr35OtYA71B0ePUM7oXrQjcdu
vndNLWgl7IVboJ2G7Vrgbjy2qo2NdxwbGHjencey3VlX/GU+Ifz3kqS2YRq/d/z3tUcfrK3Z+O/r
H9zhv9/Kcw37D7VGXCuPOtuQEBa8hua+FRfHefYV++eG7ERuAB6d8R0VmbUIsxPfTrk5hHSK9Suh
zUcyXo5mr1gLWB7L1l03mrteEo/hfsWQ7mabtF9NgOq6oPOcW7hrqqKRFrR0RBKtKNgWIdoCs5/l
hNxFq3Aw4NBfFAG16nZ+uzRZsm3PpRFqIXDKlLcu6vS0+y1TieEPwXagIWjpAaIcvgKx8Cnk5zkJ
fAiTEPthnUIYoNI90EEm061xa7DItlFzPaf7PMte40KPoks59IMHtLOMhZGRCA/kuHyeBCGvfoFj
o4yitSsRsXCF62AYXMwA41JOlH7zczmQ73WWAttF262oauAbOcal3KX9SX8S5AfODfRjybDK1iq/
r5e1oRFoCRqBJVj2ezWgCSK5M+zMFh2IRMC3Qhv98Aqg/I2+Hl6vit8lF+hUIZuVVsmkDl2MEiw9
FQMY9aPOEsUKZO+N8XRaFLbXCJ00XjhMg9zXUngeHiw21wYQdoyVNmQBYVjSAFlvbdTRjHUkhsyG
MmLRDDmyTeoiFum9NbP6Yybqg/gby43aW7UuuNbXz8qLReRJWVCaDaYIkghF9OIZ+7ciE4lv4l7y
dkjekHVxGqn3AVwWMwCpHGcxnGZJ6N9yDJOe7RdxVsZsUetbQ/3lQTNVuhsVw09bN6jJUb8p/EEg
TIOBiW/lObBcQWw3X1EEXiX6M+NgusoffIAYbpGeVAuDpq99T03qo0XnWKDTl8UWu1D2Hw+eqxDx
mAu+EXaQsrir1RjMOWDhmhqnrWM+2HXtInIrGEPKRMdtqD3ua7/7O7JcwNzNkEBp8eQJlAG9NafM
6huvPS0HIM9EMnY55uXtigII504fQxPqz9lBWtteJ+RVRO68+eTye3QP5ZetKayvGTLN0YPI4L/h
1xsQhwlJaGRDpmrtPE8KBrq1q+f+raiyTZv1Un2Anc/jAu8kGG0hzE7CpRlZu0AUzQP7HuhLo8Nz
dDzIt0hqoFSEY5oDtc+jWhVmANLQ1pPGgK3b1IZbZ3LMCJJmKVYH/DCHDjnDPqLs+TGBwlljfjM9
ZuazTV2W8IaF70QfRMSCJCaWojUAjQiIPtFQx5UbkpjXXiQMmfIKLB7dwoxDjniGwUI88Y2UF+XE
k85gr0bR5p42hqE8jC/pMwRYzMI0w0ecoQ3n8PCprRKfoKza0Hjh/egr0EBW0dpLzISZoRbHfS74
fwPyn5nSlAS/lTGkfwnlz5DDWRV27Qy7Rmf3XMAbAV5jjUYYJE/CBvm45748oiVWEJuPNvBDPGkd
3JBXHup6pxJD7hHj6GpD9lLmbMugxXsMsixm4FtCojU1IfeuqQpxIbp5Z/0CdBO4XRuh+V3Y+lqn
QQodW8rS4f1GAKXDjhchEjnFeDxEtRfBkpTSt0wncSq9Yo315iBHXGfKxFqCsGTJ19FXKMDupxPD
hd4DFu3g5gNJaF0EpHXyYzByHDlWvAhO7ktpWETBP0dsaDaiIp9loy6VBOsp+rtI/J1Gy9GjlcVe
tNZkahgWdM0tLwVenajUi7mfC46rWN4haasgAdczk6GziglphSPp9nQBwQt30Cz4uobB8g77O3ZR
/N0kfjuA01kjibaEGBRTdHWMJSsaLQ8Vpxg/q9k6G+iTYQN9UwNifAsLvygEjMUE9CI0iKR9OVGe
DegKME5O8TK7LbsNpV4QCDRS4+gsPsZjqIQzDK08GVYAvz1nHD0SUwZNCHQ47rEzDJZsYQTZoPt6
fez8aPRteXy9JOMU60VuLX7mv8moNwhnrLeJj/4otljo2k334vKPk6TgrrThKYm66mQjIe8sHiaZ
mAVll+rIWGLy6lGhnu0+393fOdp9sbm3u7e/82Jrd3MPfn3+xd7+7ie7W5vbBBf14LFfO3aeljGp
+Wt7YEFgMARPtrAulGsxyoTjIap8sOnjyx9BMkp60QSlMEekJXFMrMS4z+zEZFFaZWz1uwUzM+fk
TYIgahjvpRRbCEmfLEnv1yLbEVy1wvQjyFZ05I7oEFoniBgFworGwJMMMYhJp9lGeF5pliHjeMip
MRG75jxYhO9m0FnE2vSm9OquI0t5LTrTqMA2AaFFtka9tTe6cKr0G7cJa+PrgJ3UB3jIwtBpjQ64
6NUaKdetjacbHAipBUHwW6ZqjSNqjJvkHtpkqe3hneI54cGvPAdXmAuVxavcUR079OiT5J8qjI1v
iDYahggFmJyoChCJKkVkIUJcgCGLtbMYqZkxv4/l2e+U5yOzLtFi9tfcUNuVk3hZ01kyQtTC01kq
kfAvv89G2OQMb6nHDNTf1QvNDwk//3zPOdeBeeYDYjsD8X/cc7GNTtY9E8V5LYvplhRdLHaPdkOP
Vc4iAZqqnVWNR04rRW/NMfNnRj5ruwBiGtmAepWR8985qG4oxCl/Qs8qDDvQad2rvWLoMBe/0VFc
1adjm6E2Xes94btJjYrL76cg8wEvhqRGC0bJ1Fxjfse6aK9lS/iUe+F1DMXlP+916/UvTN/PFenV
7ul8C968l3MuVOs1zG4+U3Czlpf1Gw6cA+eNPWhG0e7FnMjR7k4OUmvXcV4LhKYri6YLmtDljHY3
U3OpzrrsU7nV+4IKoxcao8sfSzxdSstmjVk6eOBeQ1ogLCtlRTXiXGqnn3mXoatgjDIe22pNWdlN
iDZarFYm29DlVfw1nDokQPyGHG4IUdCJO6PBn/Ps7axnOIosK7QnV7EPg1PGnjE6jsFDJwEDhfvA
Ew7R1+kNc0GzlpAgJxpSIq/jabQSfchVll2rOuyGytqLVhajZSP3Iqyt1ZUVVGhGg2jFWbizq1Yq
cs5fp6CTV6hUZp2/VkmU569V0fN5aj30EaeQ1liv1a80lutRpepoKwp/as0yNMzv5rP/Dtj/K1SC
927/v7q2su7a/z9aubP/v43nGvb/DpKj9u0LYNmva7f/WVwKu33bH0AY3/uW6XWN75XYL/yfoThE
OUBRnFnfk+QNYiNeDY/QeJVO3xJ/04WBfn7UGMJrPl/XNM7XS2IShiqK/8aL0/ns9PVCk1FaqSLp
V08IKVcrEeMvFvmFKlS+ULTMN2VBY32tcDhxdVgV3TRA3KfiJBPoFAuJhQzfmK57SDugyqol6u4E
atRc+2gQaPyHlDdmvzj2y+K3DfWI6X0cGJ/WVq9C+hptB3G+bLZFR/4Mh+k0ETnD6Th0gpKzXFdI
J48B1RAu2kRZqGkCXhnZ4h7F7qRrzbX1FdEoggrr+WOXejF0VHlY0ur6ihvyFCjY13lhC6ow4YO/
Z9WvfAeL8yTFCE9eoBl8uCmsWfZ94bW/ESElrkUWQsJvyW7hiDsd4SNu5agLtqNFgdUkVhp5X2pj
WPAMGAwmGCtHi6XKUxDumSpx6FWQWAOjFhc3S1CEwjGSu6/jcnBDENwofHDRILYux3Bo7KrhsC6x
WNhGFvGyLqNc6kZO+bYuq71uNV8ntl4+KfLJJ4a7kx7B1s6u+UaNlifLv+3UDr8H9o1SyMGFSU5P
MzL+0Z2ra+w1A3BQnmPWq61mzj5VDiu8s/F0rWeEON55m1Z06abwo/Y0f7OY6elPi3gCsgPaipFn
mrqUpqwOepSpwpsvfLXWdf2ifBehoeWtebg/aJTZ6fHucIhlZjlAXbr8yexRn5tp+kNf97S+Jbzk
fJpkwA3GHFeVrSj+AvoNR+CTJdQxnifstgO+HxcpxVqaYCo8JD39o4aXeEtA9TDM1uGsKNLLn0hh
QtamKXBhwKxBy/swyMbI1ChNvVagGrKjtnyDkYtC5za5NHrZA4+BkKPcAYaroSHm3hrn8UiwPLLD
wVjqWmmPg23g1zOulVHbAfolMhi94bBHC1sfPEcVetNsRzAseg3X4TYzgJt3M+yItPJqZES8LHj9
6Xelk++Kp96tn3jeiuVZp74DedPYNGRFGKuGKtiud6KN0lVWJ+1j4Vl5+YMOuieg+KQqOdKPW4HV
ajOBnjpNcFeXB/RmoUVspKc3Wlq6pldjnYLkDjRMLks09VXFSR5wo55HlHKS2RqH97gCE3E1lkH4
duXRNVmGgPxX5aenFBWympXd+7BoYC1s72xu7e9+ubm/4xJljmit1p5Q/zvrjZDqqFxIf8944Uno
LvgmCz9zINWg7TBjDR0bWjrIsfO9fkS4YqH1QS6GxN1XTi+hTUllLiSTTKnv11pq9t1FcOFp646N
IL54FmsKDYJaw0j0GfBLqPu4/EGkx7W28dS70u5Agf5Mn1D8J24PcAPa/0b9/6M1Q/+/hvr/Byt3
+D+38lxD/7+FLOGpq/sPx366iVsBDc3n47goLs5HZ19t55NX25/wJF+9Gp00F9MG7odfQMgAU86e
+PVcNSj7nmveNGgF3dhFg1bmzdwzaAXe+DWDZim1oYHQz3/H4Jp1ejQIzrwpDUJXj8O62KxDEBcM
bRQXnlluKv7Xc+/QUpivF9JXA1cDAhfg8o8FmnyOEmHFy+SfMokjHm1Eh0ymImrlcXYr0EoKd4XK
rk9y/DlFPkMKRjLLI7AjyGwRl8nlf2gXby3kwpAOub2UaCgFayVGo+1EvLUVdaHFNsCwMSzOjAS9
7jx2/ZKN8i5iFaOmyI0oNXDYpcdx1GXg+77YMk0hMSygc0fZYu8A2RMOVS5vPUJ3UX7lvXS8BYrI
cTMQa7zgiGhaHDw0rs5myXmuQNLai02+I86Sk6I1TSra03Tdupm9TxAXspFfDK9VH0v0MXlyqNNf
vQtobGt0trK0x5qiPEDDbd2zmpF2mudwHzwH43W60jikQSV0qyaGI1ppO6X5tGlz0rQ4ZRpOmIAW
uK8NNN+gdOjscPj0UR45ihKtzpCW1z5eosYHkfWQR61S5mxhkm866VgsRL1+g1zUUlIWdKuJeEYx
mhIbBRHKnxZ1REb5Ym7vpVLBxccydslF9OKLF1ubUZkYZRXJyTh5Q+lUST2gEG9iHOgxB7GnwGHM
dD7NhkC3STNVGnG/tFkTfi710XrCx7q1Ah43z5Uzc9ICWVsqZT5MybvSx5u6XIMVSqyRaXDii9Xz
DCw52yKHc3QRzxcFnyUZLwd5873onQ9UNpi+dwwAA52LKOavzFuiJ3FU5SBm5rXcBvbmU8YMYBnH
KQ9q//6OR6FGZFrEe7oaUZJYTftqOl33m09K/COkoK09KmEkvhSeuuSFp53YLDAFR+8U67i0Zlht
GpYQR4g5hXTnD7rojqaZH5+G8RWoSEwZGys8FH3M2Z086QBUQr3rLOhSYASM+WD35fWxyyzKgivf
fKOptN9pn+RLmCY8i3wtvXAozfWWb4NOvGEx72gta9KIz7OWiwRVSLi6Ip8ln9YNkRLZ4mBx09EJ
3cS4vAyd1ad4gUl/DAZjOFRKGjjlhSdTB7QW8XhspRsh2sqr7U+gPODivvQ3HVIZ6J7UAh0gV9ik
G4fpbgYcSkpxYLIZ+kzKuOjTy+9P0YGWOflP00R7aTZu6SksCtuM4/4IWkINZ0lOk4ppBM1UIC2d
xyyCJqWnhFv00pcQaf1pcoTBg7trK73o79Hwf2F3gg4Y5PM7iBaifmTCFTEmjcVIjFZXeECSLvyB
/1tsquYDWc8r1vvo21ebn+4cvfji+TscE/Zr6+UXL/bfLVy5mofrshoKBc068pVhZO3AXfMLbki4
EC21yfDZIB3AMqhvpUMA2AzCeZDEyreGdHT+DZIzRuaVXHjdsOesBzVUY4WQmdO2iSSiRBIkGkuM
eBXnlz+UXGPB9DcU3wf4Zhul09x3mp8rcq1ACo/E9zr/awbepdjgXhTILreoDukl83lQvfTyeZF1
xYvXWukiV0PhCnksUHQdEhmDs2SaUzhTjMiBnhmg+WLZvWey5s3MYdkUCgbU9ImGUAazvrtNDFcA
uFN5rqlh/o1/IMiVJxs6jmjsqwGTbSYwzuUENnJa7lDAaH94maBjpM/6T7OhY5xGkjEZDnVP2tol
Az04XRIOa+PhK3rRw5WHASiGAG8V0gNgHGqciu3knID4EM0+GpMrP54LqlkgkzFqk0ef7718oTE2
7SAyrzpWknej+Jc26fAjWvpGLAhqif9cybDjFgw6nMFrYc5xFWsOtXDu7A/e39NfRnZmVgB7u/y+
6sBb/g/W1wP3/+xvw/9vdfWDtUd/Fa2/rwbpz1/4/b8+/yiovI9VMP/8P1h59OBu/m/jceefpL0b
XQXzz//D1fW7+b+VJzT/pOq+oVUw//yvP1h9eDf/t/HUzz+p2vogX42S92b/t7byaH3V9v+Hl3f2
f7fxfMRN2LiWb4wgMKgpQiH2v/+3jzBmMaKTcYEIBPPPQSpLxvkUL3f1BMOyFILvk3Ga/Q6NnTYW
yupinJRnSVItRGdFcrKxcFZV03KwvDwcZW8QxDyfjU7GcZH0h/lkOX4Tv10ep8flcgUyBEj0S8cw
7IjDM11+2F/vry1DNcvyXR9+LTydr9I+XiyRFqbsZ0m1zIW/9DxZftBf6T+gGtTLvqzsoajto7LK
p0bXQWROEHA6gX1SyFE4W336TAitXNQpnyzD23ARMu8oPSdtfrmxADLmCAXN0VI+q6CTCfuBuHNx
cSG678u1xBqkJ2ENe2CkoqldePoFb6KCPqLmPrByfzSMM6ESJiIhLcRcXYTToCrPx6XdHpk6FmmP
qyyC/5ZgePJshL2McNaWmAi8sUD+geIdiOBJtbHw15qD4cJTCXn2JPVXJh5RYxodp0vT8axcGqbF
cJws4T3gwtMnyyn8Fz/99tulpeh4VlXSkC5aWnrnCQr8ZBn6bA8ZbDAYNW2irETOOB3nowtn2mjR
RuloY0GspgWRiX2i/19CWO5pMuK/jglrXv48y8+TQiSc+GbiSYWrxih4Cabgd8FZq4rAF17a093t
J8vwT32iF3SP3CLhDmrYm9PJDgB9W0LcHNwH0Sa3mo1qCoBPvh5hDhgY74jhdAVK+wjB98eEtUu7
hWD36W6trvl1Q8pSjGBJCtVMOorevYP2+RoXzkX38lfIx+7T22b0zkN9LnxcKsMsg5tz4qPv1p3t
3f3AXvU2OeZHxl8v2NTo67jI0uyUUaZJNCmWVjkNwj1pTcdCu+rwaSZt2HdG2CIi1hsLO/AmxhXd
RN/sh/fpBJbhSbyEBSsi12JaBCW7ygxKy+uWM4Gq3YiFgaPhNe6DWZG6ThQV+dJTEmcgmiTVWQ5T
8+rl3v4c80Hsw8bCKC2n4/hiAEwRHLqP26xa1feyOJkjOWtot/Nqc3/rs85i9OQerN9ncRGfC0cA
1Lxz+49pXAzTeMyDGlEWWN9ztO4J3xnVxRS6Wc6OoXhjsaulzLXDH0YdXPSjODsF/iYasJ8SWhHG
eo7q8fn23j23iiepWp1LZT6GfQR/8OP47YTOIFyq1AB/4vL3cDAmS8OzZCgT37vXcuvT2CyzwWnZ
nyfLuEhvbuM00NTA0cQqwDuha54pGBipnMaw3R4ueAn3CwxHcSGDUOusYv86bc9G/Jj0DRDk9Byw
8BZ5E5vVStHIYJS04VD9ae1zRvBqnDHnv1wGnok/zvs3SioihOBKazCa56MTxD67E/5rwdYBJXMu
qoQ8MvChmELJ+Xj8z95vCGD9T+moOhvwcG3R8jLGJEmi8xTv1fB+iYw001E8MrOO8gnstI9Piiqd
cs+Gy/+IRyy8BQksCXdcPs4r0xkJH7aTykF04LmmxIeJngOEYZpefFZNxus+hGV8cBEam57x68Nx
Oj3OkWHWdny0lU/TuAgVRQW8AN6HUTFxmusEzc34jvV+NAJGmmeABRsdJyijUjQPhu7o2YCNfS/P
r9J1EE8SJOBLkF/0e+/LK3SaczXeTs/fG8SHGV+1P0tJXCCZX6JSeK/wAteB3GrRLyk73lDPpqOT
a80T5Oc9erX9yRX6ww/fG+pMkWbVnD2hPHgIUCfQDiqdpFfZZihN3lA/4Lw6T70b1nxlY1kvjGE0
Z/FpsjDw1bEwSobpBNjtQbSw4GnWAh22RLYxCT8VgWdH89wUSXWK+iXdxctbDN6KYwEsYiValB/t
7W++3j+K4uho58X2EdLWo/2X+5vPjqAwFio0WBJZhZjFrUBBK1jISnN2NHxBTQGW0GVx+uiePjp6
vvnPqnqGwpyUi8GCXkH1n6Rvg4MHvO6shOaVmKLnTTJOstPq7HmSzVR/CmjIzosvGgYCrRaBnL2G
3V+wGrbQRgMGo9/ve3NMixypPmQKtrgEwjQ8w88fz8phXAy8qb5Jilyr1zWoEWhhyLkD75GOkKvy
d2Man5JPsn99UhKy8sSKXlEEitxXDhuSmKW7/N9jjGESTJfBtsJ0e+npDOFtkmDKKYYXg0nE1JtI
FNK88AhYvh29gBgUNb0idZXYWC/hRyZssSjAGQvcGWyYzP0aI7aUVMhuRsFbyJoLv5PFqVFaI/nQ
I8oI85QnywY/53J394Gjk4J7Z7GP3CA6YvWPyz5J+XogSmhuVjn2SefQdy6sbUB5lKiPTsRVMton
5YBtcIU5kI+l4OUsKym8ux20pPMkZlBrWDo6VLvgelRggSkCIrjw0R6QqR6IgmjYPI6HkGxA1nWs
OR4swD5q+7uehQD1DfD/PLPMJGWg/J/u7PuOnmEMnOAgGJiYc3gDNfCjUCR4fNB/I6mi6ox5BHZK
ro6Ivnj9LBpdZPEkHcbjcUDswqHDbJssT3AEuXd73RhiKyl1WL7qu4G15aDhHPcJV4otSUS4ghUZ
V1XR7bAu4WKUbfVHFecj8iqfznAF0rCQk18ZoS6xFwnfngjILXO7admeJUIUXeyfx+Ou7GofX7bu
0pKo3ClG+CC1L4o5Hdnl0NvakXlmesBlsMbInXeKcSHhEKLowuN0MsWohRmL2ArNLqpZEZNBHDWl
bSuVgxI1tONs7+asR9zhL6YVoJcT7OOnfD9gR+MU7aZREALxE02mM1T3wlkZpVV4S1DaLZl9w2yf
+TXYJzNZP2EGsbWTk+dTaDnsv9OzCDZtFJ/DfNK9idZ6drVD0uRx/taWaMVDi4Jy9aH3O0BzupKg
4OsgQeFt2cLy0bBXbCHgH4GxAgJMbQnnxfFLS8qeIIVXq5O1pswnSRd/o+c1mluKvzFmOEY7qy8e
H6QjqI0bskpI2dYJDqzoEBs37M5n+8+f0ZqgVcIHEtqBmnPghTHmIyYdp8A2hBa76OoZCFzQy3+p
0SppN2dIv5giIFJ/LjH1bZOC8kmK4KBcJSpaveCWvETpFojgbSzQoB8cttAtw86aQYb73xZsMt4t
0EUe/jrSX97/Vk7vu6Ymw+JFRYzTRHq/gGPgVtBCVclTYw8bFKdPlqmqWqWfexsqnn8JbG18rO0d
T6cg73VxOYQIwjvfBx8DSsbXGgPw9qzoRUwJ3WMfg7sX9Tw4NJSqu8CswE+SaniGi5v2MW5IkA54
Qb4mWZylYf/t4S2lSvE92X+E7H+0eCXXtgKb3/7r0cNHq3f2X7fxtJl/I7blFazB6u2/VlcerHxg
2X/Bmzv7r1t55rX/mg2Zd2k8q87Q1UM4PoIsinwGavxKzQd10SomZCq19lQLQ8ZvNhN+KZGgGdKa
unRpaTdV5F8HjaPy8dJktLS65tjYXMnuKpQ7YH8lk/vtsD5LEUorjcce46tgTbWGVfiQSYQ0brAF
USZ12gYPsqsqgKT/2hXtprheuN7SImxlEOIUgu/bmEzJxMp0KkX0Zk7L5jefKie6HVXdeD8JWQuZ
iZouZXlR3DyKhES1TxqsoPT8W+i2N0d68gueI/1nOYiwu1k6TPPoIvokzebIS9ED03lah7WVnEB8
g6rgOfJKshLtoyJ7jpzj9PczzDhPSwkxnLwW49N58glDtRZZai7W+eeGlVhrvyYesmOL0aVSi+TI
rNnU7xaWPe2WPEvJrM5U8RKgLsJoFMzJUvuoQIFaGac113NECtf8+sWhfu/6pSCjUvm7Dif4/OW3
S0mpJdW32xSzvYsKAzIU4QrWiN/PkzyqGdSSNoESsutMnu6tmQbtifAwadmV642q1gNsnfFtGCOr
QsQGh53+6lx/IuuqnEry9B7n8yMWk9vTppa2ebLa4MKQxPLnXBrUV9gbaUtjvF/+gNvDzCJS4OFy
NJmNqzhqa+dKrfqFDo1cO/OuxlqLUSXD9s/i43SMBrRmYGJpOTpftfgYpqZzGkPiwy1G589Ya8zJ
lIIMmzqSVhhsH4KgIUZh/lrx0fnQuYwmZdPbGk+KR6zXG12xDeyTqJezP7V8Vg0b5bdWZF9cAcfx
D9F+WxaIHhtE7zfXDtFC5bCNEXVByTVIxKfBKBEfx/hQWCoG0tcZMvq0o7KjP7ca5e65e+6eu+fu
uXvunrvn7rl77p675+65e+6eu+fuuXvunrvnF/f8f6jBLacAgAIA