# =============================================================================
# Build Script Aksara Kampus untuk Hosting InfinityFree (PowerShell)
# Menyusun folder deploy/htdocs siap unggah via FTP (FileZilla)
# =============================================================================

$ErrorActionPreference = "Stop"

$rootDir = Split-Path -Parent $PSScriptRoot
$backendDir = Join-Path $rootDir "backend"
$frontendDir = Join-Path $rootDir "frontend"
$deployDir = Join-Path $rootDir "deploy"
$htdocsDir = Join-Path $deployDir "htdocs"
$backendTargetDir = Join-Path $htdocsDir "backend-core"

Write-Host "======================================================" -ForegroundColor Cyan
Write-Host "  Mulai Proses Build InfinityFree - Aksara Kampus     " -ForegroundColor Cyan
Write-Host "======================================================" -ForegroundColor Cyan

# 1. Bersihkan dan siapkan direktori deploy/htdocs
Write-Host "`n[1/5] Menyiapkan direktori deploy/htdocs..." -ForegroundColor Yellow
if (Test-Path $htdocsDir) {
    Remove-Item -Path $htdocsDir -Recurse -Force
}
New-Item -ItemType Directory -Path $backendTargetDir -Force | Out-Null

# 2. Build Frontend Nuxt (Static SPA Generation)
Write-Host "`n[2/5] Menjalankan 'npx nuxt generate' di frontend (NUXT_PUBLIC_API_BASE=/api)..." -ForegroundColor Yellow
Set-Location $frontendDir

$env:NUXT_PUBLIC_API_BASE = "/api"
$env:NODE_ENV = "production"

npx nuxt generate

if ($LASTEXITCODE -ne 0) {
    Write-Error "Build frontend gagal."
}

# Salin hasil statis Nuxt ke root htdocs
$distDir = Join-Path $frontendDir ".output\public"
if (-not (Test-Path $distDir)) {
    $distDir = Join-Path $frontendDir "dist"
}

Write-Host "Menyalin aset statis Nuxt dari $distDir ke $htdocsDir..."
Copy-Item -Path "$distDir\*" -Destination $htdocsDir -Recurse -Force

# 3. Salin Backend Laravel ke deploy/htdocs/backend-core
Write-Host "`n[3/5] Menyalin backend Laravel ke deploy/htdocs/backend-core..." -ForegroundColor Yellow

$excludeItems = @(".git", ".github", "node_modules", "tests", ".phpunit.cache", "database\database.sqlite")

Get-ChildItem -Path $backendDir | ForEach-Object {
    if ($excludeItems -notcontains $_.Name) {
        Copy-Item -Path $_.FullName -Destination $backendTargetDir -Recurse -Force
    }
}

# Pastikan folder storage dan bootstrap/cache ada
New-Item -ItemType Directory -Path (Join-Path $backendTargetDir "storage\app\public") -Force | Out-Null
New-Item -ItemType Directory -Path (Join-Path $backendTargetDir "storage\framework\cache") -Force | Out-Null
New-Item -ItemType Directory -Path (Join-Path $backendTargetDir "storage\framework\sessions") -Force | Out-Null
New-Item -ItemType Directory -Path (Join-Path $backendTargetDir "storage\framework\views") -Force | Out-Null
New-Item -ItemType Directory -Path (Join-Path $backendTargetDir "storage\logs") -Force | Out-Null
New-Item -ItemType Directory -Path (Join-Path $backendTargetDir "bootstrap\cache") -Force | Out-Null

# 4. Install dependensi Composer produksi di backend-core
Write-Host "`n[4/5] Menjalankan 'composer install --no-dev --optimize-autoloader' di salinan backend..." -ForegroundColor Yellow
Set-Location $backendTargetDir

composer install --no-dev --optimize-autoloader --no-interaction

# Siapkan file .env produksi default di backend-core
$prodEnvContent = @"
APP_NAME="Aksara Kampus"
APP_ENV=production
APP_KEY=base64:31r5sO3RsM/6XaHDjV/WRAz4N7u/hciHk0uKcwJ2xHc=
APP_DEBUG=false
APP_URL=http://localhost

APP_LOCALE=id
APP_FALLBACK_LOCALE=id
APP_FAKER_LOCALE=id_ID

KAMPUS_EMAIL_DOMAINS="umpku.ac.id,student.umpku.ac.id,itspku.ac.id,students.itspku.ac.id"
CORS_ALLOWED_ORIGINS="http://localhost:3000,http://localhost:3001"

DB_CONNECTION=mysql
DB_HOST=sqlxxx.infinityfree.com
DB_PORT=3306
DB_DATABASE=if0_xxxxxxxx_aksara
DB_USERNAME=if0_xxxxxxxx
DB_PASSWORD=ganti_dengan_password_db_panel

QUEUE_CONNECTION=sync
SESSION_DRIVER=file
CACHE_STORE=file

MAIL_MAILER=log
"@

Set-Content -Path (Join-Path $backendTargetDir ".env") -Value $prodEnvContent -Encoding UTF8

# 5. Konfigurasi .htaccess Satu Domain & Proteksi Folder
Write-Host "`n[5/5] Mengonfigurasi .htaccess untuk satu domain di InfinityFree..." -ForegroundColor Yellow

# Root .htaccess
$rootHtaccess = @"
<IfModule mod_rewrite.c>
  RewriteEngine On
  RewriteBase /

  # 1. Blokir akses langsung ke folder internal backend-core
  RewriteRule ^backend-core/.*$ - [F,L]

  # 2. Arahkan seluruh rute /api/* ke public/index.php Laravel
  RewriteCond %{REQUEST_URI} ^/api(/.*)?$
  RewriteRule ^api(/.*)?$ backend-core/public/index.php [L,QSA]

  # 3. Nuxt SPA Fallback untuk semua rute frontend
  RewriteCond %{REQUEST_FILENAME} !-f
  RewriteCond %{REQUEST_FILENAME} !-d
  RewriteRule ^.*$ index.html [L]
</IfModule>

# Proteksi file sensitif
<FilesMatch "(^\.env|\.git|\.lock|composer\.json)">
  Order allow,deny
  Deny from all
</FilesMatch>
"@

Set-Content -Path (Join-Path $htdocsDir ".htaccess") -Value $rootHtaccess -Encoding UTF8

# Backend-core .htaccess (Deny all direct HTTP access)
$backendHtaccess = @"
Order deny,allow
Deny from all
"@
Set-Content -Path (Join-Path $backendTargetDir ".htaccess") -Value $backendHtaccess -Encoding UTF8

# Public index.php patch bila diperlukan
$publicIndex = Join-Path $backendTargetDir "public\index.php"
if (Test-Path $publicIndex) {
    $indexContent = Get-Content -Path $publicIndex -Raw
    # Pastikan path tetap relative dan valid
    Set-Content -Path $publicIndex -Value $indexContent -Encoding UTF8
}

Set-Location $rootDir

Write-Host "`n======================================================" -ForegroundColor Green
Write-Host "  Build Selesai! Paket Deploy Siap di:               " -ForegroundColor Green
Write-Host "  -> deploy/htdocs                                    " -ForegroundColor Green
Write-Host "  Ikuti panduan unggah di deploy/PANDUAN.md           " -ForegroundColor Green
Write-Host "======================================================" -ForegroundColor Green

