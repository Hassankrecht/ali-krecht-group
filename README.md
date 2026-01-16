# Ali Krecht Group — Laravel Multi-purpose Website

This repository is a Laravel-based website (Blade + Vite) used to manage the public site and an admin panel for content: homepage settings (images/videos/carousels), projects, products, reviews, and translations.

The app supports multiple locales (English, Arabic, Portuguese), uses Eloquent models for Products/Projects/Reviews, and stores media files under `storage/app/public/*` with a `public/storage` symlink for public access.

---

## ✅ Key Features

- Multi-language frontend (en / ar / pt) with locale middleware
- Customizable homepage with hero (image/video/carousel), banners and theme settings
- Admin dashboard to create and manage Projects, Products, Categories, Reviews, and Home Settings
- Media uploads stored on Laravel `public` disk (`storage/app/public/home`, `projects`, `products`)
- Blade templates + Vite for assets, Bootstrap 5 for UI
- Role-based admin access and standard Laravel auth
- Eloquent models, migrations and seeders included

---

## 🧰 Tech Stack

- **Framework**: Laravel 10
- **PHP**: 8.1+
- **Frontend**: Blade, Bootstrap 5, Vite
- **Database**: MySQL (or compatible)
- **Storage**: Laravel Storage (`public` disk) with `public/storage` symlink

---

## 🚀 Quick Local Setup

1. Clone the repository and install PHP dependencies:

```powershell
git clone https://github.com/Hassankrecht/ali-krecht-group.git
cd ali-krecht-group
composer install
```

2. Install Node dependencies and build assets (for development):

```powershell
npm install
npm run dev
```

3. Copy the environment and generate app key:

```powershell
cp .env.example .env
php artisan key:generate
```

4. Configure your `.env` (database, mail, etc.) and run migrations/seeders:

```powershell
php artisan migrate --seed
```

5. Create the storage symlink so uploaded media is publicly accessible:

```powershell
php artisan storage:link
```

6. Serve the app (local development):

```powershell
php artisan serve
npm run dev
```

Open `http://127.0.0.1:8000` in your browser.

---

## Notes / Helpful Tips

- Media uploads: Admin uploads are stored in `storage/app/public/home/` and other folders; ensure `public/storage` exists and points to `storage/app/public` (run `php artisan storage:link`).
- Homepage settings support uploading video files (MP4/WebM). If a video is uploaded but not appearing, check `home_settings` in DB for the `hero_video_path` column and confirm the file exists in `storage/app/public/home/`.
- Locale: The project includes middleware to set locale by session. To add translations, edit files under `resources/lang/{locale}`.
- Performance: Large Blade templates include inline JS; consider extracting to Vite-managed files for caching and maintainability.

---

## Contributing

If you want to contribute or extend this project, please:

1. Create an issue describing the change or bug.
2. Open a feature branch from `main`.
3. Raise a PR with clear description and screenshots if applicable.

---

If you want, I can also:

- Produce a per-Blade audit (i18n, RTL, accessibility, performance)
- Add a developer-focused `CONTRIBUTING.md` or setup helper scripts
- Translate README to Arabic and Portuguese

---
