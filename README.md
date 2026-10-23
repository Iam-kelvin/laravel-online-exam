# CrazyExam

CrazyExam is a Laravel exam practice platform for timed subject-based exams. Learners can take a single-subject exam or combine multiple subjects in one attempt, while admins and moderators manage question banks, exam presets, users, and recovery flows.

## Features

- Subject-based question banks.
- Mixed-subject exam attempts with strict fallback rules when a selected subject does not have enough questions.
- Flexible question counts and durations, including 100 questions in 1 minute. A suggested 30 seconds per question never overrides the learner's chosen time.
- Single-subject Rush exams with a default 60-second timer and 15–180-second choices in 15-second steps, instant marking, locked answers, live scores and streaks, and sound effects with a remembered mute setting.
- Compact subject selection with Academic and Challenge categories, Browse all subjects, and selected-subject chips. Rush keeps Finish Rush visible on mobile, and reviews show only answered questions.
- A separate Games section: Word Scramble (72 words with clues) and Picture Puzzles (24 original SVG rebuses), with typed answers, Skip, 15–180-second timers, instant server marking and saved results.
- A short, conversational Lagos Street Knowledge bank with 72 distinct questions, including markets, route order and old Lagos.
- A five-slide homepage and dashboard banner starts with academic practice, followed by Lagos Rush, subject Rush, Word Scramble and Picture Puzzles. It supports arrows, dots, keyboard navigation, touch swipes and pausing; reduced-motion preferences disable automatic rotation. `/lagos-rush` remains a direct shortcut to the Lagos bank with an adjustable 60-second default.
- A Drop feedback card on completed exams and games that opens a prefilled community feedback form.
- Light and dark themes that follow the system setting initially and remember the learner's choice across pages.
- Timed attempts, saved answers, results, and score history.
- Admin and moderator dashboards for managing users, subjects, questions, and exam presets.
- Account access with optional email verification.
- Forgot password, password reset, profile updates, and admin/moderator-assisted email recovery.
- Responsive learner, auth, home, and admin interfaces.

## Tech Stack

- PHP 8.1+
- Laravel 10
- MySQL
- Bootstrap/Vite assets
- PHPUnit
- Vercel community PHP runtime for deployment
- Aiven MySQL for production database hosting

## Local Setup

Install PHP dependencies:

```bash
composer install
```

Install frontend dependencies:

```bash
npm install
```

Create the local environment file:

```bash
cp .env.example .env
php artisan key:generate
```

Update `.env` with your local database details, then run:

```bash
php artisan migrate --seed
```

Start the app:

```bash
php artisan serve
```

The app will usually be available at:

```text
http://127.0.0.1:8000
```

## Local Email

Local mail is configured to use the Laravel log driver by default.

For verification and password reset links, check:

```text
storage/logs/laravel.log
```

In production, replace the mail settings with a real SMTP provider.

## Testing

Run the test suite:

```bash
php artisan test
```

## Rush Sounds

Rush keeps the generated correct-answer tone. Wrong-answer clips (`.mp3`, `.wav`, `.ogg`, or `.m4a`) in `public/sounds/rush/wrong/` are discovered automatically and chosen randomly. That folder contains `faaaa.mp3`, `i-pour-you-spit.mp3`, and `shocked-sound-effect.mp3`. If a clip cannot play, Rush falls back to the generated wrong-answer tone.

The page-entry clip is `public/sounds/rush/intro/lets-have-it-lets-have-it.mp3`. Results use `public/sounds/rush/results/studio-audience-awwww-sound-fx.mp3` when more than half of the answered questions are correct. At 50% or below, including no answers, they use `public/sounds/rush/results/you-dey-go-na.mp3`. The denominator is answered questions, not the entire question bank. These clips respect the mute setting. Page-entry audio retries on the first tap if autoplay is blocked, and results have a Play reaction button. Redeploy after adding clips to a serverless deployment.

New exams use custom settings instead of duration presets. The legacy presets and their admin screen remain available for historical attempts. Run `php artisan migrate` when updating an existing installation to add the exam mode column.

## Games and Lagos Questions

Run `php artisan migrate` to add game question snapshots and refresh the Lagos bank. The refresh retires the old generated Lagos templates, preserves custom questions and keeps saved exam reviews intact. `QuestionSeeder` also uses the new bank, so reseeding does not restore the long prompts. The Lagos refresh can be repeated independently with `php artisan db:seed --class=LagosStreetKnowledgeSeeder`.

Game definitions and accepted phrase variants live in `app/Services/PuzzleBank.php`. Rebus images are local SVG assets; rebuild them with `node scripts/build-rebus.mjs` after changing the drawing definitions. Correct answers and accepted variants are kept on the server. Typed marking ignores case, punctuation and spacing; rebuses also allow optional articles (a, an, the). Skipping counts as an attempted wrong answer. Games use the existing Rush deadline, sounds, result history and attempted-only reviews.

Lagos content lives in `app/Services/LagosQuestionBank.php`; research links are recorded in `database/data/lagos-sources.json`. The questions avoid changing prices and timetables.

## Deployment

This repository includes Vercel deployment support:

- `vercel.json` routes requests through the Laravel serverless entrypoint.
- `api/index.php` boots Laravel for Vercel functions.
- `.vercelignore` excludes local secrets, logs, dependencies, and runtime files.

Set these environment variables in Vercel:

```env
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:your_generated_key
APP_URL=https://your-domain.vercel.app

LOG_CHANNEL=stderr
LOG_LEVEL=info
SESSION_DRIVER=cookie
SESSION_SECURE_COOKIE=true
CACHE_DRIVER=array
QUEUE_CONNECTION=sync
VIEW_COMPILED_PATH=/tmp

DB_CONNECTION=mysql
DB_HOST=your-aiven-host
DB_PORT=your-aiven-port
DB_DATABASE=defaultdb
DB_USERNAME=your-aiven-user
DB_PASSWORD=your-aiven-password
MYSQL_ATTR_SSL_CA_CONTENT="-----BEGIN CERTIFICATE-----\n...\n-----END CERTIFICATE-----"

MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-smtp-user
MAIL_PASSWORD=your-smtp-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=no-reply@your-domain.com
MAIL_FROM_NAME=CrazyExam
```

Run production migrations against Aiven once the database credentials are ready:

```bash
php artisan migrate --force
```

For the local `.env.aiven` connection, use `php artisan migrate:status --env=aiven` to check pending updates and `php artisan migrate --env=aiven --force` to apply them. Pushing PHP files does not update the database. The Rush `mode` column and puzzle question fields must be migrated before starting those attempts on a new deployment.

In Vercel, use the **Other** framework preset and the repository's empty build command for this PHP application. `vercel.json` already sets `framework: null`; the dashboard may show a production/project settings mismatch while its project preset remains Vite. Aligning the preset and redeploying clears that mismatch. A Laravel 500 still needs its runtime exception investigated separately.

The Vercel static-asset route includes `sounds` and `puzzles` as well as CSS, JS, fonts and images. Keep those paths ahead of the PHP catch-all so audio clips and SVG puzzles are served as files.

## Git Hygiene

Do not commit these files:

- `.env`
- `.env.*` except `.env.example`
- `vendor/`
- `node_modules/`
- Laravel logs, cache, sessions, and compiled views
- `.vercel/`

These are already covered by `.gitignore` and `.vercelignore`.
