#!/bin/bash
# Использование: GITHUB_TOKEN=ghp_XXXX ./push-github.sh
# Токен: https://github.com/settings/tokens (Personal Access Token с правом "repo")
set -e
cd "$(dirname "$0")"
if [ -z "$GITHUB_TOKEN" ]; then
  echo "Не задан GITHUB_TOKEN. Пример: GITHUB_TOKEN=ghp_... ./push-github.sh"; exit 1
fi
git remote set-url origin "https://x-access-token:${GITHUB_TOKEN}@github.com/ItzCatmi/ICOS.git" 2>/dev/null || \
git remote add origin "https://x-access-token:${GITHUB_TOKEN}@github.com/ItzCatmi/ICOS.git"
git push origin HEAD:main
# убираем токен из конфига
git remote set-url origin "https://github.com/ItzCatmi/ICOS.git"
echo "OK: изменения запушены в main на github.com/ItzCatmi/ICOS"
