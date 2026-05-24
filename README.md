# Satisfaction Survey System

A professional survey platform built with Laravel, Filament, and React (Inertia.js). 
Designed for high-performance feedback initiatives with real-time insights and 
proactive monitoring.

## 🚀 Features

- **Dynamic Survey Engine**: Create and manage complex surveys with various 
  question types (Rating, Radio, Checkbox, Text, etc.).
- **Advanced Admin Panel**: Powered by Filament PHP for robust management of 
  surveys, questions, and responses.
- **Heuristic UX**: Instant-feeling navigation using Inertia prefetching and 
  Shadcn UI skeleton loaders.
- **Proactive Insights**: Automated "Detractor Alerts" notify admins 
  immediately when low satisfaction ratings are received.
- **Reliable Exports**: Queued background processing for Excel and PDF 
  reports to handle large datasets.
- **Security First**: PII log scrubbing, hardened file validation, and strict 
  data isolation.

## 🛠 Tech Stack

- **Backend**: Laravel 11+
- **Admin Panel**: Filament 3
- **Frontend**: React 19, TypeScript, Inertia.js
- **Styling**: Tailwind CSS 4, Shadcn UI
- **Testing**: Pest (Backend), Vitest + React Testing Library (Frontend)

## 📦 Installation

1. **Clone the repository**:
   ```bash
   git clone <repo-url>
   cd satisfaction-survey
   ```

2. **Install dependencies**:
   ```bash
   composer install
   npm install
   ```

3. **Environment Setup**:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Database & Migrations**:
   ```bash
   php artisan migrate --seed
   ```

5. **Start Development Server**:
   ```bash
   php artisan serve
   npm run dev
   ```

## 🧪 Testing

- **Backend**: `php artisan test` or `vendor/bin/pest`
- **Frontend**: `npm run test`

## 📄 Documentation

Detailed architecture and convention documents can be found in the `docs/` 
directory:
- [Architecture](./docs/codebase/ARCHITECTURE.md)
- [Coding Conventions](./docs/codebase/CONVENTIONS.md)
- [Project Concerns](./docs/codebase/CONCERNS.md)
