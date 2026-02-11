# HeirLuxury - Laravel Forge Deployment Guide

**Complete step-by-step guide to deploy HeirLuxury on Laravel Forge**

---

## 📋 Prerequisites Checklist

Before starting, ensure you have:

- [ ] Domain purchased on Cloudflare
- [ ] Laravel Forge account ($12/month)
- [ ] VPS provider account (Hetzner recommended - €9.50/month)
- [ ] Google Cloud Platform project with Storage bucket
- [ ] GCP Service Account JSON key file
- [ ] Git repository (GitHub/GitLab/Bitbucket)

---

## 💰 Cost Breakdown

| Service | Cost | Purpose |
|---------|------|---------|
| Laravel Forge | $12/month | Server management |
| Hetzner CPX31 | €9.50/month | 4GB RAM VPS |
| GCS Storage | ~$2/month | 70GB product images |
| Cloudflare | Free | DNS + CDN |
| Domain | ~$12/year | Your domain |
| **TOTAL** | **~$28/month** or **~$336/year** |

---

## 🚀 Step 1: Prepare Your Laravel Application

### 1.1 Initialize Git Repository (if not done)

```bash
cd C:\Users\simon\Dev\Laravel\HeirLuxury
git init
git add .
git commit -m "Initial commit - HeirLuxury v1.0"
```

### 1.2 Create GitHub Repository

1. Go to https://github.com/new
2. Create repository named `heirluxury`
3. Push your code:

```bash
git remote add origin https://github.com/YOUR_USERNAME/heirluxury.git
git branch -M main
git push -u origin main
```

### 1.3 Add Production Files to .gitignore

The following files should NOT be committed:

```
.env
.env.production
database/database.sqlite
storage/app/public/imports/
node_modules/
vendor/
public/hot
public/build/
public/storage
storage/*.key
storage/logs/
```

Make sure `.gitignore` includes these.

---

## 🔑 Step 2: Set Up Google Cloud Storage

### 2.1 Create Service Account

1. Go to https://console.cloud.google.com/iam-admin/serviceaccounts
2. Select your project: `YOUR_GCP_PROJECT_ID`
3. Click "Create Service Account"
   - Name: `heirluxury-production`
   - Description: `Service account for HeirLuxury production storage`
4. Grant role: **Storage Admin**
5. Click "Done"

### 2.2 Generate JSON Key File

1. Click on the service account you just created
2. Go to "Keys" tab
3. Click "Add Key" → "Create new key"
4. Choose "JSON" format
5. Download the file
6. Rename it to: `gcs-service-account.json`
7. **Keep this file safe** - you'll upload it to your server later

### 2.3 Make Images Public

```bash
gsutil iam ch allUsers:objectViewer gs://YOUR_GCP_PROJECT_ID-storage
```

This allows images to be publicly accessible via CDN.

---

## 🏗️ Step 3: Sign Up for Laravel Forge

### 3.1 Create Forge Account

1. Go to https://forge.laravel.com
2. Sign up with your email
3. Choose plan: **Hobby Plan ($12/month)**
4. Add payment method

### 3.2 Connect Source Control

1. In Forge dashboard, go to "Account" → "Source Control"
2. Connect your GitHub account
3. Authorize Forge to access your repositories

---

## 🖥️ Step 4: Create Server on Hetzner

### 4.1 Sign Up for Hetzner

1. Go to https://www.hetzner.com/cloud
2. Create account
3. Verify email
4. Add payment method

### 4.2 Connect Hetzner to Forge

1. In Hetzner Console, go to "Security" → "API Tokens"
2. Click "Generate API Token"
   - Description: `Laravel Forge`
   - Permissions: **Read & Write**
3. Copy the token
4. In Forge, go to "Servers" → "Create Server"
5. Choose "Hetzner Cloud"
6. Paste API token
7. Click "Connect"

### 4.3 Provision Server

In Forge, configure your server:

**Server Details:**
- **Name**: `heirluxury-production`
- **Region**: Choose closest to your users (e.g., `Nuremberg` for EU, `Ashburn` for US)
- **Size**: **CPX31** (4GB RAM, 3 vCPUs, 80GB SSD) - €9.50/month
- **PHP Version**: **8.2**
- **Database**: **MySQL 8.0**

**Optional Add-ons:**
- ✅ Enable Weekly Backups (recommended)
- ✅ Install Redis (for better caching)
- ❌ Skip Node.js (we'll use Forge's deployment)

Click "Create Server" - this takes 5-10 minutes.

---

## 🌐 Step 5: Configure Cloudflare DNS

### 5.1 Get Server IP Address

After your server is provisioned in Forge, you'll see the IP address. Copy it.

### 5.2 Set Up DNS Records

1. Log into Cloudflare
2. Select your domain
3. Go to "DNS" → "Records"
4. Add these records:

| Type | Name | Content | Proxy Status | TTL |
|------|------|---------|--------------|-----|
| A | @ | YOUR_SERVER_IP | Proxied | Auto |
| CNAME | www | yourdomain.com | Proxied | Auto |

**Important:** Make sure "Proxy status" is set to "Proxied" (orange cloud) for CDN benefits.

### 5.3 Configure SSL/TLS

1. In Cloudflare, go to "SSL/TLS"
2. Set encryption mode to: **Full (strict)**
3. Go to "Edge Certificates"
4. Enable:
   - ✅ Always Use HTTPS
   - ✅ Automatic HTTPS Rewrites
   - ✅ Minimum TLS Version: 1.2

---

## 📦 Step 6: Deploy Site on Forge

### 6.1 Create Site

1. In Forge, click on your server
2. Go to "Sites" → "New Site"
3. Configure:
   - **Root Domain**: `yourdomain.com`
   - **Aliases**: `www.yourdomain.com` (optional)
   - **Project Type**: **General PHP / Laravel**
   - **Web Directory**: `/public`
4. Click "Add Site"

### 6.2 Install Repository

1. Click on your site
2. Go to "Git Repository"
3. Configure:
   - **Source Control**: GitHub
   - **Repository**: `YOUR_USERNAME/heirluxury`
   - **Branch**: `main`
   - **Install Composer Dependencies**: ✅ Yes
4. Click "Install Repository"

Wait for deployment to complete (2-3 minutes).

### 6.3 Enable Quick Deploy

1. In your site settings, go to "Deployment"
2. Enable "Quick Deploy"
3. This auto-deploys when you push to `main` branch

---

## 🔐 Step 7: Configure Environment Variables

### 7.1 Update .env on Server

1. In Forge, go to your site → "Environment"
2. Replace the contents with `.env.production` file contents
3. Update these values:

```env
APP_NAME="HeirLuxury"
APP_ENV=production
APP_KEY=base64:YOUR_EXISTING_KEY_FROM_LOCAL
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=forge
DB_USERNAME=forge
DB_PASSWORD=FORGE_SETS_THIS_AUTOMATICALLY

FILESYSTEM_DISK=gcs

GOOGLE_CLOUD_PROJECT_ID=YOUR_GCP_PROJECT_ID
GOOGLE_CLOUD_STORAGE_BUCKET=YOUR_GCP_PROJECT_ID-storage
GOOGLE_CLOUD_KEY_FILE=/home/forge/yourdomain.com/storage/gcs-service-account.json

CACHE_STORE=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=    # Your Gmail address (set in Forge env, NOT here)
MAIL_PASSWORD=    # Your Gmail app password (set in Forge env, NOT here)
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS= # noreply@yourdomain.com
MAIL_FROM_NAME="${APP_NAME}"
```

4. Click "Save"

---

## 📁 Step 8: Upload GCS Service Account Key

### 8.1 SSH into Server

In Forge, click "Server" → Copy SSH command, then run in terminal:

```bash
ssh forge@YOUR_SERVER_IP
```

### 8.2 Upload JSON Key File

On your local machine:

```bash
scp C:\Path\To\gcs-service-account.json forge@YOUR_SERVER_IP:/home/forge/yourdomain.com/storage/
```

### 8.3 Set Permissions

In SSH session:

```bash
cd /home/forge/yourdomain.com
chmod 600 storage/gcs-service-account.json
```

---

## 🗄️ Step 9: Set Up Database

### 9.1 Export SQLite Data to SQL

On your local machine:

```bash
cd C:\Users\simon\Dev\Laravel\HeirLuxury
php artisan db:seed --class=DatabaseSeeder  # If you have seeders
```

Or create SQL dump manually:

```bash
sqlite3 database/database.sqlite .dump > database_export.sql
```

### 9.2 Import to MySQL

Upload the SQL file:

```bash
scp database_export.sql forge@YOUR_SERVER_IP:/tmp/
```

SSH into server and import:

```bash
mysql -u forge -p forge < /tmp/database_export.sql
```

**OR** run migrations fresh:

```bash
cd /home/forge/yourdomain.com
php artisan migrate --force
php artisan db:seed --force  # If you have seeders
```

### 9.3 Verify Data

```bash
php artisan tinker
>>> \App\Models\Product::count()
# Should return 25465
```

---

## 🏗️ Step 10: Configure Deployment Script

### 10.1 Update Deploy Script in Forge

1. Go to your site → "Deployment Script"
2. Replace with:

```bash
cd /home/forge/yourdomain.com
git pull origin main

$FORGE_COMPOSER install --no-dev --no-interaction --prefer-dist --optimize-autoloader

( flock -w 10 9 || exit 1
    echo 'Restarting FPM...'; sudo -S service $FORGE_PHP_FPM reload ) 9>/tmp/fpmlock

if [ -f artisan ]; then
    $FORGE_PHP artisan migrate --force
    $FORGE_PHP artisan cache:clear
    $FORGE_PHP artisan config:cache
    $FORGE_PHP artisan route:cache
    $FORGE_PHP artisan view:cache
    $FORGE_PHP artisan optimize
fi

# Build Vite assets
npm ci
npm run build
```

3. Click "Save"

---

## 🎨 Step 11: Build Frontend Assets

### 11.1 Install Node.js on Server (if not done)

In Forge, go to "Server" → "Network" → Install Node.js 20

### 11.2 Deploy

Click "Deploy Now" in Forge.

This will:
- Pull latest code
- Install dependencies
- Run migrations
- Build Vite assets
- Clear/cache config

---

## 🔒 Step 12: Enable SSL Certificate

### 12.1 Install Let's Encrypt Certificate

1. In Forge, go to your site → "SSL"
2. Click "LetsEncrypt"
3. Enable:
   - ✅ `yourdomain.com`
   - ✅ `www.yourdomain.com` (if you added www alias)
4. Click "Obtain Certificate"

Wait 1-2 minutes for certificate to be issued.

### 12.2 Force HTTPS

In Forge site settings → SSL → Enable "Force HTTPS"

---

## ⚡ Step 13: Optimize Performance

### 13.1 Enable OPcache

In Forge, go to "Server" → "PHP" → Enable OPcache

### 13.2 Configure Nginx Caching

1. Go to site → "Files" → "Edit Nginx Configuration"
2. Add inside `server` block:

```nginx
# Cache static assets
location ~* \.(jpg|jpeg|png|gif|ico|css|js|svg|woff|woff2|ttf|eot)$ {
    expires 365d;
    add_header Cache-Control "public, immutable";
}

# Disable access logs for static assets
location ~* \.(jpg|jpeg|png|gif|ico|css|js)$ {
    access_log off;
}
```

3. Click "Save"

### 13.3 Enable Redis Caching

Update `.env`:

```env
CACHE_STORE=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
```

Then deploy again.

---

## 📊 Step 14: Configure Cloudflare CDN

### 14.1 Set Up Page Rules

1. In Cloudflare, go to "Rules" → "Page Rules"
2. Create rule for images:

**Rule 1: Cache Product Images**
- URL: `yourdomain.com/storage/*`
- Settings:
  - Cache Level: Cache Everything
  - Edge Cache TTL: 1 month
  - Browser Cache TTL: 1 month

**Rule 2: Cache GCS Images**
- URL: `storage.googleapis.com/YOUR_GCP_PROJECT_ID-storage/*`
- Settings:
  - Cache Level: Cache Everything
  - Edge Cache TTL: 1 year

### 14.2 Purge Cache (if needed)

Go to "Caching" → "Configuration" → "Purge Everything"

---

## ✅ Step 15: Final Verification

### 15.1 Test Your Site

1. Visit `https://yourdomain.com`
2. Check all pages load correctly:
   - Homepage
   - Category pages (/catalog/women-bags, etc.)
   - Product pages
   - Search functionality
3. Test image loading
4. Check mobile responsiveness

### 15.2 Performance Testing

Run tests:
- https://pagespeed.web.dev/ (should be 90+)
- https://gtmetrix.com/ (should be Grade A)

### 15.3 Monitor Server Health

In Forge:
- Go to "Server" → "Monitoring"
- Check CPU, RAM, Disk usage
- Set up email alerts for high usage

---

## 🔄 Daily Workflow

### Making Changes

1. Make changes locally
2. Test thoroughly
3. Commit and push:

```bash
git add .
git commit -m "Description of changes"
git push origin main
```

4. Forge auto-deploys (if Quick Deploy enabled)
5. Check site to verify changes

---

## 🆘 Troubleshooting

### Issue: 500 Error After Deploy

**Solution:**
```bash
ssh forge@YOUR_SERVER_IP
cd /home/forge/yourdomain.com
php artisan config:clear
php artisan cache:clear
php artisan optimize
```

### Issue: Images Not Loading

**Solution:**
- Check GCS service account permissions
- Verify `gcs-service-account.json` path in `.env`
- Test GCS access:

```bash
php artisan tinker
>>> Storage::disk('gcs')->files()
```

### Issue: Database Connection Error

**Solution:**
- Check `.env` DB credentials
- Verify MySQL is running: `sudo service mysql status`
- Reset database password in Forge → Database tab

### Issue: Out of Memory

**Solution:**
- Upgrade to larger server (CPX41 - 8GB RAM)
- Or enable swap:

```bash
sudo fallocate -l 2G /swapfile
sudo chmod 600 /swapfile
sudo mkswap /swapfile
sudo swapon /swapfile
```

---

## 📝 Post-Deployment Checklist

After successful deployment:

- [ ] Site loads over HTTPS
- [ ] All 25,465 products visible
- [ ] Category pages working
- [ ] Search functionality working
- [ ] Images loading from GCS
- [ ] Contact form sends emails
- [ ] Mobile layout correct
- [ ] Performance score 90+
- [ ] SSL certificate valid
- [ ] Backups enabled in Forge
- [ ] Monitoring alerts configured

---

## 💡 Next Steps & Improvements

### Optional Enhancements:

1. **Set up Redis Queue Worker**
   - For background jobs (email sending, image processing)
   - In Forge → Queue → Enable daemon

2. **Configure Backups**
   - In Forge → Backups
   - Set daily MySQL backups
   - Store on S3/DigitalOcean Spaces

3. **Add Monitoring**
   - Install Laravel Telescope (dev only)
   - Set up error tracking (Sentry, Bugsnag)
   - Use Forge monitoring

4. **Improve SEO**
   - Add meta descriptions
   - Generate sitemap.xml
   - Submit to Google Search Console

5. **Add Analytics**
   - Google Analytics 4
   - Cloudflare Web Analytics (free)

---

## 📞 Support Resources

- **Laravel Forge Docs**: https://forge.laravel.com/docs
- **Hetzner Support**: https://docs.hetzner.com/
- **Cloudflare Docs**: https://developers.cloudflare.com/
- **Laravel Docs**: https://laravel.com/docs

---

## 🎉 Congratulations!

Your HeirLuxury site is now live on production with:
- ✅ Professional server management via Forge
- ✅ Fast Hetzner VPS hosting
- ✅ 70GB images served from Google Cloud Storage
- ✅ Cloudflare CDN for global performance
- ✅ Automatic SSL certificates
- ✅ One-click deployments from Git
- ✅ ~$336/year total cost

**Your site is production-ready! 🚀**
